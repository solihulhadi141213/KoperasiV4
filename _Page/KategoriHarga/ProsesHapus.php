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

    // Validasi ID
    if (empty($_POST['id_barang_kategori_harga'])) {
        $response["message"] = "ID kategori harga tidak valid.";
        echo json_encode($response);
        exit;
    }

    // Sanitasi
    $id_barang_kategori_harga = validateAndSanitizeInput($_POST['id_barang_kategori_harga']);

    // Cek Data
    $Qry = $Conn->prepare("
        SELECT
            id_barang_kategori_harga,
            kategori_harga
        FROM barang_kategori_harga
        WHERE id_barang_kategori_harga = ?
        LIMIT 1
    ");

    if (!$Qry) {
        $response["message"] = "Terjadi kesalahan saat mempersiapkan query database.";
        echo json_encode($response);
        exit;
    }

    $Qry->bind_param("i", $id_barang_kategori_harga);

    if (!$Qry->execute()) {
        $response["message"] = "Terjadi kesalahan saat membuka data.";
        echo json_encode($response);
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        $response["message"] = "Data kategori harga tidak ditemukan.";
        echo json_encode($response);
        $Qry->close();
        exit;
    }

    $Data = $Result->fetch_assoc();
    $kategori_harga = $Data['kategori_harga'];

    $Qry->close();

    // Hapus Data
    $Delete = $Conn->prepare("
        DELETE FROM barang_kategori_harga
        WHERE id_barang_kategori_harga = ?
        LIMIT 1
    ");

    if (!$Delete) {
        $response["message"] = "Terjadi kesalahan saat mempersiapkan proses hapus.";
        echo json_encode($response);
        exit;
    }

    $Delete->bind_param("i", $id_barang_kategori_harga);

    if (!$Delete->execute()) {
        $response["message"] = "Gagal menghapus data. " . $Delete->error;
        echo json_encode($response);
        $Delete->close();
        exit;
    }

    if ($Delete->affected_rows == 0) {
        $response["message"] = "Data gagal dihapus atau sudah tidak tersedia.";
        echo json_encode($response);
        $Delete->close();
        exit;
    }

    $Delete->close();
    $Conn->close();

    // Response Success
    echo json_encode([
        "status"  => "success",
        "message" => "Kategori harga \"$kategori_harga\" berhasil dihapus."
    ]);
    exit;
?>
