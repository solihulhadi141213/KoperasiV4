<?php
    // Connection, Function And Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Time Zone
    date_default_timezone_set("Asia/Jakarta");

    // Default Response
    $response = [
        'status'  => 'error',
        'message' => 'Tidak Ada Proses Yang Berjalan'
    ];

    // =========================================================
    // VALIDASI SESI
    // =========================================================
    if (empty($SessionIdAkses)) {
        $response = [
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan login ulang.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI INPUT
    // =========================================================
    if (empty($_POST['uuid_akses_entitas'])) {
        $response = [
            'status'  => 'error',
            'message' => 'ID Level/Entitas Tidak Boleh Kosong'
        ];

        echo json_encode($response);
        exit;
    }

    if (empty($_POST['akses'])) {
        $response = [
            'status'  => 'error',
            'message' => 'Akses Tidak Boleh Kosong'
        ];

        echo json_encode($response);
        exit;
    }

    if (empty($_POST['keterangan'])) {
        $response = [
            'status'  => 'error',
            'message' => 'Keterangan Entitas Akses Tidak Boleh Kosong! Setidaknya anda menjelaskan tentang entitas akses tersebut.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // SANITASI INPUT
    // =========================================================
    $uuid_akses_entitas = validateAndSanitizeInput($_POST['uuid_akses_entitas']);
    $akses              = validateAndSanitizeInput($_POST['akses']);
    $keterangan         = validateAndSanitizeInput($_POST['keterangan']);

    // =========================================================
    // VALIDASI DATA EXIST
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "SELECT uuid_akses_entitas 
        FROM akses_entitas 
        WHERE uuid_akses_entitas = ? 
        LIMIT 1"
    );

    if (!$stmt) {
        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat validasi data.'
        ];

        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $uuid_akses_entitas
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_store_result($stmt);

    $ValidasiID = mysqli_stmt_num_rows($stmt);

    mysqli_stmt_close($stmt);

    if (empty($ValidasiID)) {
        $response = [
            'status'  => 'error',
            'message' => 'Data entitas akses tidak ditemukan.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI DUPLIKAT AKSES
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "SELECT uuid_akses_entitas 
        FROM akses_entitas 
        WHERE akses = ?
        AND uuid_akses_entitas != ?"
    );

    if (!$stmt) {
        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat validasi duplikat data.'
        ];

        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $akses,
        $uuid_akses_entitas
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_store_result($stmt);

    $ValidasiAkses = mysqli_stmt_num_rows($stmt);

    mysqli_stmt_close($stmt);

    if (!empty($ValidasiAkses)) {
        $response = [
            'status'  => 'error',
            'message' => 'Entitas Akses ' . $akses . ' Tersebut Sudah Terdaftar Sebelumnya.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // UPDATE DATABASE
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "UPDATE akses_entitas 
        SET 
            akses = ?,
            keterangan = ?
        WHERE uuid_akses_entitas = ?"
    );

    if (!$stmt) {
        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat update data ke database'
        ];

        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $akses,
        $keterangan,
        $uuid_akses_entitas
    );

    $Update = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    // =========================================================
    // JIKA GAGAL UPDATE
    // =========================================================
    if (!$Update) {
        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat update data ke database'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // BERHASIL
    // =========================================================
    $response = [
        'status'  => 'success',
        'message' => 'Data Entitas Akses Berhasil Diupdate!'
    ];

    echo json_encode($response);
    exit;
?>