<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Services\AdminService;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->adminService = new AdminService;
    }

    public function show(){
        $company = Company::first();
        return view('admin.company.main',[
            'company' => $company
        ]);
    }

    public function save(Request $request){
        $name = $request->name;
        $address = $request->address;
        $hotline = $request->hotline;
        $email = $request->email;
        $map = $request->map;
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

        $company = Company::find($request->id);

        if (!empty($logo)) {
            if (app()->environment('local')) {
                $imagePath = public_path('storage/company/logo/' . $company->image);
            } else {
                $imagePath = base_path('../public_html/storage/company/logo/' . $company->logo);
            }
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $messageError = $this->adminService->generateImage($_FILES["logo"],'company/logo');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }else{
            $logo = $company->logo;
        }

        if (!empty($favicon)) {
            if (app()->environment('local')) {
                $imagePath = public_path('storage/company/favicon/' . $company->favicon);
            } else {
                $imagePath = base_path('../public_html/storage/company/favicon/' . $company->favicon);
            }
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $messageError = $this->adminService->generateImage($_FILES["favicon"],'company/favicon');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }else{
            $favicon = $company->favicon;
        }
        
        $company->name = $name;
        $company->address = $address;
        $company->hotline = $hotline;
        $company->email = $email;
        $company->map = $map;
        $company->logo = $logo;
        $company->favicon = $favicon;
        $company->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }
}
