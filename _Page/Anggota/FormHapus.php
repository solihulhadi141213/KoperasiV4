<?php
    
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Sesi Akses sudah berakhir! Silahkan Login Ulang.
                </small>
            </div>
        ';
        exit;
    }

    // Validasi id_anggota
    if(empty($_POST['id_anggota'])){
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Anda belum memilih data manapun
                </small>
            </div>
        ';
        exit;
    }

    // Variabel And Sanitazer
    $id_anggota=validateAndSanitizeInput($_POST['id_anggota']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM anggota WHERE id_anggota = ? LIMIT 1");
    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat mempersiapkan query database!<br>
                    Keterangan : ' . htmlspecialchars($Conn->error) . '
                </small>
            </div>
        ';
        exit;
    }
    $Qry->bind_param("i", $id_anggota);
    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat membuka data dari database!<br>
                    Keterangan : ' . htmlspecialchars($Qry->error) . '
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }
    $Result = $Qry->get_result();

    // Jika Tidak Ditemukan
    if ($Result->num_rows == 0) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Data tidak ditemukan!
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }
    $Data                = $Result->fetch_assoc();
    $nia                 = htmlspecialchars($Data['nia']);
    $nama                = htmlspecialchars($Data['nama']);
    $kontak              = htmlspecialchars($Data['kontak']);
    $email               = htmlspecialchars($Data['email']);
    $organization_tag    = htmlspecialchars($Data['organization_tag']);
    $rank_tag            = htmlspecialchars($Data['rank_tag']);
    $status              = htmlspecialchars($Data['status']);
    $datetime_registered = htmlspecialchars($Data['datetime_registered']);
    

    if(empty($kontak)){
        $kontak = "-";
    }
    if(empty($email)){
        $email = "-";
    }
    if(empty($Data['datetime_leave'])){
        $datetime_leave = "-";
    }else{
        $datetime_leave      = htmlspecialchars($Data['datetime_leave']);
    }
   
    $Qry->close();
?>
    <input type="hidden" name="id_anggota" value="<?php echo $id_anggota; ?>">
    <div class="row mb-3">
        <div class="col-6"><small>Nomor Induk Anggota</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$nia"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Nama Anggota</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$nama"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Nomor Kontak</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$kontak"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Email</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$email"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Organization Tag</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$organization_tag"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Rank Tag</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$rank_tag"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Status</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$status"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Tanggal Daftar</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$datetime_registered"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6"><small>Tanggal Keluar</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-5">
            <small class="text-grayish">
                <?php echo "$datetime_leave"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-danger text-center">
                <small>
                    <b>PENTING!</b><br>
                    Menghapus data anggota mungkin akan menyebabkan data riwayat transaksi keuangan bersangkutan akan ikut terhapus.<br>
                    <b>Apakah anda yakin akan menghapus anggota tersebut?</b>
                </small>
            </div>
        </div>
    </div>
    