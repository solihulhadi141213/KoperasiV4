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
?>
<input type="hidden" class="form-control" name="id_akses_fitur" value="<?php echo $id_akses_fitur; ?>">
<div class="row mb-3">
    <div class="col-md-12">
        <label for="nama_edit">Nama Fitur</label>
        <input type="text" class="form-control" name="nama" id="nama_edit" value="<?php echo $nama; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="kategori_edit">Kategori Fitur</label>
        <input type="text" class="form-control" name="kategori" id="kategori_edit" list="ListKategori2" value="<?php echo $kategori; ?>">
        <datalist id="ListKategori2">
            <?php
                $query = mysqli_query($Conn, "SELECT DISTINCT kategori FROM akses_fitur ORDER BY kategori ASC");
                while ($data = mysqli_fetch_array($query)) {
                    $kategori= $data['kategori'];
                    echo '<option value="'.$kategori.'">';
                }
            ?>
        </datalist>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="kode_edit">Kode Fitur</label>
        <div class="input-group">
            <input type="text" class="form-control kode_fitur" name="kode" id="kode_edit" value="<?php echo $kode; ?>">
            <button type="button" class="btn btn-dark generate_kode_fitur" title="Generate Kode">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="keterangan_edit">Keterangan</label>
        <textarea name="keterangan" id="keterangan_edit" class="form-control"><?php echo $keterangan; ?></textarea>
    </div>
</div>