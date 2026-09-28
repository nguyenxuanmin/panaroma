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
                        <li class="breadcrumb-item"><a href="{{route('list_project')}}">Project</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$titlePage}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" enctype="multipart/form-data" data-url-submit="{{route('save_project')}}" data-url-complete="{{route('list_project')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                @if ($action == 'add')
                                    <button class="btn btn-primary">Create New</button>
                                @else
                                    <button class="btn btn-info">Update</button>
                                @endif
                                <a href="{{route('list_project')}}" class="btn btn-dark">Back</a>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="mb-3">
                                    <label class="form-label">Project Name</label>
                                    <input type="text" class="form-control" name="name" value="@if (isset($project)){{$project->name}}@endif">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="user_name" value="@if (isset($project)){{$project->user_name}}@endif">
                                </div>
                                @if ($action == 'add')
                                    <div class="mb-3 position-relative">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" value="">
                                        <i class="bi bi-eye icon-eye" onclick="togglePassword('password')"></i>
                                    </div>
                                    <div class="mb-3 position-relative">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="confirm" value="">
                                        <i class="bi bi-eye icon-eye" onclick="togglePassword('confirm')"></i>
                                    </div>
                                @else
                                    <input type="hidden" class="form-control" name="password" value="">
                                    <input type="hidden" class="form-control" name="confirm" value="">
                                @endif
                            </div>
                            <div class="col-12 col-md-7">
                                <div class="mb-3 position-relative">
                                    <label for="mapLinkInput" class="form-label">Link Google Maps</label>
                                    <input type="text" class="form-control" id="mapLinkInput" name="map" value="@if (isset($project)){{$project->map}}@endif" placeholder="Paste the Google Maps link (Share -> Embed a map)">
                                    <small class="form-text text-muted">Tip: Go to Google Maps -> Share -> Embed a map -> Copy HTML, then paste it here to preview.</small>
                                    <div id="mapPreviewWrapper" class="mt-3" style="display:none;">
                                        <label class="form-label text-muted small mb-1">Preview:</label>
                                        <div class="border rounded overflow-hidden" style="height:350px; background:#f8f9fa;">
                                            <iframe id="mapPreview" src="" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                                        </div>
                                        <small id="mapPreviewHint" class="text-warning d-none">This link is not in the correct embed format. Please use a link in the format <code>https://www.google.com/maps/embed?pb=...</code> to display correctly.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="{{$action}}">
                    <input type="hidden" name="id" value="@if (isset($project)){{$project->id}}@endif">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function togglePassword(inputName) {
            let inputChange = document.querySelector(`input[name="${inputName}"]`);
            if (!inputChange) return;

            if (inputChange.type === 'password') {
                inputChange.type = 'text';
            } else {
                inputChange.type = 'password';
            }
        }
        function extractMapSrc(value) {
            if (!value) return '';
            value = value.trim();
            const srcMatch = value.match(/src=["']([^"']+)["']/i);
            if (srcMatch) return srcMatch[1];
            return value;
        }
        function isGoogleMapUrl(url) {
            return url.includes('google.com/maps') || url.includes('maps.app.goo.gl') || url.includes('goo.gl/maps');
        }
        function updateMapPreview() {
            const raw = $('#mapLinkInput').val();
            const src = extractMapSrc(raw);
            const $wrapper = $('#mapPreviewWrapper');
            const $iframe = $('#mapPreview');
            const $hint = $('#mapPreviewHint');

            if (!raw || !raw.trim()) {
                $wrapper.hide();
                $iframe.attr('src', '');
                return;
            }
            if (!isGoogleMapUrl(src)) {
                $wrapper.hide();
                $iframe.attr('src', '');
                return;
            }
            if (!src.includes('/maps/embed')) {
                $hint.removeClass('d-none');
            } else {
                $hint.addClass('d-none');
            }
            $iframe.attr('src', src);
            $wrapper.show();
        }
        $(document).ready(function() {
            $('#mapLinkInput').on('input change', updateMapPreview);
            updateMapPreview();
        });
    </script>
@endsection
