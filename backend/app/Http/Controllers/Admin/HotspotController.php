<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Panorama;
use App\Models\Hotspot;

class HotspotController extends Controller
{
    public function show(){
        $hotspots = Hotspot::orderBy('title','asc')->paginate(20);
        return view('admin.hotspot.list',[
            'hotspots' => $hotspots
        ]);
    }

    public function add(){
        $titlePage = "Create New Hotspot";
        $action = "add";
        $panoramas = Panorama::orderBy('name','asc')->get();
        $targetPanoramas = Panorama::orderBy('name','asc')->get();
        return view('admin.hotspot.main',[
            'titlePage' => $titlePage,
            'panoramas' => $panoramas,
            'action' => $action,
            'targetPanoramas' => $targetPanoramas
        ]);
    }

    public function edit($id){
        $titlePage = "Update Hotspot";
        $action = "edit";
        $hotspot = Hotspot::find($id);
        $panoramas = Panorama::orderBy('name','asc')->get();
        $targetPanoramas = Panorama::orderBy('name','asc')->get();
        return view('admin.hotspot.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'panoramas' => $panoramas,
            'targetPanoramas' => $targetPanoramas,
            'hotspot' => $hotspot
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $panoramaId = $request->panorama;
        $targetPanoramaId = $request->targetPanorama;
        $yaw = $request->yaw;
        $pitch = $request->pitch;
        $action = $request->action;

        if (empty($title)) {
            return response()->json([
                'success' => false,
                'message' => 'The title cannot be left blank.'
            ]);
        }

        if (empty($panoramaId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a panorama.'
            ]);
        }

        if (empty($targetPanoramaId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a target panorama.'
            ]);
        }

        if ($yaw === null || $yaw === '') {
            return response()->json([
                'success' => false,
                'message' => 'Please select a hotspot location.'
            ]);
        }

        if ($action === 'add') {
            $hotspot = new hotspot();
        } else {
            $hotspot = hotspot::find($request->id);
        }
        
        $hotspot->panorama_id = $panoramaId;
        $hotspot->target_panorama_id = $targetPanoramaId;
        $hotspot->title = $title;
        $hotspot->yaw = $yaw;
        $hotspot->pitch = $pitch;
        $hotspot->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $hotspot = hotspot::find($request->id);
        $hotspot->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
