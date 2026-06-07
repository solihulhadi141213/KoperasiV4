<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION
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
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($_POST['id_barang_diskon'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Diskon tidak boleh kosong."
        ]);
        exit;
    }

    if (!isset($_POST['diskon']) || $_POST['diskon'] === '') {
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
    // SANITASI
    // =========================================================
    $id_barang_diskon = validateAndSanitizeInput($_POST['id_barang_diskon']);
    $diskon           = validateAndSanitizeInput($_POST['diskon']);
    $datetime_start   = validateAndSanitizeInput($_POST['datetime_start']);
    $datetime_end     = validateAndSanitizeInput($_POST['datetime_end']);

    // =========================================================
    // VALIDASI NUMERIK
    // =========================================================
    if (!is_numeric($id_barang_diskon)) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Diskon tidak valid."
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
    if ($datetime_start > $datetime_end) {
        echo json_encode([
            "status"  => "error",
            "message" => "Tanggal mulai tidak boleh lebih besar dari tanggal berakhir."
        ]);
        exit;
    }

    // =========================================================
    // CEK DATA
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
        echo json_encode([
            "status"  => "error",
            "message" => "Data diskon tidak ditemukan."
        ]);
        exit;
    }

    $stmt->close();

    // =========================================================
    // UPDATE
    // =========================================================
    $stmt = $Conn->prepare("
        UPDATE barang_diskon
        SET
            diskon=?,
            datetime_start=?,
            datetime_end=?
        WHERE id_barang_diskon=?
    ");

    if (!$stmt) {
        echo json_encode([
            "status"  => "error",
            "message" => $Conn->error
        ]);
        exit;
    }

    $stmt->bind_param(
        "dssi",
        $diskon,
        $datetime_start,
        $datetime_end,
        $id_barang_diskon
    );

    if (!$stmt->execute()) {

        echo json_encode([
            "status"  => "error",
            "message" => "Gagal update data. ".$stmt->error
        ]);

        $stmt->close();
        exit;
    }

    $stmt->close();

    // =========================================================
    // RESPONSE
    // =========================================================
    echo json_encode([
        "status"  => "success",
        "message" => "Data diskon berhasil diperbarui."
    ]);
    exit;
?>