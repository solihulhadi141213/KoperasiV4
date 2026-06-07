<?php
    // =========================================================
    // CONNECTION, FUNCTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger">
                Sesi akses sudah berakhir. Silakan login ulang.
            </div>
        ';
        exit;
    }

    // =========================================================
    // VALIDASI ID
    // =========================================================
    if (empty($_POST['id_barang_diskon'])) {
        echo '
            <div class="alert alert-danger">
                ID Diskon tidak boleh kosong.
            </div>
        ';
        exit;
    }

    $id_barang_diskon = validateAndSanitizeInput($_POST['id_barang_diskon']);

    // =========================================================
    // BUKA DATA
    // =========================================================
    $Qry = $Conn->prepare("
        SELECT
            bd.id_barang_diskon,
            bd.id_barang,
            bd.diskon,
            bd.datetime_start,
            bd.datetime_end,
            b.kode,
            b.nama,
            b.kategori
        FROM barang_diskon bd
        LEFT JOIN barang b ON bd.id_barang=b.id_barang
        WHERE bd.id_barang_diskon=?
        LIMIT 1
    ");

    if(!$Qry){
        echo '
            <div class="alert alert-danger">
                Gagal mempersiapkan query.
            </div>
        ';
        exit;
    }

    $Qry->bind_param("i", $id_barang_diskon);

    if(!$Qry->execute()){
        echo '
            <div class="alert alert-danger">
                Gagal membuka data.
            </div>
        ';
        exit;
    }

    $Result = $Qry->get_result();

    if($Result->num_rows==0){
        echo '
            <div class="alert alert-danger">
                Data diskon tidak ditemukan.
            </div>
        ';
        exit;
    }

    $Data = $Result->fetch_assoc();

    $kode           = htmlspecialchars($Data['kode']);
    $nama           = htmlspecialchars($Data['nama']);
    $kategori       = htmlspecialchars($Data['kategori']);
    $diskon         = number_format($Data['diskon'],2,',','.');
    $datetime_start = date('d/m/Y',strtotime($Data['datetime_start']));
    $datetime_end   = date('d/m/Y',strtotime($Data['datetime_end']));

    $Qry->close();
?>

<input type="hidden" name="id_barang_diskon" value="<?php echo $id_barang_diskon; ?>">

<div class="row mb-2">
    <div class="col-4">
        <small class="text-muted">Kode</small>
    </div>
    <div class="col-8">
        <small><b><?php echo $kode; ?></b></small>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">
        <small class="text-muted">Nama Barang</small>
    </div>
    <div class="col-8">
        <small><b><?php echo $nama; ?></b></small>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">
        <small class="text-muted">Kategori</small>
    </div>
    <div class="col-8">
        <small><?php echo $kategori; ?></small>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4">
        <small class="text-muted">Diskon</small>
    </div>
    <div class="col-8">
        <small><?php echo $diskon; ?> %</small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-4">
        <small class="text-muted">Periode</small>
    </div>
    <div class="col-8">
        <small>
            <?php echo $datetime_start; ?>
            s/d
            <?php echo $datetime_end; ?>
        </small>
    </div>
</div>
 <div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>
                <b>PENTING!</b><br>
                Menghapus data diskon barang tersebut mungkin akan menyebabkan data riwayat transaksi keuangan bersangkutan akan ikut terhapus.<br>
                <b>Apakah anda yakin akan menghapus data diskon barang tersebut?</b>
            </small>
        </div>
    </div>
</div>