<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";
include "../../_Config/Session.php";
include "../../_Config/GlobalFunction.php";

date_default_timezone_set("Asia/Jakarta");

// SESSION
if (empty($SessionIdAkses)) {
    echo json_encode([
        "status" => "error",
        "message" => "Sesi akses sudah berakhir."
    ]);
    exit;
}

// VALIDASI
$id_barang_batch = validateAndSanitizeInput($_POST['id_barang_batch'] ?? '');
$id_barang       = validateAndSanitizeInput($_POST['id_barang'] ?? '');
$no_batch        = trim(validateAndSanitizeInput($_POST['no_batch'] ?? ''));
$qty_batch       = validateAndSanitizeInput($_POST['qty_batch'] ?? 0);
$expired_date    = validateAndSanitizeInput($_POST['expired_date'] ?? '');
$reminder_date   = validateAndSanitizeInput($_POST['reminder_date'] ?? '');
$status          = isset($_POST['status']) ? 1 : 0;

if (empty($id_barang_batch)) {
    echo json_encode([
        "status"=>"error",
        "message"=>"ID Batch tidak valid."
    ]);
    exit;
}

if (empty($no_batch)) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Nomor batch tidak boleh kosong."
    ]);
    exit;
}

if (empty($expired_date)) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Tanggal expired tidak boleh kosong."
    ]);
    exit;
}

// VALIDASI DUPLIKAT
$stmt = $Conn->prepare("
    SELECT id_barang_batch
    FROM barang_batch
    WHERE id_barang = ?
    AND no_batch = ?
    AND id_barang_batch != ?
    LIMIT 1
");

$stmt->bind_param(
    "isi",
    $id_barang,
    $no_batch,
    $id_barang_batch
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    echo json_encode([
        "status"=>"error",
        "message"=>"Nomor batch sudah digunakan."
    ]);
    exit;
}

$stmt->close();

// UPDATE
$update = $Conn->prepare("
    UPDATE barang_batch
    SET
        no_batch=?,
        qty_batch=?,
        expired_date=?,
        reminder_date=?,
        status=?
    WHERE id_barang_batch=?
");

$update->bind_param(
    "sdssii",
    $no_batch,
    $qty_batch,
    $expired_date,
    $reminder_date,
    $status,
    $id_barang_batch
);

if ($update->execute()) {

    echo json_encode([
        "status"    => "success",
        "message"   => "Data batch berhasil diperbarui.",
        "id_barang" => $id_barang
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Gagal memperbarui data batch.",
        "detail" => $update->error
    ]);
}

$update->close();
$Conn->close();