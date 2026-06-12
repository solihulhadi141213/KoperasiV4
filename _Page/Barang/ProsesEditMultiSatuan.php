<?php
    header('Content-Type: application/json');

    require_once "../../_Config/Connection.php";
    require_once "../../_Config/Session.php";
    require_once "../../_Config/GlobalFunction.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi akses sudah berakhir.'
        ]);
        exit;
    }

    // Validasi Input
    $id_barang_satuan = isset($_POST['id_barang_satuan']) ? (int)$_POST['id_barang_satuan'] : 0;
    $satuan           = isset($_POST['satuan']) ? trim($_POST['satuan']) : '';
    $isi              = isset($_POST['isi']) ? (int)$_POST['isi'] : 0;

    if ($id_barang_satuan <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID Satuan tidak valid.'
        ]);
        exit;
    }

    if (empty($satuan)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nama satuan tidak boleh kosong.'
        ]);
        exit;
    }

    if ($isi <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Isi harus lebih besar dari nol.'
        ]);
        exit;
    }

    $satuan = strtoupper(validateAndSanitizeInput($satuan));

    // Cari data lama
    $query = "
        SELECT *
        FROM barang_satuan
        WHERE id_barang_satuan = ?
    ";

    $stmt = mysqli_prepare($Conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_barang_satuan);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$row = mysqli_fetch_assoc($result)) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Data tidak ditemukan.'
        ]);
        exit;
    }

    $id_barang = $row['id_barang'];

    mysqli_stmt_close($stmt);

    // Cek duplikasi
    $query = "
        SELECT id_barang_satuan
        FROM barang_satuan
        WHERE id_barang = ?
        AND satuan = ?
        AND id_barang_satuan != ?
    ";

    $stmt = mysqli_prepare($Conn, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "isi",
        $id_barang,
        $satuan,
        $id_barang_satuan
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Nama satuan sudah digunakan.'
        ]);
        exit;
    }

    mysqli_stmt_close($stmt);

    // Update Data
    $query = "
        UPDATE barang_satuan
        SET
            satuan = ?,
            isi = ?
        WHERE id_barang_satuan = ?
    ";

    $stmt = mysqli_prepare($Conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "sii",
        $satuan,
        $isi,
        $id_barang_satuan
    );

    if (mysqli_stmt_execute($stmt)) {

        echo json_encode([
            'status' => 'success',
            'message' => 'Data multi satuan berhasil diperbarui.',
            'id_barang' => $id_barang
        ]);

    } else {

        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memperbarui data.'
        ]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($Conn);
    exit;
?>