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
                'message' => 'Vui lòng nhập đầy đủ thông tin mật khẩu.'
            ]);
        }

        if (!Hash::check($passwordOld, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu cũ không chính xác.'
            ]);
        }

        if (strlen($passwordNew) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu mới phải có ít nhất 8 ký tự.'
            ]);
        }

        if ($passwordNew !== $passwordConfirm) {
            return response()->json([
                'success' => false,
                'message' => 'Xác nhận mật khẩu mới không khớp.'
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
