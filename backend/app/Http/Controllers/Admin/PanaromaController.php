<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Models\Floor;
use App\Models\Panaroma;

class PanaromaController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }

    public function show(){
        $panaromas = Panaroma::orderBy('name','asc')->paginate(20);
        return view('admin.panaroma.list',[
            'panaromas' => $panaromas
        ]);
    }

    public function add(){
        $titlePage = "Create New Panaroma";
        $action = "add";
        $floors = Floor::orderBy('name','asc')->get();
        return view('admin.panaroma.main',[
            'titlePage' => $titlePage,
            'floors' => $floors,
            'action' => $action
        ]);
    }

    public function edit($id){
        $titlePage = "Update Panaroma";
        $action = "edit";
        $panaroma = Panaroma::find($id);
        $floors = Floor::orderBy('name','asc')->get();
        return view('admin.panaroma.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'floors' => $floors,
            'panaroma' => $panaroma
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
                'message' => 'Please select a panaroma category.'
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
                'message' => 'Please select a panaroma location.'
            ]);
        }

        if ($action === 'add') {
            $panaroma = new Panaroma();
            $imageUrl = 'storage/panaromas/' . $imageName;
        } else {
            $panaroma = Panaroma::find($request->id);
            
            if (!empty($imageName)) {
                if (app()->environment('local')) {
                    $imagePath = public_path($panaroma->thumbnail);
                } else {
                    $imagePath = base_path('../public_html/' . $panaroma->thumbnail);
                }
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath);
                }
                $imageUrl = 'storage/panaromas/' . $imageName;
            } else {
                $imageUrl = $panaroma->thumbnail;
            }
        }

        if (!empty($imageName)) {
            $messageError = $this->adminService->generateImage($_FILES["image"],'panaromas');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }

        $number = Panaroma::where('floor_id',$floorId)->count() + 1;
        
        $panaroma->floor_id = $floorId;
        $panaroma->name = $title;
        $panaroma->code = $title;
        $panaroma->thumbnail = $imageUrl;
        $panaroma->url = $imageUrl;
        $panaroma->number = $number;
        $panaroma->map_x = $map_x;
        $panaroma->map_y = $map_y;
        $panaroma->map_angle = $map_angle;
        $panaroma->default_yaw = $yaw;
        $panaroma->default_pitch = $pitch;
        $panaroma->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $panaroma = Panaroma::find($request->id);
        if (app()->environment('local')) {
            $imagePath = public_path($panaroma->thumbnail);
        } else {
            $imagePath = base_path('../public_html/' . $panaroma->thumbnail);
        }
        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
        $panaroma->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
