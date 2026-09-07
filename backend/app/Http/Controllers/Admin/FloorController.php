<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Models\Floor;

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
        $titlePage = "Create New Panaroma Category";
        $action = "add";
        return view('admin.floor.main',[
            'titlePage' => $titlePage,
            'action' => $action
        ]);
    }

    public function edit($id){
        $titlePage = "Update Panaroma Category";
        $action = "edit";
        $floor = Floor::find($id);
        return view('admin.floor.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'floor' => $floor
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $image = $_FILES['image'] ?? null;
        $imageName = $image['name'] ?? '';
        $action = $request->action;

        if (empty($title)) {
            return response()->json([
                'success' => false,
                'message' => 'The title cannot be left blank.'
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
            $imageUrl = 'storage/floors/' . $imageName;
        } else {
            $floor = Floor::find($request->id);
            if (!empty($imageName)) {
                if (app()->environment('local')) {
                    $imagePath = public_path($floor->plan_image);
                } else {
                    $imagePath = base_path('../public_html/' . $floor->plan_image);
                }
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath);
                }
                $imageUrl = 'storage/floors/' . $imageName;
            } else {
                $imageUrl = $floor->plan_image;
            }
        }

        if (isset($image)) {
            $messageError = $this->adminService->generateImage($_FILES["image"],'floors');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }
        
        $floor->project_id = 1;
        $floor->name = $title;
        $floor->plan_image = $imageUrl;
        $floor->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $floor = Floor::with('panaromas')->find($request->id);
        if (app()->environment('local')) {
            $imagePath = public_path($floor->plan_image);
        } else {
            $imagePath = base_path('../public_html/' . $floor->plan_image);
        }
        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
        foreach ($floor->panaromas as $key => $panaroma) {
            if (app()->environment('local')) {
                $imagePathpanaroma = public_path($panaroma->thumbnail);
            } else {
                $imagePathpanaroma = base_path('../public_html/' . $panaroma->thumbnail);
            }
            if (file_exists($imagePathpanaroma) && is_file($imagePathpanaroma)) {
                unlink($imagePathpanaroma);
            }
        }
        $floor->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
