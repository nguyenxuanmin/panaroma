@extends('admin.layout.master-page')

@section('title')
    Đổi mật khẩu admin
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Đổi mật khẩu admin</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Đổi mật khẩu admin</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline mb-4">
                <form id="submitForm" data-url-submit="{{route('save_company')}}" data-url-complete="">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="old" class="form-label">Mật khẩu cũ</label>
                                    <input type="password" class="form-control pr" name="old" value="">
                                    <i class="bi bi-eye icon-eye" onclick="togglePassword('old', this)"></i>
                                </div>
                                <div class="mb-3 position-relative">
                                    <label for="new" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" name="new" value="">
                                </div>
                                <div class="mb-3 position-relative">
                                    <label for="confirm" class="form-label">Xác nhận lại mật khẩu mới</label>
                                    <input type="password" class="form-control" name="confirm" value="">
                                </div>
                            </div>
                            <div class="col-12 mb-3 text-end">
                                <button class="btn btn-primary">Lưu</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            document.getElementById('logo').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageUrl = e.target.result;
                        const imgElement = document.getElementById('logoContent'); 
                        imgElement.src = imageUrl; 
                        imgElement.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            document.getElementById('favicon').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageUrl = e.target.result;
                        const imgElement = document.getElementById('faviconContent'); 
                        imgElement.src = imageUrl; 
                        imgElement.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
