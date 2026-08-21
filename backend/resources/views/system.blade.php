<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Thiết lập hệ thống</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous"/>
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    </head>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            <main class="app-main">
                <div class="app-content-header">
                    <div class="container">
                        <div class="row">
                            <div class="col-12"><h3 class="mb-0">Thiết lập hệ thống</h3></div>
                        </div>
                    </div>
                </div>
                <div class="app-content">
                    <div class="container">
                        <div class="card card-primary card-outline mb-4">
                            <form id="submitForm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-header mb-3"><div class="card-title">Thông tin công ty</div></div>
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Tên công ty</label>
                                                <input type="text" class="form-control" name="name" value="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Địa chỉ</label>
                                                <input type="text" class="form-control" name="address" value="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="hotline" class="form-label">Hotline</label>
                                                <input type="text" class="form-control" name="hotline" value="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="text" class="form-control" name="email" value="">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label for="logo" class="form-label">Logo</label>
                                                <input type="file" class="form-control mb-3" name="logo" id="logo" accept="image/*">
                                                <div class="logoContent">
                                                    <img id="logoContent" src="{{asset('library/admin/default-image.png')}}" alt="Logo preview" style="max-width: 100%; max-height: 150px;">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="favicon" class="form-label">Favicon</label>
                                                <input type="file" class="form-control mb-3" name="favicon" id="favicon" accept="image/*">
                                                <div class="faviconContent">
                                                    <img id="faviconContent" src="{{asset('library/admin/default-image.png')}}" alt="Favicon preview" style="max-width: 100%; max-height: 100px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-header mb-3"><div class="card-title">Thông tin admin</div></div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="nameAdmin" class="form-label">Tên</label>
                                                        <input type="text" class="form-control" name="nameAdmin" value="">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="userNameAdmin" class="form-label">Tên đăng nhập</label>
                                                        <input type="text" class="form-control" name="userNameAdmin" value="">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="emailAdmin" class="form-label">Email</label>
                                                        <input type="text" class="form-control" name="emailAdmin" value="">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="passWordAdmin" class="form-label">Mật khẩu</label>
                                                        <input type="password" class="form-control" name="passWordAdmin" value="">
                                                    </div>
                                                </div>
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
            </main>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
        <script src="{{ asset('js/admin.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    
                $('#submitForm').on('submit', function(e){
                    e.preventDefault();
                    var formData = new FormData(this);
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '{{ route('save_system') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false, 
                        success: function(response) {
                            if (response.success == true) {
                                Swal.fire({
                                    text: "Thành công!",
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then((result) => {
                                    location.href = '{{route('login')}}';
                                });
                            }else{
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                        }
                    });
                });
            });
        </script>
    </body>
</html>
