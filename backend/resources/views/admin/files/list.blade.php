@extends('admin.layouts.master-page')

@section('title')
    Unused Files
@endsection

@php
    $folderSorting = request()->query('folder');
    $visibleOrphans = $folderSorting ? array_values(array_filter($orphans, fn($o) => $o['folder'] === $folderSorting)) : $orphans;
@endphp

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Unused Files (not in database)</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Unused Files</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-warning text-dark">{{ count($visibleOrphans) }} file(s)</span>
                    <span class="badge bg-secondary ms-1">{{ number_format($totalSize / 1048576, 2) }} MB orphan</span>
                </div>
                @if (count($visibleOrphans) > 0)
                    <button class="btn btn-danger" id="deleteAllBtn" onclick="deleteAllOrphans();"><i class="fa-solid fa-trash"></i> Delete all orphans</button>
                @endif
            </div>

            @if ($folderSorting)
                <a class="btn btn-outline-dark mb-3" href="{{route('show_file')}}">← All folders</a>
            @endif

            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="60px" class="text-center"><input type="checkbox" id="checkAll"></th>
                        <th scope="col" width="260px">Folder
                            @foreach (['buildings','floors','panaromas','panaroma-images','videos','company/logo','company/favicon'] as $f)
                                <a class="badge bg-primary" href="{{route('show_file', ['folder' => $f])}}">{{$f}}</a>
                            @endforeach
                        </th>
                        <th scope="col">File name</th>
                        <th scope="col" width="120px" class="text-center">Size</th>
                        <th scope="col" width="180px" class="text-center">Last modified</th>
                        <th scope="col" width="140px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($visibleOrphans) == 0)
                        <tr><td colspan="6" class="text-center text-success py-4">✅ No unused files — everything is referenced in the database.</td></tr>
                    @endif
                    @foreach ($visibleOrphans as $orphan)
                        <tr data-path="{{ $orphan['path'] }}">
                            <td class="text-center"><input type="checkbox" class="orphan-check" value="{{ $orphan['path'] }}"></td>
                            <td valign="middle"><span class="badge bg-dark">{{ $orphan['folder'] }}</span></td>
                            <td valign="middle" style="word-break: break-all;">{{ $orphan['name'] }}</td>
                            <td valign="middle" class="text-center">{{ number_format($orphan['size']/1024, 1) }} KB</td>
                            <td valign="middle" class="text-center">{{ $orphan['modified'] }}</td>
                            <td valign="middle" class="text-center">
                                <button class="btn btn-outline-danger btn-sm" title="Delete" data-path="{{ $orphan['path'] }}" onclick="deleteOrphan(this);"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var deleteUrl = '{{ route('delete_orphan_file') }}';
        var csrfToken = '{{ csrf_token() }}';

        function deleteOrphan(btn) {
            var path = $(btn).data('path');
            Swal.fire({
                title: 'Delete file?',
                text: path,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: { path: path, mode: 'single', _token: csrfToken },
                    success: function(res) {
                        if (res.success == true) {
                            toastr.success('Deleted');
                            $(btn).closest('tr').fadeOut(300, function() { location.reload(); });
                        } else {
                            Swal.fire({ text: res.message, icon: 'error' });
                        }
                    },
                    error: function() { Swal.fire({ text: 'Request failed', icon: 'error' }); }
                });
            });
        }

        function deleteAllOrphans() {
            Swal.fire({
                title: 'Delete ALL unused files?',
                text: 'Make sure backups exist; this cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete all',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: { mode: 'all', _token: csrfToken },
                    success: function(res) {
                        if (res.success == true) {
                            toastr.success(res.message || 'Deleted');
                            setTimeout(function() { location.reload(); }, 800);
                        } else {
                            Swal.fire({ text: res.message, icon: 'error' });
                        }
                    },
                    error: function() { Swal.fire({ text: 'Request failed', icon: 'error' }); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var all = document.getElementById('checkAll');
            var checks = document.querySelectorAll('.orphan-check');
            if (all) {
                all.addEventListener('change', function() {
                    checks.forEach(function(c) { c.checked = all.checked; });
                });
            }
        });
    </script>
@endsection
