<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir, silahkan login ulang."
        ]);
        exit;
    }

    // =========================================================
    // AMBIL DATA
    // =========================================================
    $nama_supplier      = trim($_POST['nama_supplier'] ?? '');
    $kategori_supplier  = trim($_POST['kategori_supplier'] ?? '');
    $email_supplier     = trim($_POST['email_supplier'] ?? '');
    $kontak_supplier    = trim($_POST['kontak_supplier'] ?? '');
    $alamat_supplier    = trim($_POST['alamat_supplier'] ?? '');

    // =========================================================
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($nama_supplier)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nama supplier tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($kategori_supplier)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Kategori supplier tidak boleh kosong."
        ]);
        exit;
    }

    // =========================================================
    // SANITASI DATA
    // =========================================================
    $nama_supplier      = htmlspecialchars($nama_supplier, ENT_QUOTES, 'UTF-8');
    $kategori_supplier  = htmlspecialchars($kategori_supplier, ENT_QUOTES, 'UTF-8');
    $email_supplier     = htmlspecialchars($email_supplier, ENT_QUOTES, 'UTF-8');
    $kontak_supplier    = htmlspecialchars($kontak_supplier, ENT_QUOTES, 'UTF-8');
    $alamat_supplier    = htmlspecialchars($alamat_supplier, ENT_QUOTES, 'UTF-8');

    // =========================================================
    // VALIDASI EMAIL
    // =========================================================
    if (!empty($email_supplier) && !filter_var($email_supplier, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Format email tidak valid."
        ]);
        exit;
    }

    // =========================================================
    // VALIDASI DUPLIKAT NAMA SUPPLIER
    // =========================================================
    $stmt = $Conn->prepare("
        SELECT id_supplier
        FROM supplier
        WHERE nama_supplier = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $nama_supplier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nama supplier tersebut sudah terdaftar."
        ]);
        exit;
    }

    $stmt->close();

    // =========================================================
    // SIMPAN DATA
    // =========================================================
    $status = 1;

    $insert = $Conn->prepare("
        INSERT INTO supplier (
            nama_supplier,
            kategori_supplier,
            alamat_supplier,
            email_supplier,
            kontak_supplier,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )
    ");

    $insert->bind_param(
        "sssssi",
        $nama_supplier,
        $kategori_supplier,
        $alamat_supplier,
        $email_supplier,
        $kontak_supplier,
        $status
    );

    if ($insert->execute()) {

        echo json_encode([
            "status"  => "success",
            "message" => "Data supplier berhasil disimpan."
        ]);

    } else {

        echo json_encode([
            "status"  => "error",
            "message" => "Gagal menyimpan data supplier."
        ]);

    }

    $insert->close();
    $Conn->close();
?>