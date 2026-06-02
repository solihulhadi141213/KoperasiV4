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
    if (empty($_POST['id_simpanan_reference'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Referensi Jenis Simpanan Tidak Boleh Kosong!"
        ]);
        exit;
    }

    // SANITASI
    $id_simpanan_reference = validateAndSanitizeInput(
        $_POST['id_simpanan_reference']
    );

    // CEK DATA
    $Qry = $Conn->prepare("
        SELECT
            id_simpanan_reference,
            simpanan_nama
        FROM simpanan_reference
        WHERE id_simpanan_reference = ?
        LIMIT 1
    ");

    if (!$Qry) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat mempersiapkan query database."
        ]);
        exit;
    }

    $Qry->bind_param("i", $id_simpanan_reference);

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
            "message" => "Data yang akan dihapus tidak ditemukan."
        ]);
        $Qry->close();
        exit;
    }

    $Data = $Result->fetch_assoc();
    $simpanan_nama = $Data['simpanan_nama'];

    $Qry->close();

    // HAPUS DATA
    $Delete = $Conn->prepare("
        DELETE FROM simpanan_reference
        WHERE id_simpanan_reference = ?
        LIMIT 1
    ");

    if (!$Delete) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat mempersiapkan proses hapus."
        ]);
        exit;
    }

    $Delete->bind_param(
        "i",
        $id_simpanan_reference
    );

    if (!$Delete->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal menghapus data. " . $Delete->error
        ]);
        $Delete->close();
        exit;
    }

    if ($Delete->affected_rows == 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Data gagal dihapus atau sudah tidak tersedia."
        ]);
        $Delete->close();
        exit;
    }

    $Delete->close();

    // RESPONSE SUCCESS
    echo json_encode([
        "status"  => "success",
        "message" => "Jenis simpanan \"$simpanan_nama\" berhasil dihapus."
    ]);
    exit;
?>