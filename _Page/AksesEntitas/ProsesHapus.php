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
            'message' => 'Sesi akses sudah berakhir! Silahkan login ulang.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI UUID
    // =========================================================
    if (empty($_POST['uuid_akses_entitas'])) {

        $response = [
            'status'  => 'error',
            'message' => 'ID Level/Entitas akses tidak boleh kosong.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // SANITASI INPUT
    // =========================================================
    $uuid_akses_entitas = validateAndSanitizeInput(
        $_POST['uuid_akses_entitas']
    );

    // =========================================================
    // VALIDASI DATA EXIST
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "SELECT 
            uuid_akses_entitas,
            akses
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

    $result = mysqli_stmt_get_result($stmt);

    // Jika data tidak ditemukan
    if (mysqli_num_rows($result) == 0) {

        mysqli_stmt_close($stmt);

        $response = [
            'status'  => 'error',
            'message' => 'Data entitas akses tidak ditemukan.'
        ];

        echo json_encode($response);
        exit;
    }

    $data  = mysqli_fetch_assoc($result);
    $akses = $data['akses'];

    mysqli_stmt_close($stmt);

    // =========================================================
    // CEK APAKAH MASIH DIGUNAKAN USER
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "SELECT COUNT(*) AS total
        FROM akses
        WHERE uuid_akses_entitas = ?"
    );

    if (!$stmt) {

        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat validasi pengguna.'
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

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    $jumlah_pengguna = (int)$row['total'];

    mysqli_stmt_close($stmt);

    // Jika masih digunakan user
    if ($jumlah_pengguna > 0) {

        $response = [
            'status'  => 'error',
            'message' => 'Level/Entitas akses masih digunakan oleh ' . $jumlah_pengguna . ' pengguna.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // HAPUS DATA
    // =========================================================
    $stmt = mysqli_prepare(
        $Conn,
        "DELETE FROM akses_entitas
        WHERE uuid_akses_entitas = ?"
    );

    if (!$stmt) {

        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat menghapus data.'
        ];

        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $uuid_akses_entitas
    );

    $Delete = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    // =========================================================
    // JIKA GAGAL DELETE
    // =========================================================
    if (!$Delete) {

        $response = [
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada saat menghapus data.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // BERHASIL
    // =========================================================
    $response = [
        'status'  => 'success',
        'message' => 'Level/Entitas akses ' . $akses . ' berhasil dihapus.'
    ];

    echo json_encode($response);
    exit;
?>