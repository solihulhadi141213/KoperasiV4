<?php
    // =========================================================
    // TIME ZONE
    // =========================================================
    date_default_timezone_set('Asia/Jakarta');

    // =========================================================
    // HEADER JSON
    // =========================================================
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION, HELPER, SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // =========================================================
    // DEFAULT RESPONSE
    // =========================================================
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan.'
    ];

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {

        $response['message'] = 'Sesi akses sudah berakhir. Silakan login ulang.';

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI REQUEST METHOD
    // =========================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        $response['message'] = 'Metode pengiriman data tidak valid.';

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI MANDATORI
    // =========================================================
    if (empty($_POST['id_akses_fitur'])) {

        $response['message'] = 'ID fitur tidak boleh kosong.';

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // SANITASI INPUT
    // =========================================================
    $id_akses_fitur = validateAndSanitizeInput($_POST['id_akses_fitur']);

    // =========================================================
    // VALIDASI TIPE DATA
    // =========================================================
    if (!is_numeric($id_akses_fitur)) {

        $response['message'] = 'Format ID fitur tidak valid.';

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI DATA FITUR
    // =========================================================
    $QryCheck = $Conn->prepare("
        SELECT 
            id_akses_fitur,
            nama
        FROM akses_fitur
        WHERE id_akses_fitur = ?
        LIMIT 1
    ");

    if (!$QryCheck) {

        $response['message'] = 'Gagal mempersiapkan query validasi data.';

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

    // =========================================================
    // DATA TIDAK DITEMUKAN
    // =========================================================
    if ($ResultCheck->num_rows == 0) {

        $response['message'] = 'Data fitur yang akan dihapus tidak ditemukan.';

        echo json_encode($response);

        $QryCheck->close();
        exit;
    }

    $DataFitur = $ResultCheck->fetch_assoc();

    $QryCheck->close();

    // =========================================================
    // CEK RELASI PADA akses_referensi
    // =========================================================
    $QryReferensi = $Conn->prepare("
        SELECT COUNT(*) AS total
        FROM akses_referensi
        WHERE id_akses_fitur = ?
    ");

    if (!$QryReferensi) {

        $response['message'] = 'Gagal mempersiapkan validasi referensi fitur.';

        echo json_encode($response);
        exit;
    }

    $QryReferensi->bind_param("i", $id_akses_fitur);

    if (!$QryReferensi->execute()) {

        $response['message'] = 'Terjadi kesalahan saat memvalidasi referensi fitur.';

        echo json_encode($response);

        $QryReferensi->close();
        exit;
    }

    $ResultReferensi = $QryReferensi->get_result();
    $DataReferensi   = $ResultReferensi->fetch_assoc();

    $totalReferensi = (int)($DataReferensi['total'] ?? 0);

    $QryReferensi->close();

    // =========================================================
    // CEK RELASI PADA akses_ijin
    // =========================================================
    $QryIjin = $Conn->prepare("
        SELECT COUNT(*) AS total
        FROM akses_ijin
        WHERE id_akses_fitur = ?
    ");

    if (!$QryIjin) {

        $response['message'] = 'Gagal mempersiapkan validasi izin fitur.';

        echo json_encode($response);
        exit;
    }

    $QryIjin->bind_param("i", $id_akses_fitur);

    if (!$QryIjin->execute()) {

        $response['message'] = 'Terjadi kesalahan saat memvalidasi izin fitur.';

        echo json_encode($response);

        $QryIjin->close();
        exit;
    }

    $ResultIjin = $QryIjin->get_result();
    $DataIjin   = $ResultIjin->fetch_assoc();

    $totalIjin = (int)($DataIjin['total'] ?? 0);

    $QryIjin->close();

    // =========================================================
    // VALIDASI MASIH DIGUNAKAN
    // =========================================================
    if ($totalReferensi > 0 || $totalIjin > 0) {

        $response['message'] = 'Fitur tidak dapat dihapus karena masih digunakan pada data referensi atau izin akses.';

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // HAPUS DATA
    // =========================================================
    $Delete = $Conn->prepare("
        DELETE FROM akses_fitur
        WHERE id_akses_fitur = ?
    ");

    if (!$Delete) {

        $response['message'] = 'Gagal mempersiapkan proses hapus data.';

        echo json_encode($response);
        exit;
    }

    $Delete->bind_param("i", $id_akses_fitur);

    if (!$Delete->execute()) {

        $response['message'] = 'Terjadi kesalahan saat menghapus data fitur.';

        echo json_encode($response);

        $Delete->close();
        exit;
    }

    $Delete->close();

    // =========================================================
    // RESPONSE SUCCESS
    // =========================================================
    $response = [
        'status'  => 'success',
        'message' => 'Data fitur berhasil dihapus.'
    ];

    echo json_encode($response);
    exit;
?>