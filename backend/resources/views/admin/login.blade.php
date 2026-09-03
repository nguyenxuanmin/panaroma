
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if (!empty($company->favicon))
            <link rel="icon" href="{{asset('storage/company/favicon/'.$company->favicon)}}" type="favicon">
        @endif
        <title>Đăng nhập</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous"/>
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    </head>
    <body class="login-page bg-body-secondary">
        <div class="login-box">
            <div class="login-logo">
                @if (!empty($company->favicon))
                    <img src="{{asset('storage/company/logo/'.$company->logo)}}" alt="{{$company->name}}" class="brand-image" />
                @else
                    <img src="{{asset('library/admin/AdminLTEFullLogo.png')}}" alt="AdminLTE Logo" class="brand-image" />
                @endif
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Log in to start the session.</p>
                    <form id="formLogIn">
                        <div class="input-group mb-3">
                            <input type="text" name="user_name" class="form-control" placeholder="Username" />
                            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" />
                            <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
                        </div>
                        <div class="d-grid gap-2 mb-2">
                            <button type="submit" class="btn btn-primary">Log In</button>
                        </div>
                    </form>
                    <p id="response" class="mb-1"></p>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
        <script src="{{ asset('js/admin.js') }}"></script>
        <script>
            $(document).ready(function(){
                $('#formLogIn').on('submit', function(e){
                    e.preventDefault();
                    var formData = $(this).serialize();
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '{{ route('login') }}',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success == true) {
                                location.href = '{{route('admin')}}';
                            }else{
                                $('#response').html('<p style="color: red; font-size:16px">' + response.message + '</p>');
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