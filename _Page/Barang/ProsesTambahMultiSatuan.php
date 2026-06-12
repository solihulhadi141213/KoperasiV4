<?php
    header('Content-Type: application/json');

    require_once "../../_Config/Connection.php";
    require_once "../../_Config/Session.php";
    require_once "../../_Config/GlobalFunction.php";

    /* =====================================================
    VALIDASI SESSION
    ===================================================== */
    if (empty($SessionIdAkses)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Sesi akses sudah berakhir. Silahkan login ulang.'
        ]);
        exit;
    }

    /* =====================================================
    VALIDASI INPUT
    ===================================================== */
    $id_barang   = isset($_POST['id_barang']) ? (int)$_POST['id_barang'] : 0;
    $satuan      = isset($_POST['satuan']) ? trim($_POST['satuan']) : '';
    $isi         = isset($_POST['isi']) ? (int)$_POST['isi'] : 0;

    if ($id_barang <= 0) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID Barang tidak valid.'
        ]);
        exit;
    }

    if (empty($satuan)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Nama satuan tidak boleh kosong.'
        ]);
        exit;
    }

    if ($isi <= 0) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Isi harus lebih besar dari 0.'
        ]);
        exit;
    }

    $satuan = validateAndSanitizeInput($satuan);

    /* =====================================================
    CEK BARANG
    ===================================================== */
    $query = "SELECT id_barang FROM barang WHERE id_barang = ? LIMIT 1";
    $stmt  = mysqli_prepare($Conn, $query);

    mysqli_stmt_bind_param($stmt, "i", $id_barang);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {

        mysqli_stmt_close($stmt);

        echo json_encode([
            'status'  => 'error',
            'message' => 'Data barang tidak ditemukan.'
        ]);
        exit;
    }

    mysqli_stmt_close($stmt);

    /* =====================================================
    CEK DUPLIKAT SATUAN
    ===================================================== */
    $query = "
        SELECT id_barang_satuan
        FROM barang_satuan
        WHERE id_barang = ?
        AND satuan = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($Conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $id_barang,
        $satuan
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        mysqli_stmt_close($stmt);

        echo json_encode([
            'status'  => 'error',
            'message' => 'Satuan tersebut sudah tersedia.'
        ]);
        exit;
    }

    mysqli_stmt_close($stmt);

    /* =====================================================
    SIMPAN DATA
    ===================================================== */
    $query = "
        INSERT INTO barang_satuan (
            id_barang,
            satuan,
            isi
        ) VALUES (?, ?, ?)
    ";

    $stmt = mysqli_prepare($Conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "isi",
        $id_barang,
        $satuan,
        $isi
    );

    if (mysqli_stmt_execute($stmt)) {

        echo json_encode([
            'status'  => 'success',
            'message' => 'Multi satuan berhasil ditambahkan.',
            'id_barang' => $id_barang
        ]);

    } else {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan saat menyimpan data.'
        ]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($Conn);
    exit;