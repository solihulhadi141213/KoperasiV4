<?php
include "../../_Config/Connection.php";
include "../../_Config/Session.php";
include "../../_Config/GlobalFunction.php";

// =========================================================
// VALIDASI SESSION
// =========================================================
if (empty($SessionIdAkses)) {
?>
    <div class="alert alert-danger text-center">
        Sesi akses sudah berakhir. Silakan login ulang.
    </div>
<?php
    exit;
}

// =========================================================
// VALIDASI ID
// =========================================================
if (empty($_POST['id_barang_diskon'])) {
?>
    <div class="alert alert-danger text-center">
        ID Diskon tidak ditemukan.
    </div>
<?php
    exit;
}

$id_barang_diskon = validateAndSanitizeInput($_POST['id_barang_diskon']);

// =========================================================
// QUERY DATA
// =========================================================
$stmt = $Conn->prepare("
    SELECT *
    FROM barang_diskon
    WHERE id_barang_diskon=?
    LIMIT 1
");

$stmt->bind_param("i", $id_barang_diskon);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
?>
    <div class="alert alert-danger text-center">
        Data diskon tidak ditemukan.
    </div>
<?php
    exit;
}

$data = $result->fetch_assoc();

$id_barang      = $data['id_barang'];
$diskon         = $data['diskon'];
$datetime_start = $data['datetime_start'];
$datetime_end   = $data['datetime_end'];

$stmt->close();

// =========================================================
// DATA BARANG
// =========================================================
$stmtBarang = $Conn->prepare("
    SELECT kode,nama
    FROM barang
    WHERE id_barang=?
    LIMIT 1
");

$stmtBarang->bind_param("i", $id_barang);
$stmtBarang->execute();

$resultBarang = $stmtBarang->get_result();
$dataBarang   = $resultBarang->fetch_assoc();

$kode_barang = $dataBarang['kode'] ?? '';
$nama_barang = $dataBarang['nama'] ?? '';

$stmtBarang->close();
?>

<input type="hidden" name="id_barang_diskon" value="<?php echo $id_barang_diskon; ?>">

<div class="row mb-3">
    <div class="col-md-12">
        <label>
            <small>Barang</small>
        </label>
        <input
            type="text"
            class="form-control"
            value="<?php echo htmlspecialchars($nama_barang.' ('.$kode_barang.')'); ?>"
            readonly
        >
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="diskon_edit">
            <small>% Diskon <i class="bi bi-exclamation-circle"></i></small>
        </label>
        <input
            type="number"
            min="0"
            step="0.01"
            class="form-control"
            name="diskon"
            id="diskon_edit"
            value="<?php echo $diskon; ?>"
            required
        >
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="datetime_start_edit">
            <small>Tanggal Mulai <i class="bi bi-exclamation-circle"></i></small>
        </label>
        <input
            type="date"
            class="form-control"
            name="datetime_start"
            id="datetime_start_edit"
            value="<?php echo $datetime_start; ?>"
            required
        >
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="datetime_end_edit">
            <small>Tanggal Berakhir <i class="bi bi-exclamation-circle"></i></small>
        </label>
        <input
            type="date"
            class="form-control"
            name="datetime_end"
            id="datetime_end_edit"
            value="<?php echo $datetime_end; ?>"
            required
        >
    </div>
</div>