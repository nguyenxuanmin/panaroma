<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function login(Request $request){
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        if (empty($user_name) || empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'The username and password must not be left blank.'
            ]);
        }

        $credentials = ['user_name' => $user_name, 'password' => $password, 'role' => 'admin'];
        if (auth()->attempt($credentials)) {
            return response()->json([
                'success' => true,
                'message' => ''
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The username or password is incorrect.'
        ]);
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function changePassword(){
        return view('admin.change-password');
    }

    public function saveChangePassword(Request $request){
        $passwordOld = $request->input('old');
        $passwordNew = $request->input('new');
        $passwordConfirm = $request->input('confirm');

        if (empty($passwordOld) || empty($passwordNew) || empty($passwordConfirm)) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill in all password information.'
            ]);
        }

        if (!Hash::check($passwordOld, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The old password is incorrect.'
            ]);
        }

        if (strlen($passwordNew) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'The new password must be at least 8 characters long.'
            ]);
        }

        if ($passwordNew !== $passwordConfirm) {
            return response()->json([
                'success' => false,
                'message' => 'The confirmation of the new password does not match.'
            ]);
        }

        $user = Auth::user();
        $user->password = Hash::make($passwordNew);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => ''
        ]);
    }
}
