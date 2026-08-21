<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Support\Facades\Hash;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }
    
    public function index(){
        return view('system');
    }

    public function save(Request $request){
        $name = $request->name;
        $address = $request->address;
        $hotline = $request->hotline;
        $email = $request->email;
        if (isset($_FILES["logo"])) {
            $logo = $_FILES["logo"]["name"];
        }else{
            $logo = "";
        }
        if (isset($_FILES["favicon"])) {
            $favicon = $_FILES["favicon"]["name"];
        }else{
            $favicon = "";
        }
        
        if (empty($name)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên công ty không được để trống.'
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => $email. ' không phải là email hợp lệ.',
            ]);
        }

        if (!empty($logo)) {
            $messageError = $this->adminService->generateImage($_FILES["logo"],'company/logo');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }

        if (!empty($favicon)) {
            $messageError = $this->adminService->generateImage($_FILES["favicon"],'company/favicon');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }

        $nameAdmin = $request->nameAdmin;
        $userNameAdmin = $request->userNameAdmin;
        $emailAdmin = $request->emailAdmin;
        $passWordAdmin = $request->passWordAdmin;

        if (empty($nameAdmin) || empty($userNameAdmin) || empty($emailAdmin) || empty($passWordAdmin)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên, tên đăng nhập, email và mật khẩu không được để trống.',
            ]);
        }
        if (!filter_var($emailAdmin, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => $emailAdmin. ' không phải là email hợp lệ.',
            ]);
        }
        if (strlen($passWordAdmin) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            ]);
        }
        
        $company = new Company();
        $company->name = $name;
        $company->address = $address;
        $company->hotline = $hotline;
        $company->email = $email;
        $company->logo = $logo;
        $company->favicon = $favicon;
        $company->save();

        $user = new User();
        $user->name = $nameAdmin;
        $user->user_name = $userNameAdmin;
        $user->email = $emailAdmin;
        $user->role = 'admin';
        $user->password = Hash::make($passWordAdmin);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }
}
