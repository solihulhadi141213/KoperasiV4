<?php
    header('Content-Type: application/json');

    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Response Default
    $response = [
        "status"  => "error",
        "message" => "Terjadi kesalahan."
    ];

    // =====================================================
    // VALIDASI SESSION
    // =====================================================
    if (empty($SessionIdAkses)) {
        $response["message"] = "Sesi akses sudah berakhir. Silahkan login ulang.";
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // VALIDASI INPUT
    // =====================================================
    if (empty($_POST['id_anggota'])) {
        $response["message"] = "ID anggota tidak valid.";
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['tanggal_inactive'])) {
        $response["message"] = "Tanggal nonaktif wajib diisi.";
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['jam_inactive'])) {
        $response["message"] = "Jam nonaktif wajib diisi.";
        echo json_encode($response);
        exit;
    }

    $id_anggota       = validateAndSanitizeInput($_POST['id_anggota']);
    $tanggal_inactive = validateAndSanitizeInput($_POST['tanggal_inactive']);
    $jam_inactive     = validateAndSanitizeInput($_POST['jam_inactive']);

    // =====================================================
    // VALIDASI FORMAT TANGGAL
    // =====================================================
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_inactive)) {
        $response["message"] = "Format tanggal tidak valid.";
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // VALIDASI FORMAT JAM
    // =====================================================
    if (!preg_match('/^\d{2}:\d{2}$/', $jam_inactive)) {
        $response["message"] = "Format jam tidak valid.";
        echo json_encode($response);
        exit;
    }

    // Datetime Leave
    $datetime_leave = $tanggal_inactive . ' ' . $jam_inactive . ':00';

    // =====================================================
    // CEK DATA ANGGOTA
    // =====================================================
    $Qry = $Conn->prepare("
        SELECT
            id_anggota,
            nama,
            status
        FROM anggota
        WHERE id_anggota = ?
        LIMIT 1
    ");

    if (!$Qry) {
        $response["message"] = "Terjadi kesalahan saat mempersiapkan query.";
        echo json_encode($response);
        exit;
    }

    $Qry->bind_param("i", $id_anggota);

    if (!$Qry->execute()) {
        $response["message"] = "Terjadi kesalahan saat membaca data anggota.";
        echo json_encode($response);
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        $response["message"] = "Data anggota tidak ditemukan.";
        echo json_encode($response);
        exit;
    }

    $Data = $Result->fetch_assoc();

    $nama   = $Data['nama'];
    $status = $Data['status'];

    $Qry->close();

    // =====================================================
    // SUDAH INACTIVE?
    // =====================================================
    if ($status == "Inactive") {
        $response["message"] = "Anggota tersebut sudah berstatus non aktif.";
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // UPDATE STATUS
    // =====================================================
    $Update = $Conn->prepare("
        UPDATE anggota
        SET
            status = 'Inactive',
            datetime_leave = ?
        WHERE id_anggota = ?
    ");

    if (!$Update) {
        $response["message"] = "Terjadi kesalahan saat mempersiapkan update.";
        echo json_encode($response);
        exit;
    }

    $Update->bind_param(
        "si",
        $datetime_leave,
        $id_anggota
    );

    if (!$Update->execute()) {

        $response["message"] =
            "Terjadi kesalahan saat menyimpan data. " .
            $Update->error;

        $Update->close();
        echo json_encode($response);
        exit;
    }

    $Update->close();

    // =====================================================
    // SUCCESS
    // =====================================================
    $response["status"]  = "success";
    $response["message"] =
        "Anggota <b>" .
        htmlspecialchars($nama) .
        "</b> berhasil dinonaktifkan.";

    echo json_encode($response);
?>