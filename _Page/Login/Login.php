<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            //Koneksi
            include "_Config/SettingGeneral.php";
        ?>

        <!-- METADATA -->
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>Login | <?php echo $title_page; ?></title>
        <meta content="<?php echo "$deskripsi"; ?>" name="description">
        <meta content="<?php echo "$kata_kunci"; ?>" name="keywords">

        <!-- FAVICON -->
        <link href="assets/img/Icon/<?php echo "$favicon"; ?>?v=<?php echo date('YmdHis'); ?>" rel="icon">
        <link href="assets/img/Icon/<?php echo "$favicon"; ?>?v=<?php echo date('YmdHis'); ?>" rel="apple-touch-icon">

        <!-- FONT -->
        <link href="assets/fonts/fonts.css" rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">

        <!-- Template Main CSS File -->
        <link href="assets/css/style.css?v=<?php echo date('YmdHis'); ?>" rel="stylesheet">
        <link href="node_modules/mdb-ui-kit/css/mdb.min.css" rel="stylesheet">
        

    </head>
    <body>
        <main class="landing_background">
            <div class="container">
                <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                                <img src="assets/img/Icon/<?php echo $logo;?>" alt="<?php echo $title_page;?>" width="100px">
                                <div class="d-flex justify-content-center py-2">
                                    <p>
                                        <a href="" class="logo d-flex align-items-center w-auto">
                                            <span class="d-none d-lg-block text-light"><?php echo $title_page;?></span>
                                        </a>
                                    </p>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="pb-2">
                                            <h5 class="card-title text-center pb-0 fs-4">Login Ke Akun Anda</h5>
                                            <p class="text-center small">Masukan Email Dan Password Untuk Melakukan Login</p>
                                        </div>
                                        <form action="javascript:void(0);" class="row g-3" id="ProsesLogin">
                                            <div class="col-12">
                                                <label for="email" class="form-label">Email</label>
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                    <input type="email" name="email" class="form-control" id="email" required>
                                                    <div class="invalid-feedback">Please enter your email.</div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text" id="inputGroupPrepend">
                                                        <i class="bi bi-key"></i>
                                                    </span>
                                                    <input type="password" name="password" id="password" class="form-control" required>
                                                    <div class="invalid-feedback">Please enter your password.</div>
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
                                            <div class="col-12">
                                                Pastikan email dan password sudah benar.
                                            </div>
                                            <div class="col-12" id="NotifikasiLogin"></div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary w-100" id="ButtonLogin">
                                                    <i class="bi bi-lock"></i> Login
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <p class="small mb-0">Anda Lupa Password? <a href="Login.php?Page=LupaPassword">Reset password</a></p>
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
        <script src="assets/js/main.js"></script>

        <script>
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
                var Loading='<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>';
                $('#NotifikasiLogin').html("Loading...");
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/Login/ProsesLogin.php',
                    data 	    :  ProsesLogin,
                    success     : function(data){
                        $('#NotifikasiLogin').html(data);
                        var NotifikasiProsesLoginBerhasil=$('#NotifikasiProsesLoginBerhasil').html();
                        if(NotifikasiProsesLoginBerhasil=="Success"){
                            window.location.href = "index.php";
                        }
                    }
                });
            });

            //Proses Kirim Tautan Lupa Password
            $('#ProsesLupaPassword').submit(function(){
                var ProsesLupaPassword = $('#ProsesLupaPassword').serialize();
                var Loading='<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>';
                $('#NotifikasiLupaPassword').html("Loading...");
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/ResetPassword/ProsesLupaPassword.php',
                    data 	    :  ProsesLupaPassword,
                    success     : function(data){
                        $('#NotifikasiLupaPassword').html(data);
                        var NotifikasiLupaPasswordBerhasil=$('#NotifikasiLupaPasswordBerhasil').html();
                        if(NotifikasiLupaPasswordBerhasil=="Success"){
                            //Tampilkan Swal Bahwa Proses Berhasil
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Kami telah mengirim tautan ke email anda',
                                icon: 'success',
                                confirmButtonText: 'Tutup'
                            }).then((result) => {
                                if (result.isConfirmed || result.dismiss === Swal.DismissReason.close) {
                                    window.location.href = 'Login.php';
                                }
                            });
                        }
                    }
                });
            });

            //Kondisi saat tampilkan password
            $('.form-check-input').click(function(){
                if($(this).is(':checked')){
                    $('#PasswordBaru1').attr('type','text');
                    $('#PasswordBaru2').attr('type','text');
                }else{
                    $('#PasswordBaru1').attr('type','password');
                    $('#PasswordBaru2').attr('type','password');
                }
            });
            $('#ProsesResetPassword').submit(function(){
                var ProsesResetPassword = $('#ProsesResetPassword').serialize();
                var Loading='<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>';
                $('#NotifikasiResetPassword').html("Loading...");
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/ResetPassword/ProsesResetPassword.php',
                    data 	    :  ProsesResetPassword,
                    success     : function(data){
                        $('#NotifikasiResetPassword').html(data);
                        var NotifikasiResetPasswordBerhasil=$('#NotifikasiResetPasswordBerhasil').html();
                        if(NotifikasiResetPasswordBerhasil=="Success"){
                            //Tampilkan Swal Bahwa Proses Berhasil
                            Swal.fire({
                                title: 'Ubah Password Berhasil',
                                text: 'Silahkan Login Menggunakan Password Baru Anda',
                                icon: 'success',
                                confirmButtonText: 'Tutup'
                            }).then((result) => {
                                if (result.isConfirmed || result.dismiss === Swal.DismissReason.close) {
                                    window.location.href = 'Login.php';
                                }
                            });
                        }
                    }
                });
            });
        </script>
    </body>
</html>