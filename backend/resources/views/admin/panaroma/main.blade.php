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
                            <div class="col-12 col-md-7">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" value="@if (isset($panaroma)){{$panaroma->name}}@endif">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Building</label>
                                    <select class="form-select" name="building_id" id="buildingSelect">
                                        <option value="" disabled @if (!isset($panaroma)) selected @endif>-- Select Building --</option>
                                        @if (isset($buildings))
                                            @foreach ($buildings as $b)
                                                @php
                                                    $isSelected = false;
                                                    if (isset($panaroma)) {
                                                        if (!empty($panaroma->building_id) && $panaroma->building_id == $b->id) $isSelected = true;
                                                        if (!empty($panaroma->floor_id) && $panaroma->floor && $panaroma->floor->building_id == $b->id) $isSelected = true;
                                                    }
                                                @endphp
                                                <option value="{{$b->id}}" data-type="{{$b->type}}" data-image="{{ $b->plan_image ? asset($b->plan_image) : '' }}" @if ($isSelected) selected @endif>{{$b->name}} — {{$b->type}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="text-muted">Single: panaroma gắn trực tiếp building. Group: phải chọn thêm Floor.</small>
                                </div>
                                <div class="mb-3" id="floorWrapper" style="display:none;">
                                    <label class="form-label">Floor (chỉ khi Building = group)</label>
                                    <select class="form-select" name="floor_id" id="floorSelect">
                                        <option value="" disabled selected>-- Select Floor --</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="floorPlanContainer" style="display: none;">
                                    <label class="form-label" id="mapLabel">Position on map</label>
                                    <div id="floorPlan" style="position: relative; cursor: crosshair; line-height: 0;">
                                        <img id="floorPlanImage" src="" alt="Sơ đồ map" style="display: block; max-width: 100%; height: auto;">
                                        <span id="floorPlanMarker" aria-hidden="true" style="display: none; position: absolute; width: 14px; height: 14px; margin: -7px 0 0 -7px; border: 2px solid #fff; border-radius: 50%; background: #dc3545; box-shadow: 0 0 0 1px #000;"></span>
                                    </div>
                                    <small class="text-muted">Click on the diagram to select a panaroma location.</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" class="form-control mb-3" name="image" id="imageUpload" accept="image/*">
                                    <div class="imageContent">
                                        <img id="imageContent" src="@if (isset($panaroma) && !empty($panaroma->thumbnail)){{ asset($panaroma->thumbnail) }}@else{{asset('library/admin/default-image.png')}}@endif" alt="Image preview" style="max-width: 100%; max-height: 200px;">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Other Images</label>
                                    <input type="file" name="panaromaImages[]" id="imageUploads" class="form-control mb-3" multiple accept="image/*">
                                    <div id="previewImageUploads"></div>
                                    @if (isset($panaroma))
                                        <p><label class="form-label">List of other images</label></p>
                                        <div class="d-flex flex-wrap justify-content-start align-items-start gap-2">
                                            @foreach ($panaroma->panaromaImages as $item)
                                                <div class="list-image">
                                                    <img src="{{ asset($item->thumbnail) }}" alt="{{$item->title}}">
                                                    <i class="fa-solid fa-xmark" onclick="deletePhoto({{$item->id}},'{{route('delete_panaroma_image')}}')"></i>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
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
        const buildingsData = @json($buildings ?? []);
        // map buildingId -> floors
        const floorsByBuilding = {};
        buildingsData.forEach(b => { floorsByBuilding[b.id] = b.floors || []; });

        document.addEventListener('DOMContentLoaded', function () {
            const buildingSelect = document.getElementById('buildingSelect');
            const floorSelect = document.getElementById('floorSelect');
            const floorWrapper = document.getElementById('floorWrapper');
            const floorPlanContainer = document.getElementById('floorPlanContainer');
            const floorPlan = document.getElementById('floorPlan');
            const floorPlanImage = document.getElementById('floorPlanImage');
            const floorPlanMarker = document.getElementById('floorPlanMarker');
            const mapLabel = document.getElementById('mapLabel');
            const mapX = document.querySelector('[name="map_x"]');
            const mapY = document.querySelector('[name="map_y"]');
            const mapAngle = document.querySelector('[name="map_angle"]');
            const yaw = document.querySelector('[name="yaw"]');
            const pitch = document.querySelector('[name="pitch"]');

            const initialFloorId = "{{ isset($panaroma) ? ($panaroma->floor_id ?? '') : '' }}";

            function populateFloors(buildingId, selectedFloorId = null) {
                floorSelect.innerHTML = '<option value="" disabled selected>-- Select Floor --</option>';
                const floors = floorsByBuilding[buildingId] || [];
                floors.forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f.id;
                    opt.textContent = f.name;
                    opt.dataset.image = f.plan_image ? ('/' + f.plan_image.replace(/^\//,'')) : '';
                    // normalize to asset URL if needed
                    if (f.plan_image && !f.plan_image.startsWith('http')) {
                        // buildingsData plan_image is storage/... -> use same origin
                        opt.dataset.image = '/' + f.plan_image.replace(/^\//,'');
                        // also try asset prefix
                        if (buildingsData.find(b=>b.id==buildingId)?.plan_image) { /* keep */ }
                    }
                    // use original asset url from select building's floors if available via data-image on building options fallback
                    if (f.plan_image) {
                        // attempt to reuse asset helper: floors' plan_image may need asset()
                        opt.dataset.image = "{{ asset('') }}".replace(/\/$/,'') + '/' + f.plan_image;
                    }
                    if (String(f.id) === String(selectedFloorId)) opt.selected = true;
                    floorSelect.appendChild(opt);
                });
            }

            function updateMapImage() {
                const bOpt = buildingSelect.options[buildingSelect.selectedIndex];
                if (!bOpt || !bOpt.value) {
                    floorPlanContainer.style.display = 'none';
                    floorPlanMarker.style.display = 'none';
                    return;
                }
                const type = bOpt.dataset.type;
                let imageUrl = '';
                if (type === 'single') {
                    imageUrl = bOpt.dataset.image || '';
                    mapLabel.textContent = 'Position on building map (single)';
                } else {
                    const fOpt = floorSelect.options[floorSelect.selectedIndex];
                    imageUrl = fOpt?.dataset.image || '';
                    mapLabel.textContent = 'Position on floor map (group)';
                }
                floorPlanImage.src = imageUrl;
                floorPlanContainer.style.display = imageUrl ? '' : 'none';
                if (!imageUrl) floorPlanMarker.style.display = 'none';
            }

            function restoreMarker() {
                if (mapX.value === '' || mapY.value === '') {
                    return;
                }
                floorPlanMarker.style.left = `${mapX.value}%`;
                floorPlanMarker.style.top = `${mapY.value}%`;
                floorPlanMarker.style.display = 'block';
            }

            function handleBuildingChange(resetCoords = true) {
                const bOpt = buildingSelect.options[buildingSelect.selectedIndex];
                if (!bOpt || !bOpt.value) {
                    floorWrapper.style.display = 'none';
                    floorPlanContainer.style.display = 'none';
                    return;
                }
                const type = bOpt.dataset.type;
                if (type === 'group') {
                    floorWrapper.style.display = '';
                    // populate if empty or building changed
                    const currentBuildingId = bOpt.value;
                    const alreadyPopulated = floorSelect.options.length > 1 && floorSelect.dataset.buildingId === currentBuildingId;
                    if (!alreadyPopulated) {
                        populateFloors(currentBuildingId, initialFloorId);
                        floorSelect.dataset.buildingId = currentBuildingId;
                    }
                } else {
                    floorWrapper.style.display = 'none';
                    floorSelect.innerHTML = '<option value="" disabled selected>-- Select Floor --</option>';
                    floorSelect.dataset.buildingId = '';
                }
                if (resetCoords) {
                    [mapX, mapY, mapAngle, yaw, pitch].forEach(f => f.value = '');
                    floorPlanMarker.style.display = 'none';
                }
                updateMapImage();
            }

            buildingSelect.addEventListener('change', function(){ handleBuildingChange(true); });
            floorSelect.addEventListener('change', function () {
                [mapX, mapY, mapAngle, yaw, pitch].forEach(function (field) {
                    field.value = '';
                });
                floorPlanMarker.style.display = 'none';
                updateMapImage();
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
            // init: if editing, restore building/floor state
            if (buildingSelect.value) {
                handleBuildingChange(false);
            }
            updateMapImage();
            restoreMarker();
        });

        $('#imageUploads').on('change', function (event) {
            const preview = $('#previewImageUploads');
            preview.empty();
            const files = event.target.files;
            $.each(files, function (i, file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = $('<img>')
                    .attr('src', e.target.result)
                    .css({
                        width: '200px',
                        height: 'auto',
                        margin: '5px',
                        border: '1px solid #ccc',
                        'object-fit': 'cover',
                        'border-radius': '5px'
                    });
                    preview.append(img);
                };
                reader.readAsDataURL(file);
            });
        });

        function deletePhoto(id,url) {
                Swal.fire({
                    text: 'Do you want to delete this image?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            type: 'POST',
                            data: {id: id},
                            success: function(response) {
                                Swal.fire({
                                    text: "Image deleted successfully!",
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then((result) => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                console.log(xhr);
                            }
                        });
                    }
                });
            }
    </script>
@endsection
