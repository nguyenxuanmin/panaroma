<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Models\Floor;
use App\Models\Panorama;

class PanoramaController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }

    public function show(){
        $panoramas = Panorama::orderBy('name','asc')->paginate(20);
        return view('admin.panorama.list',[
            'panoramas' => $panoramas
        ]);
    }

    public function add(){
        $titlePage = "Create New Panorama";
        $action = "add";
        $floors = Floor::orderBy('name','asc')->get();
        return view('admin.panorama.main',[
            'titlePage' => $titlePage,
            'floors' => $floors,
            'action' => $action
        ]);
    }

    public function edit($id){
        $titlePage = "Update Panorama";
        $action = "edit";
        $panorama = Panorama::find($id);
        $floors = Floor::orderBy('name','asc')->get();
        return view('admin.panorama.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'floors' => $floors,
            'panorama' => $panorama
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $floorId = $request->floor;
        $map_x = $request->map_x;
        $map_y = $request->map_y;
        $map_angle = $request->map_angle;
        $yaw = $request->yaw;
        $pitch = $request->pitch;
        $image = $_FILES['image'] ?? null;
        $imageName = $image['name'] ?? '';
        $action = $request->action;

        if (empty($title)) {
            return response()->json([
                'success' => false,
                'message' => 'The title cannot be left blank.'
            ]);
        }

        if (empty($floorId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a panorama category.'
            ]);
        }

        if ($action == 'add' && empty($imageName)) {
            return response()->json([
                'success' => false,
                'message' => 'The image field cannot be left blank.'
            ]);
        }

        if ($map_x === null || $map_x === '') {
            return response()->json([
                'success' => false,
                'message' => 'Please select a panorama location.'
            ]);
        }

        if ($action === 'add') {
            $panorama = new Panorama();
            $imageUrl = 'storage/panoramas/' . $imageName;
        } else {
            $panorama = Panorama::find($request->id);
            
            if (!empty($imageName)) {
                if (app()->environment('local')) {
                    $imagePath = public_path($panorama->thumbnail);
                } else {
                    $imagePath = base_path('../public_html/' . $panorama->thumbnail);
                }
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath);
                }
                $imageUrl = 'storage/panoramas/' . $imageName;
            } else {
                $imageUrl = $panorama->thumbnail;
            }
        }

        if (!empty($imageName)) {
            $messageError = $this->adminService->generateImage($_FILES["image"],'panoramas');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }

        $number = Panorama::where('floor_id',$floorId)->count() + 1;
        
        $panorama->floor_id = $floorId;
        $panorama->name = $title;
        $panorama->code = $title;
        $panorama->thumbnail = $imageUrl;
        $panorama->url = $imageUrl;
        $panorama->number = $number;
        $panorama->map_x = $map_x;
        $panorama->map_y = $map_y;
        $panorama->map_angle = $map_angle;
        $panorama->default_yaw = $yaw;
        $panorama->default_pitch = $pitch;
        $panorama->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $panorama = Panorama::find($request->id);
        if (app()->environment('local')) {
            $imagePath = public_path($panorama->thumbnail);
        } else {
            $imagePath = base_path('../public_html/' . $panorama->thumbnail);
        }
        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
        $panorama->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
