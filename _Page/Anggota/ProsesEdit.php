<?php
    header('Content-Type: application/json');

    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Response default
    $response = [
        "status" => "error",
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

    // Ambil Data
    $id_anggota      = validateAndSanitizeInput($_POST['id_anggota']);
    $nia             = validateAndSanitizeInput($_POST['nia'] ?? '');
    $nama            = validateAndSanitizeInput($_POST['nama'] ?? '');
    $kontak          = validateAndSanitizeInput($_POST['kontak'] ?? '');
    $email           = validateAndSanitizeInput($_POST['email'] ?? '');
    $organizationTag = validateAndSanitizeInput($_POST['organization_tag'] ?? '');
    $rankTag         = validateAndSanitizeInput($_POST['rank_tag'] ?? '');

    // Validasi Wajib
    if (empty($nia)) {
        $response["message"] = "Nomor Induk Anggota tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    if (empty($nama)) {
        $response["message"] = "Nama anggota tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    if (empty($organizationTag)) {
        $response["message"] = "Organization Tag tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    if (empty($rankTag)) {
        $response["message"] = "Rank Tag tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    // Rank wajib angka
    if (!preg_match('/^\d+$/', $rankTag)) {
        $response["message"] = "Rank Tag hanya boleh berisi angka.";
        echo json_encode($response);
        exit;
    }

    // Validasi Email
    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response["message"] = "Format email tidak valid.";
            echo json_encode($response);
            exit;
        }
    }

    // Cek Data Anggota Ada
    $check = $Conn->prepare("
        SELECT id_anggota
        FROM anggota
        WHERE id_anggota = ?
        LIMIT 1
    ");

    $check->bind_param("i", $id_anggota);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $response["message"] = "Data anggota tidak ditemukan.";
        echo json_encode($response);
        exit;
    }

    $check->close();

    // Cek Duplikasi NIA selain dirinya sendiri
    $dup = $Conn->prepare("
        SELECT id_anggota
        FROM anggota
        WHERE nia = ?
        AND id_anggota != ?
        LIMIT 1
    ");

    $dup->bind_param("si", $nia, $id_anggota);
    $dup->execute();

    $dupResult = $dup->get_result();

    if ($dupResult->num_rows > 0) {
        $response["message"] = "Nomor Induk Anggota (NIA) sudah digunakan.";
        echo json_encode($response);
        exit;
    }

    $dup->close();

    // Update Data
    $update = $Conn->prepare("
        UPDATE anggota
        SET
            nia = ?,
            nama = ?,
            kontak = ?,
            email = ?,
            organization_tag = ?,
            rank_tag = ?
        WHERE id_anggota = ?
    ");

    if (!$update) {
        $response["message"] = "Gagal mempersiapkan query update.";
        echo json_encode($response);
        exit;
    }

    $update->bind_param(
        "sssssii",
        $nia,
        $nama,
        $kontak,
        $email,
        $organizationTag,
        $rankTag,
        $id_anggota
    );

    if ($update->execute()) {

        $response["status"] = "success";
        $response["message"] = "Data anggota berhasil diperbarui.";

    } else {

        $response["message"] =
            "Terjadi kesalahan saat update data. " .
            $update->error;

    }

    $update->close();

    echo json_encode($response);
?>