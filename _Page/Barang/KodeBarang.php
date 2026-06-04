<?php
header('Content-Type: application/json');

// =========================================================
// CONNECTION & SESSION
// =========================================================
include "../../_Config/Connection.php";
include "../../_Config/Session.php";

// =========================================================
// VALIDASI SESSION
// =========================================================
if (empty($SessionIdAkses)) {
    echo json_encode([
        "status" => "error",
        "message" => "Akses ditolak, session tidak ditemukan"
    ]);
    exit;
}

// =========================================================
// VALIDASI INPUT
// =========================================================
if (!isset($_POST['id_barang']) || empty($_POST['id_barang'])) {
    echo json_encode([
        "status" => "error",
        "message" => "ID barang tidak valid"
    ]);
    exit;
}

$id_barang = intval($_POST['id_barang']);

// =========================================================
// QUERY DATA BARANG
// =========================================================
$query = mysqli_query($Conn, "
    SELECT 
        id_barang,
        kode,
        nama,
        kategori
    FROM barang
    WHERE id_barang = '$id_barang'
    LIMIT 1
");

if (!$query) {
    echo json_encode([
        "status" => "error",
        "message" => "Query gagal: " . mysqli_error($Conn)
    ]);
    exit;
}

// =========================================================
// CEK DATA
// =========================================================
if (mysqli_num_rows($query) == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Data barang tidak ditemukan"
    ]);
    exit;
}

$data = mysqli_fetch_assoc($query);

// =========================================================
// RESPONSE SUCCESS
// =========================================================
echo json_encode([
    "status" => "success",
    "id_barang" => $data['id_barang'],
    "kode_barang" => $data['kode'],
    "nama_barang" => $data['nama'],
    "kategori" => $data['kategori']
]);