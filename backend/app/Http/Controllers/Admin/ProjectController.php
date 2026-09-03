<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\AdminService;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }

    public function show(){
        $projects = Project::orderBy('name','asc')->paginate(20);
        return view('admin.project.list',[
            'projects' => $projects
        ]);
    }

    public function add(){
        $titlePage = "Add Project";
        $action = "add";
        return view('admin.project.main',[
            'titlePage' => $titlePage,
            'action' => $action
        ]);
    }

    public function edit($id){
        $titlePage = "Update Project";
        $action = "edit";
        $project = Project::find($id);
        return view('admin.project.main',[
            'titlePage' => $titlePage,
            'action' => $action,
            'project' => $project
        ]);
    }

    public function changePassword($id){
        $titlePage = "Change Password";
        $action = "change_password";
        $project = Project::find($id);
        return view('admin.project.change-password',[
            'titlePage' => $titlePage,
            'action' => $action,
            'project' => $project
        ]);
    }

    public function save(Request $request){
        $action = $request->action;
        if($action == "change_password"){
            $passwordNew = $request->input('new');
            $passwordConfirm = $request->input('confirm');

            if (empty($passwordNew) || empty($passwordConfirm)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fill in all password information.'
                ]);
            }

            if ($passwordNew != $passwordConfirm) {
                return response()->json([
                    'success' => false,
                    'message' => 'The new password and confirmation password do not match.'
                ]);
            }

            $project = Project::find($request->id);
            $project->password = Hash::make($passwordNew);
            
        }else{
            $title = $request->title;
            $slug = Str::slug($title);

            if (empty($title)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The title cannot be left blank.'
                ]);
            }

            if($action == "edit"){
                $project = Project::find($request->id);
            }else{
                $project = new Project();
            }
            
            $project->name = $title;
            $project->slug = $slug;
        }

        $project->save();
        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }

    public function delete(Request $request){
        $project = project::find($request->id);
        $project->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
