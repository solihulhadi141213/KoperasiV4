<?php
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    header('Content-Type: application/json');

    // VALIDASI SESSION
    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir. Silahkan login ulang."
        ]);
        exit;
    }

    // VALIDASI ID
    if (empty($_POST['id_supplier'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Referensi Supplier Tidak Boleh Kosong!"
        ]);
        exit;
    }

    // SANITASI
    $id_supplier = validateAndSanitizeInput(
        $_POST['id_supplier']
    );

    // CEK DATA
    $Qry = $Conn->prepare("
        SELECT id_supplier
        FROM supplier
        WHERE id_supplier = ?
        LIMIT 1
    ");

    if (!$Qry) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat mempersiapkan query."
        ]);
        exit;
    }

    $Qry->bind_param("i", $id_supplier);

    if (!$Qry->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat membuka data."
        ]);
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Data yang maksud tidak ditemukan."
        ]);
        $Qry->close();
        exit;
    }

    $Qry->close();

    // SOFT DELETE
    $Update = $Conn->prepare("
        UPDATE supplier
        SET status = '0'
        WHERE id_supplier = ?
    ");

    if (!$Update) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat mempersiapkan proses."
        ]);
        exit;
    }

    $Update->bind_param(
        "i",
        $id_supplier
    );

    if (!$Update->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal update data. ".$Update->error
        ]);
        $Update->close();
        exit;
    }

    $Update->close();

    // RESPONSE SUCCESS
    echo json_encode([
        "status"  => "success",
        "message" => "Data supplier berhasil diperbaharui."
    ]);
    exit;
?>