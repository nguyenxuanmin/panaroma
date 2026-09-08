<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;

class VideoController extends Controller
{
    public function show(){
        $videos = Video::orderBy('title','asc')->paginate(20);
        return view('admin.video.list',[
            'videos' => $videos
        ]);
    }

    public function add(){
        $titlePage = "Create New Video";
        $action = "add";
        return view('admin.video.main',[
            'titlePage' => $titlePage,
            'action' => $action
        ]);
    }

    public function edit($id){
        $titlePage = "Update Video";
        $action = "edit";
        $video = Video::find($id);
        return view('admin.video.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'video' => $video
        ]);
    }

    public function save(Request $request){
        $title = $request->title;
        $link = $request->link;
        $action = $request->action;

        if (empty($link)) {
            return response()->json([
                'success' => false,
                'message' => 'The link cannot be left blank.'
            ]);
        }

        if ($action === 'add') {
            $video = new Video();
        } else {
            $video = Video::find($request->id);
        }
        
        $video->project_id = 1;
        $video->title = $title;
        $video->link = $link;
        $video->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $video = Video::find($request->id);
        $video->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
