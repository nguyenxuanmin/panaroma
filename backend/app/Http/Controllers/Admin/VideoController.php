<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Project;

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
        $action = $request->action;
        $project_id = $request->project;

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
        
        $video->project_id = $project_id;
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
