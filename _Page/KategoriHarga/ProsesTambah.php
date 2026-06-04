<?php
    header('Content-Type: application/json');

    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Response default
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

    // Ambil Data
    $kategori_harga = trim($_POST['kategori_harga'] ?? '');
    $keterangan     = trim($_POST['keterangan'] ?? '');

    // Validasi Mandatory
    if (empty($kategori_harga)) {
        $response["message"] = "Kategori harga tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    // Sanitasi Data
    $kategori_harga = htmlspecialchars($kategori_harga, ENT_QUOTES, 'UTF-8');
    $keterangan     = htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8');

    // Validasi Duplikat
    $check = $Conn->prepare("
        SELECT id_barang_kategori_harga
        FROM barang_kategori_harga
        WHERE kategori_harga = ?
        LIMIT 1
    ");

    if (!$check) {
        $response["message"] = "Gagal mempersiapkan query validasi data.";
        echo json_encode($response);
        exit;
    }

    $check->bind_param("s", $kategori_harga);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $response["message"] = "Kategori harga tersebut sudah terdaftar.";
        echo json_encode($response);
        $check->close();
        exit;
    }

    $check->close();

    // Simpan Data
    $insert = $Conn->prepare("
        INSERT INTO barang_kategori_harga (
            kategori_harga,
            keterangan
        ) VALUES (
            ?, ?
        )
    ");

    if (!$insert) {
        $response["message"] = "Gagal mempersiapkan query simpan data.";
        echo json_encode($response);
        exit;
    }

    $insert->bind_param(
        "ss",
        $kategori_harga,
        $keterangan
    );

    if ($insert->execute()) {
        $response["status"]  = "success";
        $response["message"] = "Kategori harga berhasil disimpan.";
    } else {
        $response["message"] = "Gagal menyimpan kategori harga. " . $insert->error;
    }

    $insert->close();
    $Conn->close();

    echo json_encode($response);
    exit;
?>
