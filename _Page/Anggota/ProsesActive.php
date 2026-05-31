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

    $id_anggota = validateAndSanitizeInput($_POST['id_anggota']);

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
        $Qry->close();
        echo json_encode($response);
        exit;
    }

    $Data = $Result->fetch_assoc();

    $nama   = $Data['nama'];
    $status = $Data['status'];

    $Qry->close();

    // =====================================================
    // SUDAH ACTIVE?
    // =====================================================
    if ($status == "Active") {
        $response["message"] = "Anggota tersebut sudah berstatus aktif.";
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // UPDATE STATUS
    // =====================================================
    $Update = $Conn->prepare("
        UPDATE anggota
        SET
            status = 'Active',
            datetime_leave = NULL
        WHERE id_anggota = ?
    ");

    if (!$Update) {
        $response["message"] = "Terjadi kesalahan saat mempersiapkan update.";
        echo json_encode($response);
        exit;
    }

    $Update->bind_param(
        "i",
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
        "</b> berhasil diaktifkan kembali.";

    echo json_encode($response);
?>