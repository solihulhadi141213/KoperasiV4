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
        <div class="alert alert-danger text-center">
            <small>
                Apakah anda yakin akan menghapus data satuan <b><?php echo $satuan; ?></b> Tersebut?
            </small>
        </div>
    </div>
</div>

<script>
    // Enable Button 'ButtonHapusMultiSatuan'
    $('#ButtonHapusMultiSatuan').prop('disabled', false);
</script>
