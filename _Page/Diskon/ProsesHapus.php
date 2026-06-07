<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION, FUNCTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

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
    // VALIDASI ID
    // =========================================================
    if (empty($_POST['id_barang_diskon'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Diskon tidak boleh kosong."
        ]);
        exit;
    }

    $id_barang_diskon = validateAndSanitizeInput($_POST['id_barang_diskon']);

    // =========================================================
    // CEK DATA
    // =========================================================
    $Qry = $Conn->prepare("
        SELECT id_barang_diskon
        FROM barang_diskon
        WHERE id_barang_diskon=?
        LIMIT 1
    ");

    if(!$Qry){
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan pada query database."
        ]);
        exit;
    }

    $Qry->bind_param("i", $id_barang_diskon);

    if(!$Qry->execute()){
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal membuka data diskon."
        ]);
        exit;
    }

    $Result = $Qry->get_result();

    if($Result->num_rows==0){
        echo json_encode([
            "status"  => "error",
            "message" => "Data diskon tidak ditemukan."
        ]);
        exit;
    }

    $Qry->close();

    // =========================================================
    // HAPUS DATA
    // =========================================================
    $Delete = $Conn->prepare("
        DELETE FROM barang_diskon
        WHERE id_barang_diskon=?
    ");

    if(!$Delete){
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan proses hapus."
        ]);
        exit;
    }

    $Delete->bind_param("i", $id_barang_diskon);

    if(!$Delete->execute()){
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal menghapus data. ".$Delete->error
        ]);
        exit;
    }

    $Delete->close();

    // =========================================================
    // RESPONSE
    // =========================================================
    echo json_encode([
        "status"  => "success",
        "message" => "Data diskon berhasil dihapus."
    ]);
?>