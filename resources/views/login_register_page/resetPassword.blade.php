<!doctype html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   {{-- Font --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Vite  -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <title>Forget Password</title>
  </head>
  <body class="forget-password">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-4">
                    <div class="panel panel-default">
                         <div class="panel-body">
                                <div class="text-center">
                                    <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="car-key">
                                    <h2 class="text-center">Lupa Password?</h2>
                                    <p>Anda bisa mereset Password anda disini.</p>
                                    <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                                <input id="forgetAnswer" name="forgetAnswer" placeholder="Masukkan Email anda Disini" class="form-control"  type="text">
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <input name="btnForget" class="btn btn-lg btn-primary btn-block btnForget" value="Reset Password" type="submit">
                                        </div>

                                    </form>
                                        <a href="{{ url('/login') }}" class="btn btn-primary d-inline-flex align-items-center mt-2" onclick="event.preventDefault(); 
                                            if (history.length > 1) { history.back(); } 
                                            else { window.location.href=this.href; }">
                                        <i class="bi bi-arrow-left me-2"></i> Back
                                        </a>
                                </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>