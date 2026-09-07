@extends('admin.layouts.master-page')

@section('title')
    Map
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Map</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$titlePage}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" data-url-submit="{{route('save_project')}}" data-url-complete="">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <button class="btn btn-primary">Save</button>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="mb-3 position-relative">
                                    <label for="mapLinkInput" class="form-label">Link Google Maps</label>
                                    <input type="text" class="form-control" id="mapLinkInput" name="map" value="{{$project->map}}" placeholder="Paste the Google Maps link (Share -> Embed a map)">
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
                    <input type="hidden" name="id" value="{{$project->id}}">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
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
