<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            //Koneksi
            include "_Config/SettingGeneral.php";

            // Menentukan Environment
            $environment = date('YmdHis');
        ?>

        <!-- METADATA -->
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>Login | <?php echo $title_page; ?></title>
        <meta content="<?php echo "$deskripsi"; ?>" name="description">
        <meta content="<?php echo "$kata_kunci"; ?>" name="keywords">

        <!-- FAVICON -->
        <link href="assets\img\Icon\<?php echo "$favicon"; ?>?v=<?php echo $environment; ?>" rel="icon">
        <link href="assets\img\Icon\<?php echo "$favicon"; ?>?v=<?php echo $environment; ?>" rel="apple-touch-icon">

        <!-- FONT -->
        <link href="assets/fonts/fonts.css" rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">

        <!-- Template Main CSS File -->
        <link href="assets/css/style.css?v=<?php echo $environment; ?>" rel="stylesheet">
        <link href="node_modules/mdb-ui-kit/css/mdb.min.css" rel="stylesheet">
        

    </head>
    <body>
        <main class="login_background">
            <div class="container">
                <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                                
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="pb-2 text-center">
                                            <img src="assets/img/Icon/<?php echo $logo;?>" alt="<?php echo $title_page;?>" width="100px">
                                            <h5 class="card-title text-center pb-0 fs-4">
                                                <a href=""><?php echo $title_page;?></a>
                                            </h5>
                                            <hr>
                                            <p class="text-center small">Please enter your email address and password.</p>
                                        </div>
                                        <form action="javascript:void(0);" class="row g-3" id="ProsesLogin" autocomplete="off">

                                            <!-- Email -->
                                            <div class="col-12 mb-2">
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                                                </div>
                                            </div>

                                            <!-- Password -->
                                            <div class="col-12 mb-2">
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text" id="inputGroupPrepend">
                                                        <i class="bi bi-key"></i>
                                                    </span>
                                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                                </div>
                                                <small class="credit">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Tampilkan" id="TampilkanPassword2" name="TampilkanPassword2">
                                                        <label class="form-check-label" for="TampilkanPassword2">
                                                            Tampilkan Password
                                                        </label>
                                                    </div>
                                                </small>
                                            </div>

                                            <!-- Captcha -->
                                            <div class="col-12 text-end">
                                                <img src="_Page/Login/Captcha.php" class="img img-fluid mb-2" id="captchaImg" width="100%">
                                                <a href="javascript:void(0);" id="reloadCaptcha" class="text text-primary">
                                                    <small><i class="bi bi-arrow-clockwise"></i> Reload Captcha</small>
                                                </a>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text" id="inputGroupPrepend">
                                                        <i class="bi bi-shield"></i>
                                                    </span>
                                                    <input type="text" name="captcha" id="captcha" class="form-control" placeholder="Captcha Here" required>
                                                </div>
                                            </div>

                                            <div class="col-12" id="NotifikasiLogin"></div>
                                            <div class="col-12 mb-2">
                                                <button type="submit" class="btn btn-lg btn-primary w-100" id="ButtonLogin">
                                                    <i class="bi bi-lock"></i> Login
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="credits text-center">
                                    <small>
                                        <div class="copyright text-white">
                                            &copy; Copyright <strong><span><?php echo "$title_page"; ?></span></strong>. All Rights Reserved 2023
                                        </div>
                                        <div class="credits text-white">
                                            Designed by <span class="text text-decoration-underline"><?php echo "$AuthorAplikasi"; ?></span>
                                        </div>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
        <!-- Vendor JS Files -->
        <script src="node_modules/jquery/dist/jquery.min.js"type="text/javascript"></script>
        <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
        <script src="node_modules/jquery/dist/jquery.min.js" type="text/javascript"></script>
        <script src="node_modules/jQuery-Mask-Plugin/dist/jquery.mask.min.js" type="text/javascript"></script>
        <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js" type="text/javascript"></script>

        <!-- Template Main JS File -->
        <script src="assets/js/main.js?v=<?php echo $environment; ?>"></script>

        <script>
           
            // Reload Captcha
            $('#reloadCaptcha').click(function(){
                $('#captchaImg').attr('src', '_Page/Login/Captcha.php?' + Date.now());
            });

            //Kondisi saat tampilkan password
            $('#TampilkanPassword2').click(function(){
                if($(this).is(':checked')){
                    $('#password').attr('type','text');
                }else{
                    $('#password').attr('type','password');
                }
            });

            //Submit Login
            $('#ProsesLogin').submit(function(){
                var ProsesLogin = $('#ProsesLogin').serialize();
                var ButtonLogin = $('#ButtonLogin').html();
                $('#ButtonLogin').html('<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>');
                $.ajax({
                    type    : 'POST',
                    url     : '_Page/Login/ProsesLogin.php',
                    data    : ProsesLogin,
                    dataType: 'JSON',
                    success     : function(response){
                        let status = response.status;
                        let message = response.message;

                        if(status=='success'){
                            $('#ButtonLogin').html(ButtonLogin);
                            $('#NotifikasiLogin').html('');
                            window.location.href = "index.php";
                        }else{
                            $('#NotifikasiLogin').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                            $('#ButtonLogin').html(ButtonLogin);
                        }
                    }
                });
            });

            
        </script>
    </body>
</html>