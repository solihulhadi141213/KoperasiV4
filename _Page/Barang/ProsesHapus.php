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
    if (empty($_POST['id_barang'])) {
        $response["message"] = "ID Barang tidak valid.";
        echo json_encode($response);
        exit;
    }

    // Sanitasi
    $id_barang = validateAndSanitizeInput($_POST['id_barang']);

    // Validasi Numerik
    if (!is_numeric($id_barang)) {
        $response["message"] = "ID barang tidak valid.";
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // CEK DATA barang
    // =====================================================
    $check = $Conn->prepare("
        SELECT
            id_barang,
            nama
        FROM barang
        WHERE id_barang = ?
        LIMIT 1
    ");

    if (!$check) {
        $response["message"] = "Gagal mempersiapkan query database.";
        echo json_encode($response);
        exit;
    }

    $check->bind_param("i", $id_barang);

    if (!$check->execute()) {
        $response["message"] = "Gagal membuka data barang.";
        echo json_encode($response);
        exit;
    }

    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $response["message"] = "Data barang tidak ditemukan.";
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
        DELETE FROM barang
        WHERE id_barang = ?
        LIMIT 1
    ");

    if (!$delete) {
        $response["message"] = "Gagal mempersiapkan query hapus.";
        echo json_encode($response);
        exit;
    }

    $delete->bind_param("i", $id_barang);

    if ($delete->execute()) {

        if ($delete->affected_rows > 0) {

            $response["status"]  = "success";
            $response["message"] = "Data barang '$nama' berhasil dihapus.";

        } else {

            $response["message"] = "Tidak ada data yang dihapus.";

        }

    } else {

        $response["message"] = "Terjadi kesalahan saat menghapus data. ".$delete->error;

    }

    $delete->close();

    echo json_encode($response);
?>