<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Services\AdminService;
use App\Models\Project;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }
    
    public function show(){
        $allowedSorts = [
            'title'   => 'videos.title',
            'project' => 'projects_name',
        ];
        $dir = strtolower(request()->query('dir')) === 'desc' ? 'desc' : 'asc';
        $sortKey = array_key_exists(request()->query('sort'), $allowedSorts) ? request()->query('sort') : 'title';

        $videos = Video::query()
            ->leftJoin('projects', 'videos.project_id', '=', 'projects.id')
            ->select('videos.*', 'projects.name as projects_name')
            ->with('project')
            ->orderByRaw($allowedSorts[$sortKey] . ' ' . $dir . ', videos.id asc')
            ->paginate(20);
        $videos->appends(request()->query());
        return view('admin.video.list',[
            'videos' => $videos
        ]);
    }

    public function add(){
        $titlePage = "Create New Video";
        $action = "add";
        $projects = Project::orderBy('name','asc')->get();
        return view('admin.video.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'projects' => $projects
        ]);
    }

    public function edit($id){
        $titlePage = "Update Video";
        $action = "edit";
        $video = Video::find($id);
        $projects = Project::orderBy('name','asc')->get();
        return view('admin.video.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'video' => $video,
            'projects' => $projects
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $link = $request->link;
        $videoFile = $request->file('video_file');
        $action = $request->action;
        $project_id = $request->project;

        if (!isset($project_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a project.'
            ]);
        }

        if (empty($link) && empty($videoFile)) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload a video file.'
            ]);
        }

        if ($action === 'add') {
            $video = new Video();
        } else {
            $video = Video::find($request->id);
        }

        // Ưu tiên file upload nếu có; ngược lại dùng link (YouTube)
        $filePath = null;
        if (!empty($videoFile)) {
            $messageError = $this->adminService->generateVideo($videoFile, 'videos');
            if ($messageError != "") {
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
            $extension = strtolower($videoFile->getClientOriginalExtension());
            if (empty($extension)) {
                $extension = strtolower(pathinfo($videoFile->getClientOriginalName(), PATHINFO_EXTENSION));
            }
            $filePath = 'storage/videos/' . time() . '_' . basename($videoFile->getClientOriginalName());
            // Xóa file video cũ nếu trước đó là file upload
            if (!empty($video->link) && preg_match('#^storage/(videos|panaromas)/#', $video->link) && file_exists(public_path($video->link)) && is_file(public_path($video->link))) {
                unlink(public_path($video->link));
            }
        }

        $video->project_id = $project_id;
        $video->title = $title;
        $video->link = $filePath ?: $link;
        $video->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $video = Video::find($request->id);
        if ($video && !empty($video->link) && !str_starts_with($video->link, 'http') && file_exists(public_path($video->link)) && is_file(public_path($video->link))) {
            unlink(public_path($video->link));
        }
        $video?->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
