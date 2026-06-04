<?php
header('Content-Type: application/json');

// =========================================================
// CONNECTION & SESSION
// =========================================================
include "../../_Config/Connection.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

// =========================================================
// VALIDASI SESSION
// =========================================================
if (empty($SessionIdAkses)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Sesi akses sudah berakhir. Silahkan login ulang."
    ]);
    exit;
}

// =========================================================
// VALIDASI METHOD
// =========================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status"  => "error",
        "message" => "Metode request tidak valid."
    ]);
    exit;
}

// =========================================================
// AMBIL DATA
// =========================================================
$kode           = $_POST['kode'] ?? '';
$nama           = $_POST['nama'] ?? '';
$kategori       = $_POST['kategori'] ?? '';
$harga_beli     = $_POST['harga_beli'] ?? '';
$harga_jual     = $_POST['harga_jual'] ?? '';
$stok           = $_POST['stok'] ?? '';
$stok_minimum   = $_POST['stok_minimum'] ?? '';
$satuan         = $_POST['satuan'] ?? '';

// =========================================================
// SANITASI DATA
// =========================================================
$kode           = trim(htmlspecialchars($kode));
$nama           = trim(htmlspecialchars($nama));
$kategori       = trim(htmlspecialchars($kategori));
$satuan         = trim(htmlspecialchars($satuan));

$stok           = ($stok === '') ? 0 : (float)$stok;
$stok_minimum   = ($stok_minimum === '') ? 0 : (float)$stok_minimum;

// =========================================================
// FORMAT NOMINAL
// Mengubah 1.000.000 menjadi 1000000
// =========================================================
$harga_beli = str_replace('.', '', $harga_beli);
$harga_beli = str_replace(',', '.', $harga_beli);
$harga_beli = ($harga_beli == '') ? 0 : (float)$harga_beli;

$harga_jual = str_replace('.', '', $harga_jual);
$harga_jual = str_replace(',', '.', $harga_jual);
$harga_jual = ($harga_jual == '') ? 0 : (float)$harga_jual;

// =========================================================
// VALIDASI MANDATORY
// =========================================================
if (empty($kode)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Kode barang tidak boleh kosong."
    ]);
    exit;
}

if (empty($nama)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Nama barang tidak boleh kosong."
    ]);
    exit;
}

if (empty($kategori)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Kategori barang tidak boleh kosong."
    ]);
    exit;
}

if (empty($satuan)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Satuan barang tidak boleh kosong."
    ]);
    exit;
}

// =========================================================
// VALIDASI DUPLIKAT KODE BARANG
// =========================================================
$stmt = mysqli_prepare(
    $Conn,
    "SELECT id_barang 
     FROM barang 
     WHERE kode=?"
);

mysqli_stmt_bind_param($stmt, "s", $kode);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Kode barang tersebut sudah digunakan."
    ]);
    exit;
}

// =========================================================
// INSERT DATA
// =========================================================
$status = 1;

$query = "
    INSERT INTO barang (
        kode,
        nama,
        kategori,
        satuan,
        harga_beli,
        harga_jual,
        stok,
        stok_minimum,
        status
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
";

$stmt = mysqli_prepare($Conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "ssssddddi",
    $kode,
    $nama,
    $kategori,
    $satuan,
    $harga_beli,
    $harga_jual,
    $stok,
    $stok_minimum,
    $status
);

$execute = mysqli_stmt_execute($stmt);

if ($execute) {

    echo json_encode([
        "status"  => "success",
        "message" => "Data barang berhasil disimpan."
    ]);

} else {

    echo json_encode([
        "status"  => "error",
        "message" => "Gagal menyimpan data barang."
    ]);

}
exit;
?>