<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Models\Floor;
use App\Models\Building;

class FloorController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }

    public function show(){
        $floors = Floor::orderBy('name','asc')->paginate(20);
        return view('admin.floor.list',[
            'floors' => $floors
        ]);
    }

    public function add(){
        $titlePage = "Create New Panaroma Sub-Category";
        $action = "add";
        $buildings = Building::where('type','group')->orderBy('name','asc')->get();
        return view('admin.floor.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'buildings' => $buildings
        ]);
    }

    public function edit($id){
        $titlePage = "Update Panaroma Sub-Category";
        $action = "edit";
        $floor = Floor::find($id);
        $buildings = Building::where('type','group')->orderBy('name','asc')->get();
        return view('admin.floor.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'floor' => $floor,
            'buildings' => $buildings
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $buildingId = $request->building_id;
        $image = $request->file('image');
        $imageName = $image ? $image->getClientOriginalName() : '';
        $action = $request->action;

        if (empty($title)) {
            return response()->json([
                'success' => false,
                'message' => 'The title cannot be left blank.'
            ]);
        }

        if (empty($buildingId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a group Panaroma Category.'
            ]);
        }
        $building = Building::where('id',$buildingId)->where('type','group')->first();
        if (!$building) {
            return response()->json([
                'success' => false,
                'message' => 'Selected Panaroma Category must be type group.'
            ]);
        }

        if ($action == 'add' && empty($imageName)) {
            return response()->json([
                'success' => false,
                'message' => 'The image field cannot be left blank.'
            ]);
        }

        if ($action === 'add') {
            $floor = new Floor();
            $imageUrl = 'storage/floors/' . time() . '_' . $imageName;
        } else {
            $floor = Floor::find($request->id);
            if (!empty($imageName)) {
                $imagePath = public_path($floor->plan_image);
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath);
                }
                $imageUrl = 'storage/floors/' . time() . '_' . $imageName;
            } else {
                $imageUrl = $floor->plan_image;
            }
        }

        if (isset($image)) {
            $messageError = $this->adminService->generateImage($image,'floors');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }
        
        $floor->building_id = $building->id;
        $floor->name = $title;
        $floor->plan_image = $imageUrl;
        $floor->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $floor = Floor::with('panaromas.panaromaImages')->find($request->id);
        if (!$floor) {
            return response()->json(['success'=>false,'message'=>'Floor not found.'],404);
        }
        $deleteFile = function (?string $path) {
            if (empty($path)) return;
            $full = public_path($path);
            if (file_exists($full) && is_file($full)) @unlink($full);
        };
        $deleteFile($floor->plan_image);
        foreach ($floor->panaromas as $panaroma) {
            $deleteFile($panaroma->thumbnail);
            $deleteFile($panaroma->url !== $panaroma->thumbnail ? $panaroma->url : null);
            foreach ($panaroma->panaromaImages as $img) {
                $deleteFile($img->thumbnail);
            }
        }
        $floor->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
