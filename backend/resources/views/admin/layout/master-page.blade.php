@php
    $currentUrl = $_SERVER['REQUEST_URI'];
@endphp
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if (!empty($company->favicon))
            <link rel="icon" href="{{asset('storage/company/favicon/'.$company->favicon)}}" type="favicon">
        @endif
        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous"/>
        <link rel="stylesheet" href="{{asset('css/admin.css')}}"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <style>
            .icon-eye{
                position: absolute;
                right: 10px;
                top:55%;
                cursor: pointer;
                z-index: 2;
            }
        </style>
    </head>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            @include('admin.layout.sidebar')
            @include('admin.layout.menu')
            <main class="app-main">
                @yield('content')
            </main>
            @include('admin.layout.footer')
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{asset('js/admin.js')}}"></script>
        <script>
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $(document).ready(function() {
                toastr.options = {
                    "positionClass": "toast-bottom-right"
                }
                
                if ($('#contentSummernote').length) {
                    $('#contentSummernote').summernote({
                        height: 300
                    });
                }

                if ($('#imageUpload').length) {
                    document.getElementById('imageUpload').addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const imageUrl = e.target.result;
                                const imgElement = document.getElementById('imageContent'); 
                                imgElement.src = imageUrl; 
                                imgElement.style.display = 'block';
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }

                if ($('#submitForm').length) {
                    let urlSubmit = $('#submitForm').data('url-submit');
                    let urlComplete = $('#submitForm').data('url-complete');
                    $('#submitForm').on('submit', function(e){
                        e.preventDefault();
                        var formData = new FormData(this);
                        let timerInterval;
                        Swal.fire({
                            title: "Đang thực hiện",
                            timer: 8000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                                const timer = Swal.getPopup().querySelector("b");
                                timerInterval = setInterval(() => {
                                timer.textContent = `${Swal.getTimerLeft()}`;
                                }, 100);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        });
                        $.ajax({
                            url: urlSubmit,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false, 
                            success: function(response) {
                                setTimeout(function() {
                                    if (response.success == true) {
                                        Swal.fire({
                                            text: "Thành công!",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then((result) => {
                                            if(urlComplete != ""){
                                                location.href = urlComplete;
                                            }else{
                                                location.reload();
                                            }
                                        });
                                    }else{
                                        Swal.fire({
                                            text: response.message,
                                            icon: "error",
                                            showConfirmButton: false,
                                            timer: 2000
                                        });
                                    }
                                }, 1500);
                            },
                            error: function(xhr) {
                                console.log(xhr);
                            }
                        });
                    });
                }
            });

            function deleteItem(id,transaction,url){
                Swal.fire({
                    text: 'Bạn có muốn xóa '+transaction+'?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Huỷ bỏ'
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
                                    text: "Xóa thành công!",
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

            function changeStt(id,stt,url){
                $.ajax({
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    type: 'POST',
                    data: {
                        id: id,
                        stt: stt
                    },
                    success: function(response) {
                        Swal.fire({
                            text: "Cập nhật thành công!",
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
        </script>
    </body>
</html>