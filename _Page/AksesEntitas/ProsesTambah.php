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

    // Validasi Sesi Akses
    if (empty($SessionIdAkses)) {
        $response = [
            'status'  => 'error',
            'message' => 'Sesi Akses Sudah Berakhir! Silahkan login ulang.'
        ];

        echo json_encode($response);
        exit;
    }

    // Validasi akses tidak boleh kosong
    if (empty($_POST['akses'])) {
        $response = [
            'status'  => 'error',
            'message' => 'Level/Entitas Akses Tidak Boleh Kosong'
        ];

        echo json_encode($response);
        exit;
    }

    // Validasi Keterangan
    if (empty($_POST['keterangan'])) {
        $response = [
            'status'  => 'error',
            'message' => 'Keterangan Entitas Akses Tidak Boleh Kosong! Setidaknya anda menjelaskan tentang entitas akses tersebut.'
        ];

        echo json_encode($response);
        exit;
    }

    // Create variabel And Sanitasi
    $akses      = validateAndSanitizeInput($_POST['akses']);
    $keterangan = validateAndSanitizeInput($_POST['keterangan']);

    // =========================================================
    // VALIDASI DUPLIKAT ENTITAS
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "SELECT akses FROM akses_entitas WHERE akses = ?"
    );

    if (!$stmt) {
        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat validasi data.'
        ];

        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $akses);

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
    // BUAT UUID
    // =========================================================
    $uuid = GenerateToken(32);

    // =========================================================
    // SIMPAN KE DATABASE
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "INSERT INTO akses_entitas (
            uuid_akses_entitas,
            akses,
            keterangan
        ) VALUES (
            ?, ?, ?
        )"
    );

    if (!$stmt) {
        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat insert data ke database'
        ];

        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $uuid,
        $akses,
        $keterangan
    );

    $Input = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    // Jika Gagal Input
    if (!$Input) {
        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat insert data ke database'
        ];

        echo json_encode($response);
        exit;
    }

    // Berhasil
    $response = [
        'status'  => 'success',
        'message' => 'Entitas Akses Baru Berhasil Ditambahkan!'
    ];

    echo json_encode($response);
    exit;
?>