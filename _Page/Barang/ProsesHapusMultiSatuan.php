<?php
    header('Content-Type: application/json');

    require_once "../../_Config/Connection.php";
    require_once "../../_Config/Session.php";
    require_once "../../_Config/GlobalFunction.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses sudah berakhir.'
        ]);
        exit;
    }

    // Validasi ID
    $id_barang_satuan = isset($_POST['id_barang_satuan'])
        ? (int) $_POST['id_barang_satuan']
        : 0;

    if ($id_barang_satuan <= 0) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID satuan tidak valid.'
        ]);
        exit;
    }

    // Cek Data
    $query = "
        SELECT id_barang, id_barang_satuan
        FROM barang_satuan
        WHERE id_barang_satuan = ?
    ";

    $stmt = mysqli_prepare($Conn, $query);

    if (!$stmt) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal mempersiapkan query.'
        ]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_barang_satuan);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) == 0) {

        mysqli_stmt_close($stmt);

        echo json_encode([
            'status'  => 'error',
            'message' => 'Data tidak ditemukan.'
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    $id_barang = $row['id_barang'];

    mysqli_stmt_close($stmt);

    // Hapus Data
    $query = "
        DELETE FROM barang_satuan
        WHERE id_barang_satuan = ?
    ";

    $stmt = mysqli_prepare($Conn, $query);

    if (!$stmt) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal mempersiapkan query hapus.'
        ]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_barang_satuan);

    if (mysqli_stmt_execute($stmt)) {

        echo json_encode([
            'status'    => 'success',
            'message'   => 'Data multi satuan berhasil dihapus.',
            'id_barang' => $id_barang
        ]);

    } else {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menghapus data.'
        ]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($Conn);
    exit;
?>