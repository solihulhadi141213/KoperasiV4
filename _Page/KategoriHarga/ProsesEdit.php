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

    // Validasi Data Mandatory
    if (empty($_POST['id_barang_kategori_harga'])) {
        $response["message"] = "ID kategori harga tidak valid.";
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['kategori_harga'])) {
        $response["message"] = "Kategori harga tidak boleh kosong.";
        echo json_encode($response);
        exit;
    }

    // Ambil dan Sanitasi Data
    $id_barang_kategori_harga = validateAndSanitizeInput($_POST['id_barang_kategori_harga']);
    $kategori_harga           = trim($_POST['kategori_harga'] ?? '');
    $keterangan               = trim($_POST['keterangan'] ?? '');

    $kategori_harga = htmlspecialchars($kategori_harga, ENT_QUOTES, 'UTF-8');
    $keterangan     = htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8');

    // Cek Data Lama
    $check = $Conn->prepare("
        SELECT id_barang_kategori_harga, kategori_harga
        FROM barang_kategori_harga
        WHERE id_barang_kategori_harga = ?
        LIMIT 1
    ");

    if (!$check) {
        $response["message"] = "Gagal mempersiapkan query validasi data.";
        echo json_encode($response);
        exit;
    }

    $check->bind_param("i", $id_barang_kategori_harga);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $response["message"] = "Data kategori harga tidak ditemukan.";
        echo json_encode($response);
        $check->close();
        exit;
    }

    $data_lama = $result->fetch_assoc();
    $kategori_harga_lama = $data_lama['kategori_harga'];
    $check->close();

    // Validasi Duplikat Apabila Nama Kategori Diubah
    if ($kategori_harga !== $kategori_harga_lama) {
        $dup = $Conn->prepare("
            SELECT id_barang_kategori_harga
            FROM barang_kategori_harga
            WHERE kategori_harga = ?
            AND id_barang_kategori_harga != ?
            LIMIT 1
        ");

        if (!$dup) {
            $response["message"] = "Gagal mempersiapkan query validasi duplikat.";
            echo json_encode($response);
            exit;
        }

        $dup->bind_param("si", $kategori_harga, $id_barang_kategori_harga);
        $dup->execute();
        $dupResult = $dup->get_result();

        if ($dupResult->num_rows > 0) {
            $response["message"] = "Kategori harga tersebut sudah terdaftar.";
            echo json_encode($response);
            $dup->close();
            exit;
        }

        $dup->close();
    }

    // Update Data
    $update = $Conn->prepare("
        UPDATE barang_kategori_harga
        SET
            kategori_harga = ?,
            keterangan = ?
        WHERE id_barang_kategori_harga = ?
    ");

    if (!$update) {
        $response["message"] = "Gagal mempersiapkan query update data.";
        echo json_encode($response);
        exit;
    }

    $update->bind_param(
        "ssi",
        $kategori_harga,
        $keterangan,
        $id_barang_kategori_harga
    );

    if ($update->execute()) {
        $response["status"]  = "success";
        $response["message"] = "Kategori harga berhasil diperbarui.";
    } else {
        $response["message"] = "Gagal memperbarui kategori harga. " . $update->error;
    }

    $update->close();
    $Conn->close();

    echo json_encode($response);
    exit;
?>
