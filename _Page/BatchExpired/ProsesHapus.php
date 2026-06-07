<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION, SESSION & FUNCTION
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
            "message" => "Sesi akses sudah berakhir. Silahkan login ulang."
        ]);
        exit;
    }

    // =========================================================
    // VALIDASI REQUEST
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
    $id_barang_batch = validateAndSanitizeInput($_POST['id_barang_batch'] ?? '');
    $id_barang       = validateAndSanitizeInput($_POST['id_barang'] ?? '');

    // =========================================================
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($id_barang_batch)) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Batch tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($id_barang)) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Barang tidak boleh kosong."
        ]);
        exit;
    }

    // =========================================================
    // CEK DATA BATCH
    // =========================================================
    $QryCheck = $Conn->prepare("
        SELECT
            id_barang_batch,
            id_barang,
            no_batch
        FROM barang_batch
        WHERE id_barang_batch = ?
        LIMIT 1
    ");

    if (!$QryCheck) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan query.",
            "detail"  => $Conn->error
        ]);
        exit;
    }

    $QryCheck->bind_param("i", $id_barang_batch);
    $QryCheck->execute();

    $ResultCheck = $QryCheck->get_result();

    if ($ResultCheck->num_rows == 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Data batch tidak ditemukan."
        ]);
        exit;
    }

    $DataBatch = $ResultCheck->fetch_assoc();

    $QryCheck->close();

    // =========================================================
    // HAPUS DATA
    // =========================================================
    $Delete = $Conn->prepare("
        DELETE FROM barang_batch
        WHERE id_barang_batch = ?
    ");

    if (!$Delete) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan query hapus.",
            "detail"  => $Conn->error
        ]);
        exit;
    }

    $Delete->bind_param("i", $id_barang_batch);

    // =========================================================
    // EKSEKUSI
    // =========================================================
    if ($Delete->execute()) {

        echo json_encode([
            "status"    => "success",
            "message"   => "Data batch berhasil dihapus.",
            "id_barang" => $id_barang
        ]);

    } else {

        echo json_encode([
            "status"  => "error",
            "message" => "Data batch gagal dihapus.",
            "detail"  => $Delete->error
        ]);
    }

    $Delete->close();
    $Conn->close();
?>