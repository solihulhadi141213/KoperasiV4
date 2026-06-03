<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION, FUNCTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // RESPONSE DEFAULT
    // =========================================================
    $response = [
        "status"  => "error",
        "message" => "Terjadi kesalahan."
    ];

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {
        $response["message"] = "Sesi akses sudah berakhir, silahkan login ulang.";
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI DATA MANDATORY
    // =========================================================
    if (empty($_POST['id_supplier'])) {
        $response["message"] = "ID supplier tidak valid.";
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['nama_supplier'])) {
        $response["message"] = "Nama supplier tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['kategori_supplier'])) {
        $response["message"] = "Kategori supplier tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // AMBIL & SANITASI DATA
    // =========================================================
    $id_supplier       = validateAndSanitizeInput($_POST['id_supplier']);
    $nama_supplier     = trim($_POST['nama_supplier'] ?? '');
    $kategori_supplier = trim($_POST['kategori_supplier'] ?? '');
    $email_supplier    = trim($_POST['email_supplier'] ?? '');
    $kontak_supplier   = trim($_POST['kontak_supplier'] ?? '');
    $alamat_supplier   = trim($_POST['alamat_supplier'] ?? '');

    $nama_supplier     = htmlspecialchars($nama_supplier, ENT_QUOTES, 'UTF-8');
    $kategori_supplier = htmlspecialchars($kategori_supplier, ENT_QUOTES, 'UTF-8');
    $email_supplier    = htmlspecialchars($email_supplier, ENT_QUOTES, 'UTF-8');
    $kontak_supplier   = htmlspecialchars($kontak_supplier, ENT_QUOTES, 'UTF-8');
    $alamat_supplier   = htmlspecialchars($alamat_supplier, ENT_QUOTES, 'UTF-8');

    // =========================================================
    // VALIDASI EMAIL
    // =========================================================
    if (!empty($email_supplier) && !filter_var($email_supplier, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = "Format email tidak valid.";
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // CEK DATA SUPPLIER
    // =========================================================
    $check = $Conn->prepare("
        SELECT id_supplier, nama_supplier
        FROM supplier
        WHERE id_supplier = ?
        LIMIT 1
    ");

    if (!$check) {
        $response["message"] = "Gagal mempersiapkan query validasi supplier.";
        echo json_encode($response);
        exit;
    }

    $check->bind_param("i", $id_supplier);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $response["message"] = "Data supplier tidak ditemukan.";
        echo json_encode($response);
        $check->close();
        exit;
    }

    $data_lama = $result->fetch_assoc();
    $nama_supplier_lama = $data_lama['nama_supplier'];
    $check->close();

    // =========================================================
    // VALIDASI DUPLIKAT APABILA NAMA DIUBAH
    // =========================================================
    if ($nama_supplier !== $nama_supplier_lama) {
        $dup = $Conn->prepare("
            SELECT id_supplier
            FROM supplier
            WHERE nama_supplier = ?
            AND id_supplier != ?
            LIMIT 1
        ");

        if (!$dup) {
            $response["message"] = "Gagal mempersiapkan query validasi duplikat.";
            echo json_encode($response);
            exit;
        }

        $dup->bind_param("si", $nama_supplier, $id_supplier);
        $dup->execute();
        $dupResult = $dup->get_result();

        if ($dupResult->num_rows > 0) {
            $response["message"] = "Nama supplier tersebut sudah terdaftar.";
            echo json_encode($response);
            $dup->close();
            exit;
        }

        $dup->close();
    }

    // =========================================================
    // UPDATE DATA
    // =========================================================
    $update = $Conn->prepare("
        UPDATE supplier
        SET
            nama_supplier = ?,
            kategori_supplier = ?,
            alamat_supplier = ?,
            email_supplier = ?,
            kontak_supplier = ?
        WHERE id_supplier = ?
    ");

    if (!$update) {
        $response["message"] = "Gagal mempersiapkan query update.";
        echo json_encode($response);
        exit;
    }

    $update->bind_param(
        "sssssi",
        $nama_supplier,
        $kategori_supplier,
        $alamat_supplier,
        $email_supplier,
        $kontak_supplier,
        $id_supplier
    );

    if ($update->execute()) {
        $response["status"]  = "success";
        $response["message"] = "Data supplier berhasil diperbarui.";
    } else {
        $response["message"] = "Gagal memperbarui data supplier. " . $update->error;
    }

    $update->close();
    $Conn->close();

    echo json_encode($response);
    exit;
?>
