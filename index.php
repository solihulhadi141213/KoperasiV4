<?php

    // Connection, Function Anda Session
    include "_Config/Connection.php";
    include "_Config/Helper.php";
    include "_Config/Session.php";

    if(empty($SessionIdAkses)){
        include "_Page/Login/Login.php";
        exit;
    }

    //Apabila Login Berrhasil
    include "_Config/SettingGeneral.php";
    include "_Config/Notifikasi.php";

    // Menentukan Environment
    $environment = date('YmdHis');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            // Init 'Page' Variabel
            $Page = !empty($_GET['Page']) ? $_GET['Page'] : "";

            // Routing Title By Page
            $list_halaman = [
                "MyProfile"           => "Profile Saya",
                "Profile"             => "Profile",
                "Setting"             => "Setting",
                "ApiKey"              => "API Key",
                "EmailGateway"        => "Email Gateway",
                "AksesFitur"          => "Fitur Aplikasi",
                "AksesEntitas"        => "Entitas Akses",
                "Akses"               => "Akses Pengguna",
                "Anggota"             => "Anggota",
                "JenisSimpanan"       => "Jenis Simpanan",
                "JenisPinjaman"       => "Jenis Pinjaman",
                "Supplier"            => "Supplier",
                "KategoriHarga"       => "Kategori Harga",
                "Barang"              => "Master Barang",
                "BatchExpired"        => "Batch & Expired",
                "Diskon"              => "Diskon",
                "SettingGeneral"      => "Pengaturan Umum",
                "SettingEmailGateway" => "Email Gateway",
            ];
            
            // Init Page Title
            $judul_halaman = isset($list_halaman[$Page]) ? $list_halaman[$Page] : "Dashboard";
        ?>

        <!-- METADATA -->
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title><?php echo "$judul_halaman | $title_page"; ?></title>
        <meta content="<?php echo "$deskripsi"; ?>" name="description">
        <meta content="<?php echo "$kata_kunci"; ?>" name="keywords">
        
        <!-- FAVICON -->
        <link href="assets/img/Icon/<?php echo "$favicon"; ?>" rel="icon">
        <link href="assets/img/Icon/<?php echo "$favicon"; ?>" rel="apple-touch-icon">
        <?php
            include "_Partial/Head.php";
        ?>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <header id="header" class="header fixed-top d-flex align-items-center nav_background">
            <div class="d-flex align-items-center justify-content-between">
                <a href="" class="logo d-flex align-items-center">
                    <img src="assets/img/Icon/<?php echo "$logo"; ?>" alt="">
                    <span class="d-none d-lg-block text-white"><?php echo "$title_page"; ?></span>
                </a>
                <i class="bi bi-list toggle-sidebar-btn text-white"></i>
            </div>
            <nav class="header-nav ms-auto">
                <ul class="d-flex align-items-center">
                    <?php
                        include "_Partial/Notifikasi.php";
                        include "_Partial/Profile.php";
                    ?>
                </ul>
            </nav>
        </header>
        <?php
            include "_Partial/Menu.php";
        ?>
        <main id="main" class="main flex-grow-1">
            <?php
                include "_Partial/RoutingPage.php";
                include "_Partial/Modal.php";
            ?>
        </main>
        <?php
            include "_Partial/Copyright.php";
            include "_Partial/FooterJs.php";
            include "_Partial/RoutingJs.php";
            include "_Partial/RoutingSwal.php";
        ?>
    </body>
</html>