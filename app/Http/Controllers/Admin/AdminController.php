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
                'message' => 'Tên đăng nhập và mật khẩu không được để trống.'
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
            'message' => 'Thông tin đăng nhập không chính xác.'
        ]);
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }
}
