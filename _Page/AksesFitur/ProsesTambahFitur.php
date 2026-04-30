<?php
    header('Content-Type: application/json');

    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Timezone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // Default response
    $response = [
        "status" => "error",
        "message" => "Unknown error"
    ];

    // Ambil & sanitize input
    $nama       = trim($_POST['nama'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? '');
    $kode       = trim($_POST['kode'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    // ================= VALIDASI =================
    if ($nama == '' || strlen($nama) > 100) {
        $response['message'] = "Nama fitur wajib diisi dan maksimal 100 karakter";
        echo json_encode($response);
        exit;
    }

    if ($kategori == '' || strlen($kategori) > 50) {
        $response['message'] = "Kategori wajib diisi dan maksimal 50 karakter";
        echo json_encode($response);
        exit;
    }

    if ($kode == '' || strlen($kode) < 6 || strlen($kode) > 32) {
        $response['message'] = "Kode harus 6 - 32 karakter";
        echo json_encode($response);
        exit;
    }

    if ($keterangan == '' || strlen($keterangan) > 500) {
        $response['message'] = "Keterangan wajib diisi dan maksimal 500 karakter";
        echo json_encode($response);
        exit;
    }

    // ================= CEK DUPLIKAT (PREPARED) =================

    // cek kode
    $stmt = $Conn->prepare("SELECT id_akses_fitur FROM akses_fitur WHERE kode = ?");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $response['message'] = "Kode sudah digunakan";
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    // cek nama
    $stmt = $Conn->prepare("SELECT id_akses_fitur FROM akses_fitur WHERE nama = ?");
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $response['message'] = "Nama fitur sudah digunakan";
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    // ================= INSERT DATA =================
    $stmt = $Conn->prepare("
        INSERT INTO akses_fitur (kategori, nama, kode, keterangan)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $kategori, $nama, $kode, $keterangan);

    $insert = $stmt->execute();
    $stmt->close();

    if ($insert) {
        $response['status'] = "success";
        $response['message'] = "Fitur berhasil ditambahkan";
    } else {
        $response['message'] = "Gagal menyimpan data";
    }

    echo json_encode($response);
    exit;