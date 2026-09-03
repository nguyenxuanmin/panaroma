<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Company;

// Helper: normalize storage path to /storage/...
if (!function_exists('normalizeStorageUrl')) {
    function normalizeStorageUrl(?string $path): ?string {
        if (!$path) return null;
        // already absolute url
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }
        // ensure leading slash
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        // if path already starts with /storage, keep
        // if path like /images/... keep as is (public assets fallback)
        return $path;
    }
}

function mapPanoramaForApi($panorama) {
    return [
        'id' => (string) $panorama->id,
        'name' => $panorama->name,
        'code' => $panorama->code,
        'number' => (int) $panorama->number,
        'thumbnail' => normalizeStorageUrl($panorama->thumbnail),
        'url' => normalizeStorageUrl($panorama->url ?? $panorama->thumbnail),
        'mapPosition' => [
            'x' => (float) ($panorama->map_x ?? 0),
            'y' => (float) ($panorama->map_y ?? 0),
            'angle' => (float) ($panorama->map_angle ?? 0),
        ],
        'defaultView' => [
            'yaw' => (float) ($panorama->default_yaw ?? 0),
            'pitch' => (float) ($panorama->default_pitch ?? 0),
        ],
        'hotspots' => $panorama->hotspots->map(function ($hotspot) {
            return [
                'id' => (string) $hotspot->id,
                'yaw' => (float) $hotspot->yaw,
                'pitch' => (float) $hotspot->pitch,
                'tooltip' => $hotspot->title,
                'targetPanorama' => (string) $hotspot->target_panorama_id,
            ];
        })->values(),
    ];
}

function mapProjectToFrontend($project) {
    $floors = $project->floors()->with('panoramas.hotspots')->orderBy('id')->get();

    // Each floor becomes a building of type "single" for frontend
    // This keeps sidebar rendering working without needing a buildings table
    $buildings = $floors->map(function ($floor) {
        return [
            'id' => (string) $floor->id,
            'name' => $floor->name ?? ('Floor ' . $floor->id),
            'type' => 'single',
            'shortLabel' => $floor->short_label,
            'description' => $floor->description,
            'planImage' => normalizeStorageUrl($floor->plan_image),
            'defaultPanoramaId' => $floor->panoramas->first()?->id ? (string) $floor->panoramas->first()->id : null,
            'videos' => [],
            'panoramas' => $floor->panoramas->map(fn($p) => mapPanoramaForApi($p))->values(),
        ];
    })->values();

    return [
        'id' => $project->slug ?: (string) $project->id,
        'name' => $project->name ?? ('Project ' . $project->id),
        'slug' => $project->slug,
        'buildings' => $buildings,
    ];
}

// Health check - frontend useApi health()
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
});

// Site settings - frontend useSiteSettings.jsx
Route::get('/site-settings', function () {
    $company = Company::first();
    if (!$company) {
        return response()->json([
            'company_name' => config('app.name'),
            'logo_url' => null,
            'favicon_url' => null,
        ]);
    }
    return response()->json([
        'company_name' => $company->name,
        'address' => $company->address ?? null,
        'hotline' => $company->hotline ?? null,
        'email' => $company->email ?? null,
        'logo_url' => normalizeStorageUrl($company->logo),
        'favicon_url' => normalizeStorageUrl($company->favicon),
        // aliases for frontend compat
        'company_name' => $company->name,
    ]);
});

// Auth for frontend LoginScreen (uses Project table as account)
Route::post('/auth/login', function (Request $request) {
    $id = $request->input('id');
    $password = $request->input('password');

    if (!$id || !$password) {
        return response()->json(['message' => 'Vui lòng nhập ID và Password'], 422);
    }

    // Try finding project by user_name or slug or name
    $project = Project::where('user_name', $id)
        ->orWhere('slug', $id)
        ->orWhere('name', $id)
        ->first();

    // fallback: if no project with password, allow any project if password matches hashed?
    // If still null, try by id
    if (!$project && is_numeric($id)) {
        $project = Project::find($id);
    }

    if (!$project) {
        return response()->json(['message' => 'ID không tồn tại'], 401);
    }

    // If project has no password (nullable), allow any password for demo
    if ($project->password && !Hash::check($password, $project->password)) {
        return response()->json(['message' => 'Password không đúng'], 401);
    }

    $user = [
        'id' => $project->slug ?: (string) $project->id,
        'name' => $project->name,
        'slug' => $project->slug,
        'project_id' => $project->id,
    ];

    // Store in session & also return
    session(['pano_user' => $user]);
    // also store in cache-like via session cookie

    return response()->json(['user' => $user, 'message' => 'Login success']);
});

Route::get('/auth/me', function (Request $request) {
    $user = session('pano_user');
    if ($user) {
        return response()->json(['user' => $user]);
    }
    // also allow header fallback via localStorage - if session lost, try to rehydrate from DB via query param? 
    // For API stateless fallback, check if Authorization Bearer contains project slug
    return response()->json(['message' => 'Unauthenticated'], 401);
});

Route::post('/auth/logout', function () {
    session()->forget('pano_user');
    return response()->json(['message' => 'Logged out']);
});

// Projects - main endpoint for frontend useProjects hook
Route::get('/projects', function () {
    $projects = Project::with('floors.panoramas.hotspots')->orderBy('id')->get();

    if ($projects->isEmpty()) {
        return response()->json(['data' => []]);
    }

    $data = $projects->map(fn($p) => mapProjectToFrontend($p))->values();

    return response()->json(['data' => $data]);
});

Route::get('/projects/{slug}', function (string $slug) {
    $project = Project::with('floors.panoramas.hotspots')
        ->where('slug', $slug)
        ->orWhere('id', $slug)
        ->first();

    if (!$project) {
        return response()->json(['message' => 'Project not found'], 404);
    }

    $data = mapProjectToFrontend($project);
    return response()->json(['data' => $data]);
});

// Keep original floors endpoint for backward compat + better shape
Route::get('/floors', function () {
    $floors = Floor::with(['panoramas.hotspots'])->orderBy('id')->get();

    $data = $floors->map(function ($floor) {
        return [
            'id' => (string) $floor->id,
            'name' => $floor->name,
            'shortLabel' => $floor->short_label,
            'description' => $floor->description,
            'planImage' => normalizeStorageUrl($floor->plan_image),
            'defaultPanoramaId' => $floor->panoramas->first()?->id ? (string) $floor->panoramas->first()->id : null,
            'videos' => [],
            'panoramas' => $floor->panoramas->map(fn($p) => mapPanoramaForApi($p))->values(),
        ];
    });

    return response()->json($data);
});
