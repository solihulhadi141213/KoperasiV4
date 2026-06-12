<?php
    // Connecction, Session, dan Helper
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/Session.php";
    require_once "../../_Config/GlobalFunction.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi ID
    if (empty($_POST['id_barang_satuan'])) {
        echo '
            <div class="alert alert-danger">
                <small>Tidak ada data yang anda pilih!</small>
            </div>
        ';
        exit;
    }

    // Buat variabel dan sanitasi
    $id_barang_satuan = validateAndSanitizeInput((int) $_POST['id_barang_satuan']);
    
    // Siapkan query
    $query = "SELECT * FROM barang_satuan WHERE id_barang_satuan = ?";

    // Prepare statement
    $stmt = mysqli_prepare($Conn, $query);

    // Bind parameter
    mysqli_stmt_bind_param($stmt, "i", $id_barang_satuan);

    // Eksekusi query
    mysqli_stmt_execute($stmt);

    // Ambil hasil
    $result = mysqli_stmt_get_result($stmt);

    // Ambil 1 record
    $data = mysqli_fetch_assoc($result);

    // Jika Data Kosong
    if (empty($data)) {
        echo '
            <div class="alert alert-danger">
                <small>Data Multi Satuan Tidak Ditemukan</small>
            </div>
        ';
        exit;
    }

    // Jika Ditemukan maka Buatkan variabelnya
    $satuan = $data['satuan'];
    $isi    = $data['isi'];
?>
<input type="hidden" name="id_barang_satuan" value="<?php echo $id_barang_satuan; ?>">

<div class="row mb-3">
    <div class="col-12">
        <label for="satuan_edit">
            <small>Nama Satuan</small>
        </label>
        <input type="text" name="satuan" id="satuan_edit" class="form-control" value="<?php echo $satuan; ?>">
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="isi_edit">
            <small>Nama Satuan</small>
        </label>
        <input type="number" min="1" step="1" name="isi" id="isi_edit" class="form-control" value="<?php echo $isi; ?>">
    </div>
</div>
<script>
    // Enable Button 'ButtonEditMultiSatuan'
    $('#ButtonEditMultiSatuan').prop('disabled', false);
</script>
