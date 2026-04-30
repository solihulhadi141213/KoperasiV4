<?php
    // TIME ZONE
    date_default_timezone_set('Asia/Jakarta');

    // HEADER JSON
    header('Content-Type: application/json');

    // CONNECTION, HELPER, SESSION
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // RESPONSE DEFAULT
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan.'
    ];

    // VALIDASI SESSION
    if (empty($SessionIdAkses)) {

        $response['message'] = 'Sesi akses sudah berakhir. Silakan login ulang.';

        echo json_encode($response);
        exit;
    }

    // VALIDASI METHOD
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        $response['message'] = 'Metode pengiriman data tidak valid.';

        echo json_encode($response);
        exit;
    }

    // VALIDASI MANDATORI
    $required = [
        'id_akses_fitur',
        'nama',
        'kategori',
        'kode'
    ];

    foreach ($required as $field) {

        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {

            $response['message'] = 'Masih ada data yang wajib diisi.';

            echo json_encode($response);
            exit;
        }
    }

    // SANITASI INPUT
    $id_akses_fitur = validateAndSanitizeInput($_POST['id_akses_fitur']);
    $nama           = validateAndSanitizeInput($_POST['nama']);
    $kategori       = validateAndSanitizeInput($_POST['kategori']);
    $kode           = validateAndSanitizeInput($_POST['kode']);
    $keterangan     = validateAndSanitizeInput($_POST['keterangan'] ?? '');

    // VALIDASI TIPE DATA
    if (!is_numeric($id_akses_fitur)) {

        $response['message'] = 'ID fitur tidak valid.';

        echo json_encode($response);
        exit;
    }

    // VALIDASI PANJANG KARAKTER
    if (strlen($nama) > 100) {

        $response['message'] = 'Nama fitur maksimal 100 karakter.';

        echo json_encode($response);
        exit;
    }

    if (strlen($kategori) > 50) {

        $response['message'] = 'Kategori fitur maksimal 50 karakter.';

        echo json_encode($response);
        exit;
    }

    if (strlen($kode) > 32) {

        $response['message'] = 'Kode fitur maksimal 32 karakter.';

        echo json_encode($response);
        exit;
    }

    // VALIDASI FORMAT KODE
    if (!preg_match('/^[A-Za-z0-9\_\-]+$/', $kode)) {

        $response['message'] = 'Kode fitur hanya boleh berisi huruf, angka, strip dan underscore.';

        echo json_encode($response);
        exit;
    }

    // VALIDASI DATA FITUR ADA
    $QryCheck = $Conn->prepare("
        SELECT id_akses_fitur 
        FROM akses_fitur 
        WHERE id_akses_fitur = ?
        LIMIT 1
    ");

    if (!$QryCheck) {

        $response['message'] = 'Gagal mempersiapkan query database.';

        echo json_encode($response);
        exit;
    }

    $QryCheck->bind_param("i", $id_akses_fitur);

    if (!$QryCheck->execute()) {

        $response['message'] = 'Terjadi kesalahan saat memvalidasi data fitur.';

        echo json_encode($response);
        $QryCheck->close();
        exit;
    }

    $ResultCheck = $QryCheck->get_result();

    if ($ResultCheck->num_rows == 0) {

        $response['message'] = 'Data fitur yang akan diubah tidak ditemukan.';

        echo json_encode($response);
        $QryCheck->close();
        exit;
    }

    $QryCheck->close();

    // VALIDASI DUPLIKAT KODE
    $QryDuplicate = $Conn->prepare("
        SELECT id_akses_fitur 
        FROM akses_fitur
        WHERE kode = ?
        AND id_akses_fitur != ?
        LIMIT 1
    ");

    if (!$QryDuplicate) {

        $response['message'] = 'Gagal mempersiapkan validasi kode fitur.';

        echo json_encode($response);
        exit;
    }

    $QryDuplicate->bind_param(
        "si",
        $kode,
        $id_akses_fitur
    );

    if (!$QryDuplicate->execute()) {

        $response['message'] = 'Terjadi kesalahan saat memvalidasi kode fitur.';

        echo json_encode($response);
        $QryDuplicate->close();
        exit;
    }

    $ResultDuplicate = $QryDuplicate->get_result();

    if ($ResultDuplicate->num_rows > 0) {

        $response['message'] = 'Kode fitur tersebut sudah digunakan.';

        echo json_encode($response);
        $QryDuplicate->close();
        exit;
    }

    $QryDuplicate->close();

    // UPDATE DATA
    $Update = $Conn->prepare("
        UPDATE akses_fitur 
        SET 
            nama       = ?,
            kategori   = ?,
            kode       = ?,
            keterangan = ?
        WHERE id_akses_fitur = ?
    ");

    if (!$Update) {

        $response['message'] = 'Gagal mempersiapkan proses update data.';

        echo json_encode($response);
        exit;
    }

    $Update->bind_param(
        "ssssi",
        $nama,
        $kategori,
        $kode,
        $keterangan,
        $id_akses_fitur
    );

    if (!$Update->execute()) {

        $response['message'] = 'Terjadi kesalahan saat menyimpan perubahan data.';

        echo json_encode($response);
        $Update->close();
        exit;
    }

    $Update->close();

    // RESPONSE SUCCESS
    $response = [
        'status'  => 'success',
        'message' => 'Data fitur berhasil diperbarui.'
    ];

    echo json_encode($response);
    exit;
?>