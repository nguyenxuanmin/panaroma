<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Models\Floor;
use App\Models\Panaroma;
use App\Models\PanaromaImage;

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
        $panaroma = Panaroma::with('panaromaImages')->find($id);
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
        $image = $request->file('image');
        $imageName = $image ? $image->getClientOriginalName() : '';
        $panaromaImages = $request->file('panaromaImages');
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
            $imageUrl = 'storage/panaromas/' . time() . '_' . $imageName;
            $number = Panaroma::where('floor_id',$floorId)->count() + 1;
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
                $imageUrl = 'storage/panaromas/' . time() . '_' . $imageName;
            } else {
                $imageUrl = $panaroma->thumbnail;
            }
            $number = $panaroma->number;
        }

        if (isset($image)) {
            $messageError = $this->adminService->generateImage($image,'panaromas');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }

        if (isset($panaromaImages)) {
            foreach ($panaromaImages as $panaromaImage) {
                $messageError = $this->adminService->generateImage($panaromaImage,'panaroma-images');
                if($messageError != ""){
                    return response()->json([
                        'success' => false,
                        'message' => $messageError
                    ]);
                }
            }
        }

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

        if (isset($panaromaImages)) {
            foreach ($panaromaImages as $panaromaImage) {
                $panaromaImageName = time() . '_' . $panaromaImage->getClientOriginalName();
                $panaromaImageUrl = 'storage/panaroma-images/' . $panaromaImageName;
                $filepanaromaImage = new PanaromaImage();
                $filepanaromaImage->title = $panaromaImageName;
                $filepanaromaImage->thumbnail = $panaromaImageUrl;
                $filepanaromaImage->panaroma_id = $panaroma->id;
                $filepanaromaImage->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $panaroma = Panaroma::with('panaromaImages')->find($request->id);
        if (app()->environment('local')) {
            $imagePath = public_path($panaroma->thumbnail);
        } else {
            $imagePath = base_path('../public_html/' . $panaroma->thumbnail);
        }
        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
        foreach ($panaroma->panaromaImages as $panaromaImage) {
            if (app()->environment('local')) {
                $imagePathpanaroma = public_path($panaromaImage->thumbnail);
            } else {
                $imagePathpanaroma = base_path('../public_html/' . $panaromaImage->thumbnail);
            }
            if (file_exists($imagePathpanaroma) && is_file($imagePathpanaroma)) {
                unlink($imagePathpanaroma);
            }
        }
        $panaroma->delete();
        return response()->json([
            'success' => true
        ]);
    }

    public function deletePanaromaImage(Request $request){
        $panaromaImage = PanaromaImage::find($request->id);
        if (app()->environment('local')) {
            $imagePath = public_path($panaromaImage->thumbnail);
        } else {
            $imagePath = base_path('../public_html/' . $panaromaImage->thumbnail);
        }
        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
        $panaromaImage->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
