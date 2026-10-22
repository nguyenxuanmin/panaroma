<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Panaroma;

class DashboardController extends Controller
{
    public function index(){
        $countProject = Project::count();
        $countBuilding = Building::count();
        $countFloor = Floor::count();
        $countPanaroma = Panaroma::count();
        return view('admin.dashboard', [
            'countProject' => $countProject,
            'countBuilding' => $countBuilding,
            'countFloor' => $countFloor,
            'countPanaroma' => $countPanaroma
        ]);
    }
    /**
     * Map folder trên đĩa -> bảng/cột trong database.
     * nameOnly: DB chỉ lưu tên file (path đầy đủ = storage/<folder>/<name>)
     * isVideo: folder chứa video thay vì ảnh
     */
    private function assetMap(): array
    {
        return [
            ['folder' => 'buildings', 'table' => 'buildings', 'columns' => ['plan_image'], 'nameOnly' => false, 'isVideo' => false],
            ['folder' => 'floors', 'table' => 'floors', 'columns' => ['plan_image'], 'nameOnly' => false, 'isVideo' => false],
            ['folder' => 'panaromas', 'table' => 'panaromas', 'columns' => ['thumbnail', 'url'], 'nameOnly' => false, 'isVideo' => false],
            ['folder' => 'panaroma-images', 'table' => 'panaroma_images', 'columns' => ['thumbnail'], 'nameOnly' => false, 'isVideo' => false],
            ['folder' => 'videos', 'table' => 'videos', 'columns' => ['link'], 'nameOnly' => false, 'isVideo' => true],
            ['folder' => 'company/logo', 'table' => 'companies', 'columns' => ['logo'], 'nameOnly' => true, 'isVideo' => false],
            ['folder' => 'company/favicon', 'table' => 'companies', 'columns' => ['favicon'], 'nameOnly' => true, 'isVideo' => false],
        ];
    }

    /** Tập các file đang được database tham chiếu: key = '<folder>/<filename>' */
    private function referencedFiles(): array
    {
        $referenced = [];
        foreach ($this->assetMap() as $map) {
            $rows = DB::table($map['table'])->select($map['columns'])->get();
            foreach ($rows as $row) {
                foreach ($map['columns'] as $column) {
                    $value = $row->{$column} ?? null;
                    if (empty($value) || str_starts_with($value, 'http') || str_starts_with($value, '//')) {
                        continue; // link ngoài, hoặc rỗng
                    }
                    // chuỗi lưu trong DB: 'storage/<folder>/<file>' hoặc chỉ '<name>' (nameOnly)
                    $referenced[$map['folder'] . '/' . basename($value)] = true;
                }
            }
        }
        return $referenced;
    }

    /** Lấy manifest mọi file trên đĩa nằm trong whitelist folder */
    private function scanDisk(): array
    {
        $files = [];
        foreach ($this->assetMap() as $map) {
            $dir = public_path('storage/' . $map['folder']);
            if (!File::isDirectory($dir)) continue;
            $extensions = $map['isVideo'] ? ['mp4', 'webm', 'ogg', 'm4v', 'mov'] : ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            foreach (File::files($dir) as $file) {
                if (!in_array(strtolower($file->getExtension()), $extensions)) continue;
                $rel = $map['folder'] . '/' . $file->getFilename();
                $files[$rel] = $map;
            }
        }
        return $files;
    }

    /** Danh sách file trên đĩa KHÔNG có trong database (orphan) */
    private function orphanFiles(): array
    {
        $referenced = $this->referencedFiles();
        $orphans = [];
        foreach ($this->scanDisk() as $rel => $map) {
            if (isset($referenced[$rel])) continue;
            $fullPath = public_path('storage/' . $rel);
            $orphans[] = [
                'path' => 'storage/' . $rel,      // dùng cho form delete
                'folder' => $map['folder'],
                'name' => basename($rel),
                'size' => filesize($fullPath),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
            ];
        }
        usort($orphans, fn($a, $b) => strcmp($a['folder'], $b['folder']) ?: strcmp($a['name'], $b['name']));
        return $orphans;
    }

    public function showFiles()
    {
        $orphans = $this->orphanFiles();
        $totalSize = array_sum(array_column($orphans, 'size'));
        return view('admin.files.list', [
            'orphans' => $orphans,
            'totalSize' => $totalSize,
        ]);
    }

    /**
     * Xóa 1 file orphan (mode=single, kèm path) hoặc tất cả orphan vừa quét (mode=all).
     * Mỗi lần xóa đều re-check: file phải nằm trong whitelist folder và KHÔNG được DB tham chiếu.
     */
    public function deleteOrphanFile(Request $request)
    {
        $path = $request->input('path');
        $mode = $request->input('mode', 'single'); // single | all

        if ($mode === 'all') {
            $deleted = 0;
            $freed = 0;
            foreach ($this->orphanFiles() as $file) {
                $result = $this->deleteOne($file['path']);
                if ($result === true) { $deleted++; $freed += $file['size']; }
            }
            return response()->json([
                'success' => true,
                'message' => "Deleted {$deleted} file(s), freed " . round($freed / 1048576, 2) . " MB",
            ]);
        }

        $result = $this->deleteOne($path);
        if ($result !== true) {
            return response()->json(['success' => false, 'message' => $result]);
        }
        return response()->json(['success' => true, 'message' => '']);
    }

    /** @return true|string true nếu xóa ok, string là thông báo lỗi */
    private function deleteOne($path)
    {
        // path chỉ chấp nhận dạng 'storage/<folder>/.<name>' - folder trong whitelist bên dưới
        if (!is_string($path) || !preg_match('#^storage/[A-Za-z0-9\-_/]+/[^/]+\.[a-z0-9]{2,5}$#i', $path)) {
            return 'Invalid file path.';
        }
        $rel = Str::after($path, 'storage/');
        $allowed = array_map(fn($m) => $m['folder'], $this->assetMap());
        $matchedFolder = null;
        foreach ($allowed as $allowedFolder) {
            if (str_starts_with($rel, $allowedFolder . '/')) { $matchedFolder = $allowedFolder; break; }
        }
        if ($matchedFolder === null) {
            return 'File is out of managed folders.';
        }
        $fullPath = public_path($path);
        if (!file_exists($fullPath) || !is_file($fullPath)) {
            return 'File not found.';
        }
        // re-check chắc chắn file này đang không được DB nào tham chiếu (tên file kết thúc trong DB)
        $name = basename($rel);
        $map = collect($this->assetMap())->first(fn($m) => $m['folder'] === $matchedFolder);
        $stillReferenced = DB::table($map['table'])
            ->where(function ($query) use ($map, $name) {
                foreach ($map['columns'] as $column) {
                    $query->orWhere($column, 'like', '%' . str_replace('_', '\\_', $name));
                }
            })
            ->exists();
        if ($stillReferenced) {
            return 'File is still referenced in database, cannot delete.';
        }
        if (!@unlink($fullPath)) {
            return 'Cannot delete the file (permission denied?).';
        }
        return true;
    }
}
