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
                        <li class="breadcrumb-item"><a href="{{route('list_building')}}">Panaroma Category</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$titlePage}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" enctype="multipart/form-data" data-url-submit="{{route('save_building')}}" data-url-complete="{{route('list_building')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                @if ($action == 'add')
                                    <button class="btn btn-primary">Create New</button>
                                @else
                                    <button class="btn btn-info">Update</button>
                                @endif
                                <a href="{{route('list_building')}}" class="btn btn-dark">Back</a>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" value="@if (isset($building)){{$building->name}}@endif">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select class="form-select" name="type" id="buildingType">
                                        <option value="single" @if (isset($building) && $building->type === 'group') @else selected @endif>Single — có ảnh, panaroma trực tiếp</option>
                                        <option value="group" @if (isset($building) && $building->type === 'group') selected @endif>Group — chứa floors, không có ảnh</option>
                                    </select>
                                    <small class="text-muted">Group thì không cần plan image, Single thì bắt buộc.</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3" id="buildingImageWrapper">
                                <label class="form-label">Image</label>
                                <input type="file" class="form-control mb-3" name="image" id="imageUpload" accept="image/*">
                                <div class="imageContent">
                                    <img id="imageContent" src="@if (isset($building) && !empty($building->plan_image)){{ asset($building->plan_image) }}@else{{asset('library/admin/default-image.png')}}@endif" alt="Image preview" style="max-width: 100%; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="{{$action}}">
                    <input type="hidden" name="id" value="@if (isset($building)){{$building->id}}@endif">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('buildingType');
        const imageWrapper = document.getElementById('buildingImageWrapper');
        function toggleImage() {
            if (!typeSelect || !imageWrapper) return;
            imageWrapper.style.display = typeSelect.value === 'group' ? 'none' : '';
        }
        if (typeSelect) {
            typeSelect.addEventListener('change', toggleImage);
            toggleImage();
        }
    });
</script>
@endsection
