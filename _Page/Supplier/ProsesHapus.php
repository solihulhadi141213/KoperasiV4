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
    if (empty($_POST['id_supplier'])) {
        $response["message"] = "ID Supplier tidak valid.";
        echo json_encode($response);
        exit;
    }

    // Sanitasi
    $id_supplier = validateAndSanitizeInput($_POST['id_supplier']);

    // Validasi Numerik
    if (!is_numeric($id_supplier)) {
        $response["message"] = "ID Supplier tidak valid.";
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // CEK DATA SUPPLIER
    // =====================================================
    $check = $Conn->prepare("
        SELECT
            id_supplier,
            nama_supplier
        FROM supplier
        WHERE id_supplier = ?
        LIMIT 1
    ");

    if (!$check) {
        $response["message"] = "Gagal mempersiapkan query database.";
        echo json_encode($response);
        exit;
    }

    $check->bind_param("i", $id_supplier);

    if (!$check->execute()) {
        $response["message"] = "Gagal membuka data supplier.";
        echo json_encode($response);
        exit;
    }

    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $response["message"] = "Data supplier tidak ditemukan.";
        echo json_encode($response);
        exit;
    }

    $data = $result->fetch_assoc();
    $nama_supplier = $data['nama_supplier'];

    $check->close();

    // =====================================================
    // HAPUS DATA
    // =====================================================
    $delete = $Conn->prepare("
        DELETE FROM supplier
        WHERE id_supplier = ?
        LIMIT 1
    ");

    if (!$delete) {
        $response["message"] = "Gagal mempersiapkan query hapus.";
        echo json_encode($response);
        exit;
    }

    $delete->bind_param("i", $id_supplier);

    if ($delete->execute()) {

        if ($delete->affected_rows > 0) {

            $response["status"]  = "success";
            $response["message"] = "Data supplier '$nama_supplier' berhasil dihapus.";

        } else {

            $response["message"] = "Tidak ada data yang dihapus.";

        }

    } else {

        $response["message"] = "Terjadi kesalahan saat menghapus data. ".$delete->error;

    }

    $delete->close();

    echo json_encode($response);
?>