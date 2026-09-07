@extends('admin.layouts.master-page')

@section('title')
    {{$titlePage}}
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">{{$titlePage}}</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('list_panaroma')}}">Panaroma</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$titlePage}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" enctype="multipart/form-data" data-url-submit="{{route('save_panaroma')}}" data-url-complete="{{route('list_panaroma')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                @if ($action == 'add')
                                    <button class="btn btn-primary">Create New</button>
                                @else
                                    <button class="btn btn-info">Update</button>
                                @endif
                                <a href="{{route('list_panaroma')}}" class="btn btn-dark">Back</a>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" value="@if (isset($panaroma)){{$panaroma->name}}@endif">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Panaroma Category</label>
                                    <select class="form-select" name="floor" id="floorSelect">
                                        @if (isset($floors))
                                            @if (!isset($panaroma) || (isset($panaroma) && empty($panaroma->floor_id)))
                                                <option selected disabled value="">Select Panaroma Category</option>
                                            @endif
                                            @foreach ($floors as $item)
                                                <option @if (isset($panaroma) && $item->id == $panaroma->floor_id) selected @endif value="{{$item->id}}" data-image="{{ $item->plan_image ? asset($item->plan_image) : '' }}">{{$item->name}}</option>
                                            @endforeach
                                        @else
                                            <option selected disabled value="">Select Panaroma Category</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3" id="floorPlanContainer" style="display: none;">
                                    <label class="form-label">Position on the panaroma category map</label>
                                    <div id="floorPlan" style="position: relative; cursor: crosshair; line-height: 0;">
                                        <img id="floorPlanImage" src="" alt="Sơ đồ floor" style="display: block; max-width: 100%; height: auto;">
                                        <span id="floorPlanMarker" aria-hidden="true" style="display: none; position: absolute; width: 14px; height: 14px; margin: -7px 0 0 -7px; border: 2px solid #fff; border-radius: 50%; background: #dc3545; box-shadow: 0 0 0 1px #000;"></span>
                                    </div>
                                    <small class="text-muted">Click on the diagram to select a panaroma location.</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" class="form-control mb-3" name="image" id="imageUpload" accept="image/*">
                                    <div class="imageContent">
                                        <img id="imageContent" src="@if (isset($panaroma) && !empty($panaroma->thumbnail)){{ asset($panaroma->thumbnail) }}@else{{asset('library/admin/default-image.png')}}@endif" alt="Image preview" style="max-width: 100%; max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="{{$action}}">
                    <input type="hidden" name="id" value="@if (isset($panaroma)){{$panaroma->id}}@endif">
                    <input type="hidden" class="form-control" name="map_x" value="@if (isset($panaroma)){{$panaroma->map_x}}@endif">
                    <input type="hidden" class="form-control" name="map_y" value="@if (isset($panaroma)){{$panaroma->map_y}}@endif">
                    <input type="hidden" class="form-control" name="map_angle" value="@if (isset($panaroma)){{$panaroma->map_angle}}@endif">
                    <input type="hidden" class="form-control" name="yaw" value="@if (isset($panaroma)){{$panaroma->default_yaw}}@endif">
                    <input type="hidden" class="form-control" name="pitch" value="@if (isset($panaroma)){{$panaroma->default_pitch}}@endif">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const floorSelect = document.getElementById('floorSelect');
            const floorPlanContainer = document.getElementById('floorPlanContainer');
            const floorPlan = document.getElementById('floorPlan');
            const floorPlanImage = document.getElementById('floorPlanImage');
            const floorPlanMarker = document.getElementById('floorPlanMarker');
            const mapX = document.querySelector('[name="map_x"]');
            const mapY = document.querySelector('[name="map_y"]');
            const mapAngle = document.querySelector('[name="map_angle"]');
            const yaw = document.querySelector('[name="yaw"]');
            const pitch = document.querySelector('[name="pitch"]');

            function updateFloorPlan() {
                const imageUrl = floorSelect.options[floorSelect.selectedIndex]?.dataset.image || '';
                floorPlanImage.src = imageUrl;
                floorPlanContainer.style.display = imageUrl ? '' : 'none';
                if (!imageUrl) {
                    floorPlanMarker.style.display = 'none';
                }
            }

            function restoreMarker() {
                if (mapX.value === '' || mapY.value === '') {
                    return;
                }
                floorPlanMarker.style.left = `${mapX.value}%`;
                floorPlanMarker.style.top = `${mapY.value}%`;
                floorPlanMarker.style.display = 'block';
            }

            floorSelect.addEventListener('change', function () {
                [mapX, mapY, mapAngle, yaw, pitch].forEach(function (field) {
                    field.value = '';
                });
                floorPlanMarker.style.display = 'none';
                updateFloorPlan();
            });
            floorPlan.addEventListener('click', function (event) {
                const bounds = floorPlanImage.getBoundingClientRect();
                const x = Math.max(0, Math.min(100, ((event.clientX - bounds.left) / bounds.width) * 100));
                const y = Math.max(0, Math.min(100, ((event.clientY - bounds.top) / bounds.height) * 100));
                const angle = Math.atan2(y - 50, x - 50) * 180 / Math.PI;
                const normalizedAngle = angle < 0 ? angle + 360 : angle;

                mapX.value = x.toFixed(2);
                mapY.value = y.toFixed(2);
                mapAngle.value = normalizedAngle.toFixed(2);
                yaw.value = (normalizedAngle > 180 ? normalizedAngle - 360 : normalizedAngle).toFixed(2);
                pitch.value = (50 - y).toFixed(2);
                floorPlanMarker.style.left = `${x}%`;
                floorPlanMarker.style.top = `${y}%`;
                floorPlanMarker.style.display = 'block';
            });

            floorPlanImage.addEventListener('load', restoreMarker);
            updateFloorPlan();
            restoreMarker();
        });
    </script>
@endsection
