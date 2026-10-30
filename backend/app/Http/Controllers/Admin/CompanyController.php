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
        $logo = $request->file('logo');
        $logoName = $logo ? $logo->getClientOriginalName() : '';
        $favicon = $request->file('favicon');
        $faviconName = $favicon ? $favicon->getClientOriginalName() : '';
        
        if (empty($name)) {
            return response()->json([
                'success' => false,
                'message' => 'The company name cannot be left blank.'
            ]);
        }

        $company = Company::find($request->id);
        
        if (!empty($logoName)) {
            $logoPath = public_path($company->logo);
            if (file_exists($logoPath) && is_file($logoPath)) {
                unlink($logoPath);
            }
            $logoUrl = 'storage/company/logo/' . time() . '_' . $logoName;
        } else {
            $logoUrl = $company->logo;
        }

        if (isset($logo)) {
            $messageError = $this->adminService->generateImage($logo,'company/logo');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }

        if (!empty($faviconName)) {
            $faviconPath = public_path($company->favicon);
            if (file_exists($faviconPath) && is_file($faviconPath)) {
                unlink($faviconPath);
            }
            $faviconUrl = 'storage/company/favicon/' . time() . '_' . $faviconName;
        } else {
            $faviconUrl = $company->favicon;
        }

        if (isset($favicon)) {
            $messageError = $this->adminService->generateImage($favicon,'company/favicon');
            if($messageError != ""){
                return response()->json([
                    'success' => false,
                    'message' => $messageError
                ]);
            }
        }
        
        $company->name = $name;
        $company->address = $address;
        $company->hotline = $hotline;
        $company->email = $email;
        $company->logo = $logoUrl;
        $company->favicon = $faviconUrl;
        $company->save();

        return response()->json([
            'success' => true,
            'message' => ""
        ]);
    }
}
