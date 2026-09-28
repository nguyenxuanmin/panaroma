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

    public function changePassword(){
        $titlePage = "Change Password";
        $action = "change_password";
        $project = Project::first();
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

            if (strlen($passwordNew) < 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'The new password must be at least 8 characters long.'
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
            $name = trim($request->name);
            $slug = Str::slug($name);
            $username = trim($request->user_name);
            $password = $request->password;
            $confirm = $request->confirm;
            $map = $request->map;

            if (empty($name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The project name cannot be left blank.'
                ]);
            }else{
                if($action == "edit"){
                    $existProjectName = Project::where('name',$name)->where('id', '!=', $request->id)->first();
                }else{
                    $existProjectName = Project::where('name',$name)->first();
                }

                if(isset($existProjectName)){
                    return response()->json([
                        'success' => false,
                        'message' => 'The project name already exists.'
                    ]);
                }
            }

            if (empty($username)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The username cannot be left blank.'
                ]);
            }else{
                if($action == "edit"){
                    $existUsername = Project::where('user_name',$username)->where('id', '!=', $request->id)->first();
                }else{
                    $existUsername = Project::where('user_name',$username)->first();
                }

                if(isset($existUsername)){
                    return response()->json([
                        'success' => false,
                        'message' => 'The username already exists.'
                    ]);
                }
            }

            if($action == "add"){
                if (empty($password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The password cannot be left blank.'
                    ]);
                }

                if (strlen($password) < 8) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The password must be at least 8 characters long.'
                    ]);
                }

                if ($password !== $confirm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The confirmation of the password does not match.'
                    ]);
                }

                $project = new Project();
                $project->password = Hash::make($password);
            }else{
                $project = Project::find($request->id);
            }
            
            $project->name = $name;
            $project->slug = $slug;
            $project->user_name = $username;
            $project->map = $map;
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
