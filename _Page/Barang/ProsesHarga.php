<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";
include "../../_Config/Session.php";
include "../../_Config/GlobalFunction.php";

/* =========================================================
   1. VALIDASI SESSION
========================================================= */
if (empty($SessionIdAkses)) {
    echo json_encode([
        "status" => "error",
        "message" => "Sesi akses sudah berakhir"
    ]);
    exit;
}

/* =========================================================
   2. VALIDASI INPUT
========================================================= */
if (empty($_POST['id_barang'])) {
    echo json_encode([
        "status" => "error",
        "message" => "ID barang tidak valid"
    ]);
    exit;
}

$id_barang = (int) $_POST['id_barang'];

/* =========================================================
   3. HARGA JUAL (STANDAR)
========================================================= */
$harga_jual = $_POST['harga_jual'] ?? '0';
$harga_jual = str_replace(['.', ','], ['', '.'], $harga_jual);
$harga_jual = (float) $harga_jual;

/* =========================================================
   4. ARRAY HARGA KATEGORI
========================================================= */
$harga_kategori = $_POST['harga_kategori'] ?? [];

if (!is_array($harga_kategori)) {
    echo json_encode([
        "status" => "error",
        "message" => "Data harga tidak valid"
    ]);
    exit;
}

/* =========================================================
   5. TRANSACTION START
========================================================= */
$Conn->begin_transaction();

try {

    /* =====================================================
       LOOP MULTI HARGA
    ===================================================== */
    $cek = $Conn->prepare("
        SELECT id_barang_harga 
        FROM barang_harga 
        WHERE id_barang = ? AND id_barang_kategori_harga = ?
        LIMIT 1
    ");

    $update = $Conn->prepare("
        UPDATE barang_harga 
        SET harga = ? 
        WHERE id_barang_harga = ?
    ");

    $insert = $Conn->prepare("
        INSERT INTO barang_harga 
        (id_barang_kategori_harga, id_barang, harga)
        VALUES (?, ?, ?)
    ");

    foreach ($harga_kategori as $id_kategori => $harga) {

        $id_kategori = (int) $id_kategori;

        $harga = str_replace(['.', ','], ['', '.'], $harga);
        $harga = (float) $harga;

        /* CEK EXIST */
        $cek->bind_param("ii", $id_barang, $id_kategori);
        $cek->execute();
        $result = $cek->get_result();

        if ($result && $result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $id_harga = (int) $row['id_barang_harga'];

            $update->bind_param("di", $harga, $id_harga);
            $update->execute();

        } else {

            $insert->bind_param("iid", $id_kategori, $id_barang, $harga);
            $insert->execute();
        }
    }

    $cek->close();
    $update->close();
    $insert->close();

    /* =====================================================
       UPDATE HARGA JUAL BARANG
    ===================================================== */
    $stmt = $Conn->prepare("
        UPDATE barang 
        SET harga_jual = ? 
        WHERE id_barang = ?
    ");

    $stmt->bind_param("di", $harga_jual, $id_barang);
    $stmt->execute();
    $stmt->close();

    /* =====================================================
       COMMIT
    ===================================================== */
    $Conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Harga berhasil disimpan"
    ]);

} catch (Exception $e) {

    $Conn->rollback();

    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyimpan data: " . $e->getMessage()
    ]);
}