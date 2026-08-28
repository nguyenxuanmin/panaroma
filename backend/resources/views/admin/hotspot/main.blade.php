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
                        <li class="breadcrumb-item"><a href="{{route('list_hotspot')}}">Hotspot</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$titlePage}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" enctype="multipart/form-data" data-url-submit="{{route('save_hotspot')}}" data-url-complete="{{route('list_hotspot')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                @if ($action == 'add')
                                    <button class="btn btn-primary">Create New</button>
                                @else
                                    <button class="btn btn-info">Update</button>
                                @endif
                                <a href="{{route('list_hotspot')}}" class="btn btn-dark">Back</a>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" value="@if (isset($hotspot)){{$hotspot->title}}@endif">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Target Panorama</label>
                                    <select class="form-select" name="targetPanorama">
                                        @if (isset($targetPanoramas))
                                            @if (!isset($hotspot) || (isset($hotspot) && empty($hotspot->target_panorama_id)))
                                                <option selected disabled value="">Select Target Panorama</option>
                                            @endif
                                            @foreach ($targetPanoramas as $item)
                                                <option @if (isset($hotspot) && $item->id == $hotspot->target_panorama_id) selected @endif value="{{$item->id}}" data-image="{{ $item->thumbnail ? asset($item->thumbnail) : '' }}">{{$item->name}}</option>
                                            @endforeach
                                        @else
                                            <option selected disabled value="">Select Target Panorama</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="mb-3">
                                    <label class="form-label">Panorama</label>
                                    <select class="form-select" name="panorama" id="panoramaSelect">
                                        @if (isset($panoramas))
                                            @if (!isset($hotspot) || (isset($hotspot) && empty($hotspot->panorama_id)))
                                                <option selected disabled value="">Select Panorama</option>
                                            @endif
                                            @foreach ($panoramas as $item)
                                                <option @if (isset($hotspot) && $item->id == $hotspot->panorama_id) selected @endif value="{{$item->id}}" data-image="{{ $item->thumbnail ? asset($item->thumbnail) : '' }}">{{$item->name}}</option>
                                            @endforeach
                                        @else
                                            <option selected disabled value="">Select Panorama</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3" id="panoramaPlanContainer" style="display: none;">
                                    <label class="form-label">Select hotspot position</label>
                                    <div id="panoramaPlan" style="position: relative; cursor: crosshair; line-height: 0;">
                                        <img id="panoramaPlanImage" src="" alt="Panorama preview" style="display: block; width: 100%; height: auto;">
                                        <span id="panoramaPlanMarker" aria-hidden="true" style="display: none; position: absolute; width: 14px; height: 14px; margin: -7px 0 0 -7px; border: 2px solid #fff; border-radius: 50%; background: #dc3545; box-shadow: 0 0 0 1px #000;"></span>
                                    </div>
                                    <small class="text-muted">Click on the panorama to set yaw and pitch.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="{{$action}}">
                    <input type="hidden" name="id" value="@if (isset($hotspot)){{$hotspot->id}}@endif">
                    <input type="hidden" class="form-control" name="yaw" value="@if (isset($hotspot)){{$hotspot->yaw}}@endif">
                    <input type="hidden" class="form-control" name="pitch" value="@if (isset($hotspot)){{$hotspot->pitch}}@endif">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const panoramaSelect = document.getElementById('panoramaSelect');
            const panoramaPlanContainer = document.getElementById('panoramaPlanContainer');
            const panoramaPlan = document.getElementById('panoramaPlan');
            const panoramaPlanImage = document.getElementById('panoramaPlanImage');
            const panoramaPlanMarker = document.getElementById('panoramaPlanMarker');
            const yaw = document.querySelector('[name="yaw"]');
            const pitch = document.querySelector('[name="pitch"]');

            function updatePanoramaPreview() {
                const imageUrl = panoramaSelect.options[panoramaSelect.selectedIndex]?.dataset.image || '';
                panoramaPlanImage.src = imageUrl;
                panoramaPlanContainer.style.display = imageUrl ? '' : 'none';
                if (!imageUrl) {
                    panoramaPlanMarker.style.display = 'none';
                }
            }

            function restoreMarker() {
                if (yaw.value === '' || pitch.value === '') {
                    return;
                }
                const x = ((Number(yaw.value) + 180) / 360) * 100;
                const y = ((90 - Number(pitch.value)) / 180) * 100;
                panoramaPlanMarker.style.left = `${Math.max(0, Math.min(100, x))}%`;
                panoramaPlanMarker.style.top = `${Math.max(0, Math.min(100, y))}%`;
                panoramaPlanMarker.style.display = 'block';
            }

            panoramaSelect.addEventListener('change', function () {
                [yaw, pitch].forEach(function (field) {
                    field.value = '';
                });
                panoramaPlanMarker.style.display = 'none';
                updatePanoramaPreview();
            });
            panoramaPlan.addEventListener('click', function (event) {
                const bounds = panoramaPlanImage.getBoundingClientRect();
                const x = Math.max(0, Math.min(100, ((event.clientX - bounds.left) / bounds.width) * 100));
                const y = Math.max(0, Math.min(100, ((event.clientY - bounds.top) / bounds.height) * 100));

                yaw.value = (x / 100 * 360 - 180).toFixed(2);
                pitch.value = (90 - y / 100 * 180).toFixed(2);
                panoramaPlanMarker.style.left = `${x}%`;
                panoramaPlanMarker.style.top = `${y}%`;
                panoramaPlanMarker.style.display = 'block';
            });

            panoramaPlanImage.addEventListener('load', restoreMarker);
            updatePanoramaPreview();
            restoreMarker();
        });
    </script>
@endsection
