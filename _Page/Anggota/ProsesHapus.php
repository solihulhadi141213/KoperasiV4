<?php
    header('Content-Type: application/json');

    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Response Default
    $response = [
        "status"  => "error",
        "message" => "Terjadi kesalahan."
    ];

    // Validasi Session
    if (empty($SessionIdAkses)) {
        $response["message"] = "Sesi akses sudah berakhir, silahkan login ulang.";
        echo json_encode($response);
        exit;
    }

    // Validasi ID
    if (empty($_POST['id_anggota'])) {
        $response["message"] = "ID Anggota tidak valid.";
        echo json_encode($response);
        exit;
    }

    // Sanitasi
    $id_anggota = validateAndSanitizeInput($_POST['id_anggota']);

    // Validasi Numerik
    if (!is_numeric($id_anggota)) {
        $response["message"] = "ID Anggota tidak valid.";
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // CEK DATA ANGGOTA
    // =====================================================
    $check = $Conn->prepare("
        SELECT
            id_anggota,
            nama
        FROM anggota
        WHERE id_anggota = ?
        LIMIT 1
    ");

    if (!$check) {
        $response["message"] = "Gagal mempersiapkan query database.";
        echo json_encode($response);
        exit;
    }

    $check->bind_param("i", $id_anggota);

    if (!$check->execute()) {
        $response["message"] = "Gagal membuka data anggota.";
        echo json_encode($response);
        exit;
    }

    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $response["message"] = "Data anggota tidak ditemukan.";
        echo json_encode($response);
        exit;
    }

    $data = $result->fetch_assoc();
    $nama = $data['nama'];

    $check->close();

    // =====================================================
    // HAPUS DATA
    // =====================================================
    $delete = $Conn->prepare("
        DELETE FROM anggota
        WHERE id_anggota = ?
        LIMIT 1
    ");

    if (!$delete) {
        $response["message"] = "Gagal mempersiapkan query hapus.";
        echo json_encode($response);
        exit;
    }

    $delete->bind_param("i", $id_anggota);

    if ($delete->execute()) {

        if ($delete->affected_rows > 0) {

            $response["status"]  = "success";
            $response["message"] = "Data anggota '$nama' berhasil dihapus.";

        } else {

            $response["message"] = "Tidak ada data yang dihapus.";

        }

    } else {

        $response["message"] = "Terjadi kesalahan saat menghapus data. ".$delete->error;

    }

    $delete->close();

    echo json_encode($response);
?>