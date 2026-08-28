<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Floor;

Route::get('/floors', function () {

    $floors = Floor::with([
        'panoramas.hotspots'
    ])->orderBy('id')->get();

    $data = $floors->map(function ($floor) {

        return [
            'id' => $floor->id,

            'name' => $floor->name,

            'shortLabel' => $floor->short_label,

            'description' => $floor->description,

            'planImage' => $floor->plan_image ? $floor->plan_image : null,

            'defaultPanoramaId' => $floor->panoramas->first()?->id,

            'videos' => [],

            'panoramas' => $floor->panoramas->map(function ($panorama) {

                return [
                    'id' => $panorama->id,

                    'name' => $panorama->name,

                    'code' => $panorama->code,

                    'number' => $panorama->number,

                    'thumbnail' => $panorama->thumbnail ? $panorama->thumbnail : null,

                    'url' => $panorama->url ? $panorama->url : null,

                    'mapPosition' => [
                        'x' => (float) $panorama->map_x,
                        'y' => (float) $panorama->map_y,
                        'angle' => (float) $panorama->map_angle,
                    ],

                    'defaultView' => [
                        'yaw' => (float) $panorama->default_yaw,
                        'pitch' => (float) $panorama->default_pitch,
                    ],

                    'hotspots' => $panorama->hotspots->map(function ($hotspot) {

                        return [
                            'id' => $hotspot->id,

                            'yaw' => (float) $hotspot->yaw,

                            'pitch' => (float) $hotspot->pitch,

                            'tooltip' => $hotspot->title,

                            'targetPanorama' => $hotspot->target_panorama_id,
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    });

    return response()->json($data);
});
