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

    // Validasi id_simpanan_reference
    if(empty($_POST['id_simpanan_reference'])){
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
    $id_simpanan_reference=validateAndSanitizeInput($_POST['id_simpanan_reference']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM simpanan_reference WHERE id_simpanan_reference = ? LIMIT 1");
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
    $Qry->bind_param("i", $id_simpanan_reference);
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
    $simpanan_nama       = htmlspecialchars($Data['simpanan_nama']);
    $simpanan_kategori   = htmlspecialchars($Data['simpanan_kategori']);
    $simpanan_keterangan = htmlspecialchars($Data['simpanan_keterangan']);
    $periode_pembayaran  = $Data['periode_pembayaran'] ?? '-';
    $status              = htmlspecialchars($Data['status']);
    
    // Routing Nominal
    if(empty($Data['nominal'])){
        $nominal = 0;
    }else{
        $nominal = htmlspecialchars($Data['nominal']);
    }
    // Nominal Rupiah
    $nominal_rupiah = "Rp " . number_format($nominal, 0, ',', '.');

    // Routing Status
    if($status==1){
        $label_status = '
            <span class="badge bg-success-subtle text-success">Active</span>
        ';
    }else{
        $label_status = '
            <span class="badge bg-danger-subtle text-danger">Inactive</span>
        ';
    }
   
    $Qry->close();
?>
    <input type="hidden" name="id_simpanan_reference" value="<?php echo $id_simpanan_reference; ?>">
    <div class="row mb-3">
        <div class="col-4"><small>Nama Simpanan</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$simpanan_nama"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Kategori</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$simpanan_kategori"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Keterangan</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$simpanan_keterangan"; ?>
            </small>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <div class="alert alert-danger">
                <small>
                    <b>PENTING!</b><br>
                    Menghapus jenis simpanan akan menghapus semua riwayat simpanan anggota yang sudah ada. <br>
                    <b>Apakah anda yakin akan menghapus data tersebut?</b>
                </small>
            </div>
        </div>
    </div>
    