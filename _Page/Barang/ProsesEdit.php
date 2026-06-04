<?php
    header('Content-Type: application/json');

    // Connection & Session
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status" => "error",
            "message" => "Sesi akses sudah berakhir. Silahkan login ulang."
        ]);
        exit;
    }

    // Validasi ID
    if (empty($_POST['id_barang'])) {
        echo json_encode([
            "status" => "error",
            "message" => "ID Barang tidak valid."
        ]);
        exit;
    }

    // Sanitasi
    $id_barang      = (int) $_POST['id_barang'];
    $kode           = trim(htmlspecialchars($_POST['kode'] ?? ''));
    $nama           = trim(htmlspecialchars($_POST['nama'] ?? ''));
    $kategori       = trim(htmlspecialchars($_POST['kategori'] ?? ''));
    $satuan         = trim(htmlspecialchars($_POST['satuan'] ?? ''));

    $stok           = ($_POST['stok'] ?? '') == '' ? 0 : (float) $_POST['stok'];
    $stok_minimum   = ($_POST['stok_minimum'] ?? '') == '' ? 0 : (float) $_POST['stok_minimum'];

    // Format Rupiah
    $harga_beli = $_POST['harga_beli'] ?? '';
    $harga_beli = str_replace('.', '', $harga_beli);
    $harga_beli = str_replace(',', '.', $harga_beli);
    $harga_beli = $harga_beli == '' ? 0 : (float) $harga_beli;

    $harga_jual = $_POST['harga_jual'] ?? '';
    $harga_jual = str_replace('.', '', $harga_jual);
    $harga_jual = str_replace(',', '.', $harga_jual);
    $harga_jual = $harga_jual == '' ? 0 : (float) $harga_jual;

    // Validasi Mandatory
    if (empty($kode)) {
        echo json_encode([
            "status" => "error",
            "message" => "Kode barang tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($nama)) {
        echo json_encode([
            "status" => "error",
            "message" => "Nama barang tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($kategori)) {
        echo json_encode([
            "status" => "error",
            "message" => "Kategori barang tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($satuan)) {
        echo json_encode([
            "status" => "error",
            "message" => "Satuan barang tidak boleh kosong."
        ]);
        exit;
    }

    // Ambil Data Lama
    $stmt = mysqli_prepare(
        $Conn,
        "SELECT kode
        FROM barang
        WHERE id_barang=?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id_barang);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Data barang tidak ditemukan."
        ]);
        exit;
    }

    $data_lama = mysqli_fetch_assoc($result);
    $kode_lama = $data_lama['kode'];

    // Jika kode berubah lakukan validasi duplikat
    if ($kode != $kode_lama) {

        $stmt = mysqli_prepare(
            $Conn,
            "SELECT id_barang
            FROM barang
            WHERE kode=?"
        );

        mysqli_stmt_bind_param($stmt, "s", $kode);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            echo json_encode([
                "status" => "error",
                "message" => "Kode barang tersebut sudah digunakan."
            ]);
            exit;
        }
    }

    // Update
    $query = "
        UPDATE barang SET
            kode=?,
            nama=?,
            kategori=?,
            satuan=?,
            harga_beli=?,
            harga_jual=?,
            stok=?,
            stok_minimum=?
        WHERE id_barang=?
    ";

    $stmt = mysqli_prepare($Conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssddddi",
        $kode,
        $nama,
        $kategori,
        $satuan,
        $harga_beli,
        $harga_jual,
        $stok,
        $stok_minimum,
        $id_barang
    );

    if (mysqli_stmt_execute($stmt)) {

        echo json_encode([
            "status" => "success",
            "message" => "Data barang berhasil diperbarui."
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Gagal memperbarui data barang."
        ]);
    }
    exit;