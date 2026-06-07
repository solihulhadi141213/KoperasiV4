<?php
header('Content-Type: application/json');

// =========================================================
// CONNECTION & SESSION
// =========================================================
include "../../_Config/Connection.php";
include "../../_Config/Session.php";
include "../../_Config/GlobalFunction.php";

date_default_timezone_set("Asia/Jakarta");

// =========================================================
// VALIDASI SESSION
// =========================================================
if (empty($SessionIdAkses)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Sesi akses sudah berakhir. Silakan login ulang."
    ]);
    exit;
}

// =========================================================
// VALIDASI INPUT WAJIB
// =========================================================
if (empty($_POST['id_barang'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Barang tidak boleh kosong."
    ]);
    exit;
}

if ($_POST['diskon'] === '' || !isset($_POST['diskon'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Diskon tidak boleh kosong."
    ]);
    exit;
}

if (empty($_POST['datetime_start'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Tanggal mulai tidak boleh kosong."
    ]);
    exit;
}

if (empty($_POST['datetime_end'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Tanggal berakhir tidak boleh kosong."
    ]);
    exit;
}

// =========================================================
// SANITASI DATA
// =========================================================
$id_barang      = validateAndSanitizeInput($_POST['id_barang']);
$diskon         = validateAndSanitizeInput($_POST['diskon']);
$datetime_start = validateAndSanitizeInput($_POST['datetime_start']);
$datetime_end   = validateAndSanitizeInput($_POST['datetime_end']);

// =========================================================
// VALIDASI NUMERIK
// =========================================================
if (!is_numeric($id_barang)) {
    echo json_encode([
        "status"  => "error",
        "message" => "ID Barang tidak valid."
    ]);
    exit;
}

if (!is_numeric($diskon)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Nilai diskon harus berupa angka."
    ]);
    exit;
}

$diskon = (float)$diskon;

if ($diskon < 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Diskon tidak boleh kurang dari 0."
    ]);
    exit;
}

// =========================================================
// VALIDASI TANGGAL
// =========================================================
if (!strtotime($datetime_start)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Tanggal mulai tidak valid."
    ]);
    exit;
}

if (!strtotime($datetime_end)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Tanggal berakhir tidak valid."
    ]);
    exit;
}

if ($datetime_start > $datetime_end) {
    echo json_encode([
        "status"  => "error",
        "message" => "Tanggal mulai tidak boleh lebih besar dari tanggal berakhir."
    ]);
    exit;
}

// =========================================================
// VALIDASI BARANG ADA
// =========================================================
$stmt = $Conn->prepare("
    SELECT id_barang
    FROM barang
    WHERE id_barang=?
    LIMIT 1
");

if (!$stmt) {
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal mempersiapkan query barang."
    ]);
    exit;
}

$stmt->bind_param("i", $id_barang);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "message" => "Data barang tidak ditemukan."
    ]);
    exit;
}

$stmt->close();

// =========================================================
// VALIDASI DUPLIKAT DISKON AKTIF
// =========================================================
$stmt = $Conn->prepare("
    SELECT id_barang_diskon
    FROM barang_diskon
    WHERE id_barang=?
    LIMIT 1
");

if (!$stmt) {
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal mempersiapkan query validasi."
    ]);
    exit;
}

$stmt->bind_param("i", $id_barang);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "message" => "Barang tersebut sudah memiliki data diskon."
    ]);
    exit;
}

$stmt->close();

// =========================================================
// SIMPAN DATA
// =========================================================
$stmt = $Conn->prepare("
    INSERT INTO barang_diskon (
        id_barang,
        diskon,
        datetime_start,
        datetime_end
    ) VALUES (
        ?, ?, ?, ?
    )
");

if (!$stmt) {
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal mempersiapkan query simpan."
    ]);
    exit;
}

$stmt->bind_param(
    "idss",
    $id_barang,
    $diskon,
    $datetime_start,
    $datetime_end
);

if (!$stmt->execute()) {
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal menyimpan data. ".$stmt->error
    ]);
    $stmt->close();
    exit;
}

$id_barang_diskon = $stmt->insert_id;

$stmt->close();

// =========================================================
// RESPONSE SUCCESS
// =========================================================
echo json_encode([
    "status"            => "success",
    "message"           => "Data diskon berhasil ditambahkan.",
    "id_barang_diskon"  => $id_barang_diskon
]);
exit;
?>