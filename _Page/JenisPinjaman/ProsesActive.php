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
    if (empty($_POST['id_pinjaman_jenis'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Referensi Jenis Pinjaman Tidak Boleh Kosong!"
        ]);
        exit;
    }

    // SANITASI
    $id_pinjaman_jenis = validateAndSanitizeInput(
        $_POST['id_pinjaman_jenis']
    );

    // CEK DATA
    $Qry = $Conn->prepare("
        SELECT id_pinjaman_jenis
        FROM pinjaman_jenis
        WHERE id_pinjaman_jenis = ?
        LIMIT 1
    ");

    if (!$Qry) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat mempersiapkan query."
        ]);
        exit;
    }

    $Qry->bind_param("i", $id_pinjaman_jenis);

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

    // ACTIVE
    $Update = $Conn->prepare("UPDATE pinjaman_jenis SET status = '1'WHERE id_pinjaman_jenis = ?");
    if (!$Update) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat mempersiapkan proses."
        ]);
        exit;
    }

    $Update->bind_param(
        "i",
        $id_pinjaman_jenis
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
        "message" => "Data jenis pinjaman berhasil diperbaharui."
    ]);
    exit;
?>