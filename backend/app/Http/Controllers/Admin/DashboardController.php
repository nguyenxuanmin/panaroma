<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Panaroma;

class DashboardController extends Controller
{
    public function index(){
        $countProject = Project::count();
        $countBuilding = Building::count();
        $countFloor = Floor::count();
        $countPanaroma = Panaroma::count();
        return view('admin.dashboard', [
            'countProject' => $countProject,
            'countBuilding' => $countBuilding,
            'countFloor' => $countFloor,
            'countPanaroma' => $countPanaroma
        ]);
    }
}
