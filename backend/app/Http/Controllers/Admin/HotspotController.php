<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Panaroma;
use App\Models\Hotspot;

class HotspotController extends Controller
{
    public function show(){
        $allowedSorts = [
            'title'    => 'hotspots.title',
            'panaroma' => 'p1_name',
            'target'   => 'p2_name',
            'created'  => 'hotspots.created_at',
        ];
        $dir = strtolower(request()->query('dir')) === 'desc' ? 'desc' : 'asc';
        $sortKey = array_key_exists(request()->query('sort'), $allowedSorts) ? request()->query('sort') : 'title';

        $hotspots = Hotspot::query()
            ->leftJoin('panaromas as p1', 'hotspots.panaroma_id', '=', 'p1.id')
            ->leftJoin('panaromas as p2', 'hotspots.target_panaroma_id', '=', 'p2.id')
            ->select('hotspots.*', 'p1.name as p1_name', 'p2.name as p2_name')
            ->with(['panaroma', 'targetPanaroma'])
            ->orderByRaw($allowedSorts[$sortKey] . ' ' . $dir . ', hotspots.id asc')
            ->paginate(20);
        $hotspots->appends(request()->query());
        return view('admin.hotspot.list',[
            'hotspots' => $hotspots
        ]);
    }

    public function add(){
        $titlePage = "Create New Hotspot";
        $action = "add";
        $panaromas = Panaroma::orderBy('name','asc')->get();
        $targetPanaromas = Panaroma::orderBy('name','asc')->get();
        return view('admin.hotspot.main',[
            'titlePage' => $titlePage,
            'panaromas' => $panaromas,
            'action' => $action,
            'targetPanaromas' => $targetPanaromas
        ]);
    }

    public function edit($id){
        $titlePage = "Update Hotspot";
        $action = "edit";
        $hotspot = Hotspot::find($id);
        $panaromas = Panaroma::orderBy('name','asc')->get();
        $targetPanaromas = Panaroma::orderBy('name','asc')->get();
        return view('admin.hotspot.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'panaromas' => $panaromas,
            'targetPanaromas' => $targetPanaromas,
            'hotspot' => $hotspot
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $panaromaId = $request->panaroma;
        $targetPanaromaId = $request->targetPanaroma;
        $yaw = $request->yaw;
        $pitch = $request->pitch;
        $action = $request->action;

        if (empty($title)) {
            return response()->json([
                'success' => false,
                'message' => 'The title cannot be left blank.'
            ]);
        }

        if (empty($panaromaId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a panaroma.'
            ]);
        }

        if (empty($targetPanaromaId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a target panaroma.'
            ]);
        }

        if ($yaw === null || $yaw === '') {
            return response()->json([
                'success' => false,
                'message' => 'Please select a hotspot location.'
            ]);
        }

        if ($action === 'add') {
            $hotspot = new Hotspot();
        } else {
            $hotspot = Hotspot::find($request->id);
            if (!$hotspot) {
                return response()->json(['success'=>false,'message'=>'Hotspot not found.'],404);
            }
        }
        
        $hotspot->panaroma_id = $panaromaId;
        $hotspot->target_panaroma_id = $targetPanaromaId;
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
        $hotspot = Hotspot::find($request->id);
        if (!$hotspot) {
            return response()->json(['success'=>false,'message'=>'Hotspot not found.'],404);
        }
        $hotspot->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
