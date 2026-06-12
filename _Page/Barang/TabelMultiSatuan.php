<?php
    require_once "../../_Config/Connection.php";
    require_once "../../_Config/Session.php";
    require_once "../../_Config/GlobalFunction.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small>Sesi Akses Sudah Berakhir! Silakan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

    // Validasi ID Barang
    if (empty($_POST['id_barang'])) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small>ID Barang Tidak Boleh Kosong</small>
                </td>
            </tr>
        ';
        exit;
    }

    $id_barang = (int) validateAndSanitizeInput($_POST['id_barang']);

    /* =========================================================
    AMBIL STOK BARANG
    ========================================================= */
    $query = "SELECT stok, satuan FROM barang WHERE id_barang = ?";
    $stmt  = mysqli_prepare($Conn, $query);

    if (!$stmt) {
        die("Prepare Failed: " . mysqli_error($Conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id_barang);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small>Data Barang Tidak Ditemukan</small>
                </td>
            </tr>
        ';
        exit;
    }

    $data_barang = mysqli_fetch_assoc($result);
    $stok = (float) $data_barang['stok'];
    $satuan_utama = $data_barang['satuan'];

    mysqli_stmt_close($stmt);

    /* =========================================================
    AMBIL DATA MULTI SATUAN
    ========================================================= */
    $query = "
        SELECT
            id_barang_satuan,
            satuan,
            isi
        FROM barang_satuan
        WHERE id_barang = ?
        ORDER BY isi DESC
    ";

    $stmt = mysqli_prepare($Conn, $query);

    if (!$stmt) {
        die("Prepare Failed: " . mysqli_error($Conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id_barang);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small>Data Multi Satuan Tidak Tersedia</small>
                </td>
            </tr>
        ';
        exit;
    }

    /* =========================================================
    TAMPILKAN DATA
    ========================================================= */
    $no = 1;

    while ($row = mysqli_fetch_assoc($result)) {

        $id_barang_satuan = $row['id_barang_satuan'];
        $satuan           = $row['satuan'];
        $isi              = (float) $row['isi'];

        // Hindari pembagian nol
        $stok_multi_satuan = ($isi > 0)
            ? floor($stok / $isi)
            : 0;

        echo '
            <tr>
                <td class="text-center">
                    <small>' . $no . '</small>
                </td>

                <td>
                    <small>' . htmlspecialchars($satuan) . '</small>
                </td>

                <td>
                    <small>' . number_format($isi, 0, ',', '.') . '</small>
                </td>

                <td>
                    <small>' . number_format($stok_multi_satuan, 0, ',', '.') . ' '.$satuan_utama.'</small>
                </td>

                <td class="text-center">

                    <button type="button" class="btn btn-sm btn-warning btn-floating edit-multi-satuan" data-id="' . $id_barang_satuan . '" title="Edit Satuan">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-danger btn-floating delete-multi-satuan" data-id="' . $id_barang_satuan . '" title="Hapus Satuan">
                        <i class="bi bi-trash"></i>
                    </button>

                </td>
            </tr>
        ';

        $no++;
    }

    mysqli_stmt_close($stmt);
?>