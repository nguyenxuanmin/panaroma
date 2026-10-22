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
                        <li class="breadcrumb-item"><a href="{{route('list_video')}}">Video</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$titlePage}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" enctype="multipart/form-data" data-url-submit="{{route('save_video')}}" data-url-complete="{{route('list_video')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                @if ($action == 'add')
                                    <button class="btn btn-primary">Create New</button>
                                @else
                                    <button class="btn btn-info">Update</button>
                                @endif
                                <a href="{{route('list_video')}}" class="btn btn-dark">Back</a>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" value="@if (isset($video)){{$video->title}}@endif">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload video file</label>
                                    <input type="file" class="form-control" name="video_file" id="videoFile" accept=".mp4,.webm,.ogg,.m4v,.mov,video/mp4,video/webm,video/ogg">
                                    <small class="text-muted">MP4 / WEBM / OGG / M4V / MOV</small>
                                </div>
                                <div class="mb-3" style="display:none;">
                                    <label class="form-label">Link YouTube (optional)</label>
                                    <input type="text" class="form-control" name="link" id="videoLink" placeholder="https://www.youtube.com/watch?v=... hoặc https://youtu.be/..." value="@if (isset($video)){{$video->link}}@endif">
                                    <small class="text-muted">Use only when no file is being uploaded.</small>
                                </div>
                                <div id="videoPreviewWrap" class="mb-3" style="display:none;">
                                    <label class="form-label">Preview video</label>
                                    <div id="videoPreviewRatio" class="ratio ratio-16x9 rounded overflow-hidden border bg-dark">
                                        <iframe id="videoPreviewFrame" src="" title="YouTube video preview" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="width:100%;height:100%;"></iframe>
                                    </div>
                                    <video id="videoPreviewPlayer" controls style="display:none;width:100%;max-height:360px;border-radius:8px;background:#000;"></video>
                                    <small id="videoPreviewError" class="text-danger mt-1" style="display:none;"></small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Project</label>
                                <select class="form-select" name="project">
                                    @if (isset($projects))
                                        @if (!isset($video) || (isset($video) && empty($video->project_id)))
                                            <option selected disabled value="">Select project</option>
                                        @endif
                                        @foreach ($projects as $item)
                                            <option @if (isset($video) && $item->id == $video->project_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    @else
                                        <option selected disabled value="">Select project</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="{{$action}}">
                    <input type="hidden" name="id" value="@if (isset($video)){{$video->id}}@endif">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        @php
            $existingVideoUrl = (isset($video) && !empty($video->link) && !str_starts_with($video->link, 'http')) ? asset($video->link) : null;
        @endphp
        var savedVideoUrl = @json($existingVideoUrl);
        var savedRawLink = @json(isset($video) ? $video->link : null);

        function getYouTubeEmbedUrl(url) {
            if (!url) return null;
            url = url.trim();
            // already embed
            var embedMatch = url.match(/youtube\.com\/embed\/([\w-]{11})/);
            if (embedMatch && embedMatch[1]) {
                return 'https://www.youtube.com/embed/' + embedMatch[1] + '?rel=0&modestbranding=1';
            }
            // shorts
            var shortsMatch = url.match(/youtube\.com\/shorts\/([\w-]{11})/);
            if (shortsMatch && shortsMatch[1]) {
                return 'https://www.youtube.com/embed/' + shortsMatch[1] + '?rel=0&modestbranding=1';
            }
            // youtu.be / watch?v= / v/ / watch?+v=
            var ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
            if (ytMatch && ytMatch[1]) {
                return 'https://www.youtube.com/embed/' + ytMatch[1] + '?rel=0&modestbranding=1';
            }
            // youtube.com/watch?v=ID with extra params - fallback extract v param
            try {
                var u = new URL(url);
                var v = u.searchParams.get('v');
                if (v && /^[\w-]{11}$/.test(v)) {
                    return 'https://www.youtube.com/embed/' + v + '?rel=0&modestbranding=1';
                }
            } catch (e) {}
            return null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('videoLink');
            var fileInput = document.getElementById('videoFile');
            var wrap = document.getElementById('videoPreviewWrap');
            var ratio = document.getElementById('videoPreviewRatio');
            var frame = document.getElementById('videoPreviewFrame');
            var player = document.getElementById('videoPreviewPlayer');
            var error = document.getElementById('videoPreviewError');

            function showFilePreview(file) {
                ratio.style.display = 'none';
                frame.src = '';
                player.src = URL.createObjectURL(file);
                player.style.display = '';
                error.style.display = 'none';
                wrap.style.display = '';
            }

            function showSavedPreview() {
                if (!savedVideoUrl) return;
                ratio.style.display = 'none';
                frame.src = '';
                if (player.src !== savedVideoUrl) {
                    player.src = savedVideoUrl;
                }
                player.style.display = '';
                error.style.display = 'none';
                wrap.style.display = '';
            }

            function updateVideoPreview() {
                var val = input ? input.value.trim() : '';
                // Có file mới chọn trong lần này -> luôn ưu tiên preview file mới
                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    return showFilePreview(fileInput.files[0]);
                }
                // Chưa upload file mới và đang dùng file cũ đã lưu -> preview file cũ
                if ((val === '' || val === savedRawLink) && savedVideoUrl) {
                    return showSavedPreview();
                }
                player.src = '';
                player.style.display = 'none';
                if (!val) {
                    wrap.style.display = 'none';
                    frame.src = '';
                    error.style.display = 'none';
                    return;
                }
                var embedUrl = getYouTubeEmbedUrl(val);
                if (embedUrl) {
                    frame.src = embedUrl;
                    wrap.style.display = '';
                    ratio.style.display = '';
                    error.style.display = 'none';
                } else {
                    // Check if user pasted something that looks like youtube but invalid
                    if (val.includes('youtu') || val.includes('youtube')) {
                        frame.src = '';
                        wrap.style.display = '';
                        ratio.style.display = 'none';
                        error.textContent = 'Link Youtube không hợp lệ. Vui lòng kiểm tra lại (VD: https://www.youtube.com/watch?v=VIDEO_ID)';
                        error.style.display = 'block';
                    } else {
                        // Not a youtube link yet while typing - hide preview
                        frame.src = '';
                        wrap.style.display = 'none';
                        error.style.display = 'none';
                    }
                }
            }

            if (fileInput) {
                fileInput.addEventListener('change', updateVideoPreview);
            }
            if (input) {
                input.addEventListener('input', updateVideoPreview);
                input.addEventListener('paste', function() { setTimeout(updateVideoPreview, 100); });
                // initial preview for edit mode
                updateVideoPreview();
            }
        });
    </script>
@endsection
