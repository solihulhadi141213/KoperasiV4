<?php
    // Time Zone
    date_default_timezone_set('Asia/Jakarta');

    // Connection, Function And Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Validasi Sesi Akses
    if(empty($SessionIdAkses)){
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opps</b><br> Sesi Akses Sudah Berakhir! Silahkan Login Ulang!
                </small>
            </div>
        ';
        exit;
    }

    // Validasi Mandatori
    if(empty($_POST['id_akses_fitur'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opps</b><br> ID Fitur Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    // Variable And Sanitazion
    $id_akses_fitur=validateAndSanitizeInput($_POST['id_akses_fitur']);

    // Open Data Form Database With Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM akses_fitur WHERE id_akses_fitur = ?");
    $Qry->bind_param("i", $id_akses_fitur);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opps</b><br> Terjadi kesalahan pada saat membuka data fitur dari database! <br>
                    <pre>' . htmlspecialchars($Conn->error) . '</pre>
                </small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data   = $Result->fetch_assoc();
    $Qry->close();

    //Creat Variable
    $nama       = $Data['nama'];
    $kategori   = $Data['kategori'];
    $kode       = $Data['kode'];
    $keterangan = $Data['keterangan'];

    echo '
        <input type="hidden" class="form-control" name="id_akses_fitur" value="'.$id_akses_fitur.'">
        <div class="row mb-2">
            <div class="col-4"><small>Nama Fitur</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">'.$nama.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kategori</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">'.$kategori.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Kode Fitur</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">'.$kode.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-4"><small>Keterangan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">'.$keterangan.'</small></div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <div class="alert alert-danger">
                    <small>
                        <b>PENTING!</b><br> 
                        Menghapus Fitur Bisa Menyebabkan Pengguna Aplikasi Tidak Bisa Lagi Melakukan Akses Pada Fitur Tersebut.<br>
                        <b>Apakah anda yakin akan menghapusnya?</b>
                    </small>
                </div>
            </div>
        </div>
    ';
?>
