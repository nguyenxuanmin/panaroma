@extends('admin.layouts.master-page')

@section('title')
    Change Password
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Change Password</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('list_project')}}">User</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$titlePage}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" data-url-submit="{{route('save_project')}}" data-url-complete="{{route('list_project')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <button class="btn btn-primary">Save</button>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="new" class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new" value="">
                                    <i class="bi bi-eye icon-eye" onclick="togglePassword('new')"></i>
                                </div>
                                <div class="mb-3 position-relative">
                                    <label for="confirm" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm" value="">
                                    <i class="bi bi-eye icon-eye" onclick="togglePassword('confirm')"></i>
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
    </script>
@endsection
