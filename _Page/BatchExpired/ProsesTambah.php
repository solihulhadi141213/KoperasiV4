<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION, SESSION & FUNCTION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";
    include "../../_Config/GlobalFunction.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir. Silahkan login ulang."
        ]);
        exit;
    }

    // =========================================================
    // VALIDASI POST
    // =========================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "status"  => "error",
            "message" => "Metode request tidak valid."
        ]);
        exit;
    }

    // =========================================================
    // AMBIL DATA
    // =========================================================
    $id_barang      = validateAndSanitizeInput($_POST['id_barang'] ?? '');
    $no_batch       = trim(validateAndSanitizeInput($_POST['no_batch'] ?? ''));
    $qty_batch      = validateAndSanitizeInput($_POST['qty_batch'] ?? '');
    $expired_date   = validateAndSanitizeInput($_POST['expired_date'] ?? '');
    $reminder_date  = validateAndSanitizeInput($_POST['reminder_date'] ?? '');
    $status         = isset($_POST['status']) ? 1 : 0;

    // =========================================================
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($id_barang)) {
        echo json_encode([
            "status"  => "error",
            "message" => "ID Barang tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($no_batch)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nomor batch tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($qty_batch)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Jumlah kuantitas barang tidak boleh kosong."
        ]);
        exit;
    }

    if (empty($expired_date)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Tanggal expired tidak boleh kosong."
        ]);
        exit;
    }
    if (empty($reminder_date)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Tanggal reminder tidak boleh kosong."
        ]);
        exit;
    }

    // =========================================================
    // VALIDASI BARANG
    // =========================================================
    $QryBarang = $Conn->prepare("
        SELECT id_barang
        FROM barang
        WHERE id_barang = ?
        LIMIT 1
    ");

    $QryBarang->bind_param("i", $id_barang);
    $QryBarang->execute();
    $ResultBarang = $QryBarang->get_result();

    if ($ResultBarang->num_rows == 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Data barang tidak ditemukan."
        ]);
        exit;
    }

    $QryBarang->close();

    // =========================================================
    // VALIDASI DUPLIKAT NOMOR BATCH
    // =========================================================
    $QryDuplikat = $Conn->prepare("
        SELECT id_barang_batch
        FROM barang_batch
        WHERE id_barang = ?
        AND no_batch = ?
        LIMIT 1
    ");

    $QryDuplikat->bind_param("is", $id_barang, $no_batch);
    $QryDuplikat->execute();
    $ResultDuplikat = $QryDuplikat->get_result();

    if ($ResultDuplikat->num_rows > 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nomor batch tersebut sudah terdaftar pada barang ini."
        ]);
        exit;
    }

    $QryDuplikat->close();

    // =========================================================
    // NORMALISASI DATA
    // =========================================================
    if ($qty_batch === '' || $qty_batch === null) {
        $qty_batch = 0;
    }

    if (empty($reminder_date)) {
        $reminder_date = null;
    }

    // =========================================================
    // SIMPAN DATA
    // =========================================================
    $Entry = $Conn->prepare("
        INSERT INTO barang_batch (
            id_barang,
            no_batch,
            qty_batch,
            expired_date,
            reminder_date,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )
    ");

    if (!$Entry) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan query.",
            "detail"  => $Conn->error
        ]);
        exit;
    }

    $Entry->bind_param(
        "isdssi",
        $id_barang,
        $no_batch,
        $qty_batch,
        $expired_date,
        $reminder_date,
        $status
    );

    if ($Entry->execute()) {

        echo json_encode([
            "status"    => "success",
            "message"   => "Data batch berhasil disimpan.",
            "id_barang" => $id_barang
        ]);

    } else {

        echo json_encode([
            "status"  => "error",
            "message" => "Gagal menyimpan data batch.",
            "detail"  => $Entry->error
        ]);
    }

    $Entry->close();
    $Conn->close();
?>