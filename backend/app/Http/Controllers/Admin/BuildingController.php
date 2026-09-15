<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Models\Building;
 
class BuildingController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }

    public function show(){
        $buildings = Building::orderBy('name','asc')->paginate(20);
        return view('admin.building.list',[
            'buildings' => $buildings
        ]);
    }

    public function add(){
        $titlePage = "Create New Panaroma Category";
        $action = "add";
        return view('admin.building.main',[
            'titlePage' => $titlePage,
            'action' => $action
        ]);
    }

    public function edit($id){
        $titlePage = "Update Panaroma Category";
        $action = "edit";
        $building = Building::find($id);
        return view('admin.building.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'building' => $building
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $type = $request->type ?? 'single';
        if (!in_array($type, ['single', 'group'])) $type = 'single';
        $image = $request->file('image');
        $imageName = $image ? $image->getClientOriginalName() : '';
        $action = $request->action;

        if (empty($title)) {
            return response()->json([
                'success' => false,
                'message' => 'The title cannot be left blank.'
            ]);
        }

        if ($type === 'single' && $action == 'add' && empty($imageName)) {
            return response()->json([
                'success' => false,
                'message' => 'The image field cannot be left blank for single building.'
            ]);
        }

        $imageUrl = null;
        if ($action === 'add') {
            $building = new Building();
            if ($type === 'single') {
                $imageUrl = 'storage/buildings/' . time() . '_' . $imageName;
            }
        } else {
            $building = Building::find($request->id);
            if (!$building) {
                return response()->json(['success'=>false,'message'=>'Building not found.'],404);
            }
            if ($type === 'group') {
                if (!empty($building->plan_image)) {
                    $old = app()->environment('local') ? public_path($building->plan_image) : base_path('../public_html/' . $building->plan_image);
                    if (file_exists($old) && is_file($old)) @unlink($old);
                }
                $imageUrl = null;
            } else {
                if (!empty($imageName)) {
                    if (!empty($building->plan_image)) {
                        $old = app()->environment('local') ? public_path($building->plan_image) : base_path('../public_html/' . $building->plan_image);
                        if (file_exists($old) && is_file($old)) @unlink($old);
                    }
                    $imageUrl = 'storage/buildings/' . time() . '_' . $imageName;
                } else {
                    $imageUrl = $building->plan_image;
                }
            }
        }

        if ($type === 'single' && isset($image)) {
            $messageError = $this->adminService->generateImage($image,'buildings');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }
        
        $building->project_id = 1;
        $building->name = $title;
        $building->type = $type;
        $building->plan_image = $imageUrl;
        $building->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $building = Building::with(['floors.panaromas.panaromaImages', 'panaromas.panaromaImages'])->find($request->id);
        if (!$building) {
            return response()->json(['success'=>false,'message'=>'Building not found.'],404);
        }

        $deleteFile = function (?string $path) {
            if (empty($path)) return;
            $full = app()->environment('local') ? public_path($path) : base_path('../public_html/' . $path);
            if (file_exists($full) && is_file($full)) {
                @unlink($full);
            }
        };

        $deleteFile($building->plan_image);

        foreach ($building->panaromas as $panaroma) {
            $deleteFile($panaroma->thumbnail);
            $deleteFile($panaroma->url !== $panaroma->thumbnail ? $panaroma->url : null);
            foreach ($panaroma->panaromaImages as $img) {
                $deleteFile($img->thumbnail);
            }
        }

        foreach ($building->floors as $floor) {
            $deleteFile($floor->plan_image);
            foreach ($floor->panaromas as $panaroma) {
                $deleteFile($panaroma->thumbnail);
                $deleteFile($panaroma->url !== $panaroma->thumbnail ? $panaroma->url : null);
                foreach ($panaroma->panaromaImages as $img) {
                    $deleteFile($img->thumbnail);
                }
            }
        }

        $building->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
