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
                                    <label class="form-label">Target Panaroma</label>
                                    <select class="form-select" name="targetPanaroma">
                                        @if (isset($targetPanaromas))
                                            @if (!isset($hotspot) || (isset($hotspot) && empty($hotspot->target_panaroma_id)))
                                                <option selected disabled value="">Select Target Panaroma</option>
                                            @endif
                                            @foreach ($targetPanaromas as $item)
                                                <option @if (isset($hotspot) && $item->id == $hotspot->target_panaroma_id) selected @endif value="{{$item->id}}" data-image="{{ $item->thumbnail ? asset($item->thumbnail) : '' }}">{{$item->name}}</option>
                                            @endforeach
                                        @else
                                            <option selected disabled value="">Select Target Panaroma</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="mb-3">
                                    <label class="form-label">Panaroma</label>
                                    <select class="form-select" name="panaroma" id="panaromaSelect">
                                        @if (isset($panaromas))
                                            @if (!isset($hotspot) || (isset($hotspot) && empty($hotspot->panaroma_id)))
                                                <option selected disabled value="">Select Panaroma</option>
                                            @endif
                                            @foreach ($panaromas as $item)
                                                <option @if (isset($hotspot) && $item->id == $hotspot->panaroma_id) selected @endif value="{{$item->id}}" data-image="{{ $item->thumbnail ? asset($item->thumbnail) : '' }}">{{$item->name}}</option>
                                            @endforeach
                                        @else
                                            <option selected disabled value="">Select Panaroma</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3" id="panaromaPlanContainer" style="display: none;">
                                    <label class="form-label">Select hotspot position</label>
                                    <div id="panaromaPlan" style="position: relative; cursor: crosshair; line-height: 0;">
                                        <img id="panaromaPlanImage" src="" alt="Panaroma preview" style="display: block; width: 100%; height: auto;">
                                        <span id="panaromaPlanMarker" aria-hidden="true" style="display: none; position: absolute; width: 14px; height: 14px; margin: -7px 0 0 -7px; border: 2px solid #fff; border-radius: 50%; background: #dc3545; box-shadow: 0 0 0 1px #000;"></span>
                                    </div>
                                    <small class="text-muted">Click on the panaroma to set yaw and pitch.</small>
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
            const panaromaSelect = document.getElementById('panaromaSelect');
            const panaromaPlanContainer = document.getElementById('panaromaPlanContainer');
            const panaromaPlan = document.getElementById('panaromaPlan');
            const panaromaPlanImage = document.getElementById('panaromaPlanImage');
            const panaromaPlanMarker = document.getElementById('panaromaPlanMarker');
            const yaw = document.querySelector('[name="yaw"]');
            const pitch = document.querySelector('[name="pitch"]');

            function updatePanaromaPreview() {
                const imageUrl = panaromaSelect.options[panaromaSelect.selectedIndex]?.dataset.image || '';
                panaromaPlanImage.src = imageUrl;
                panaromaPlanContainer.style.display = imageUrl ? '' : 'none';
                if (!imageUrl) {
                    panaromaPlanMarker.style.display = 'none';
                }
            }

            function restoreMarker() {
                if (yaw.value === '' || pitch.value === '') {
                    return;
                }
                const x = ((Number(yaw.value) + 180) / 360) * 100;
                const y = ((90 - Number(pitch.value)) / 180) * 100;
                panaromaPlanMarker.style.left = `${Math.max(0, Math.min(100, x))}%`;
                panaromaPlanMarker.style.top = `${Math.max(0, Math.min(100, y))}%`;
                panaromaPlanMarker.style.display = 'block';
            }

            panaromaSelect.addEventListener('change', function () {
                [yaw, pitch].forEach(function (field) {
                    field.value = '';
                });
                panaromaPlanMarker.style.display = 'none';
                updatePanaromaPreview();
            });
            panaromaPlan.addEventListener('click', function (event) {
                const bounds = panaromaPlanImage.getBoundingClientRect();
                const x = Math.max(0, Math.min(100, ((event.clientX - bounds.left) / bounds.width) * 100));
                const y = Math.max(0, Math.min(100, ((event.clientY - bounds.top) / bounds.height) * 100));

                yaw.value = (x / 100 * 360 - 180).toFixed(2);
                pitch.value = (90 - y / 100 * 180).toFixed(2);
                panaromaPlanMarker.style.left = `${x}%`;
                panaromaPlanMarker.style.top = `${y}%`;
                panaromaPlanMarker.style.display = 'block';
            });

            panaromaPlanImage.addEventListener('load', restoreMarker);
            updatePanaromaPreview();
            restoreMarker();
        });
    </script>
@endsection
