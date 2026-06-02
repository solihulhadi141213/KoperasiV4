<?php
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    header('Content-Type: application/json');

    // VALIDASI SESSION
    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir. Silahkan login ulang."
        ]);
        exit;
    }

    // VALIDASI DATA MANDATORY
    if(empty($_POST['id_simpanan_reference'])){
        echo json_encode([
            "status"  => "error",
            "message" => "ID Referensi Jenis Simpanan Tidak Boleh Kosong!"
        ]);
        exit;
    }
    if(empty($_POST['simpanan_nama'])){
        echo json_encode([
            "status"  => "error",
            "message" => "Nama Jenis Simpanan Tidak Boleh Kosong!"
        ]);
        exit;
    }
    if(empty($_POST['simpanan_kategori'])){
        echo json_encode([
            "status"  => "error",
            "message" => "Kategori Simpanan Tidak Boleh Kosong!"
        ]);
        exit;
    }
    
    // SANITASI INPUT
    $id_simpanan_reference = validateAndSanitizeInput($_POST['id_simpanan_reference']);
    $simpanan_nama         = trim(validateAndSanitizeInput($_POST['simpanan_nama']));
    $simpanan_kategori     = trim(validateAndSanitizeInput($_POST['simpanan_kategori']));
    $periode_pembayaran    = trim($_POST['periode_pembayaran'] ?? '');
    $simpanan_keterangan   = trim($_POST['simpanan_keterangan'] ?? '');
    $nominal               = trim($_POST['nominal'] ?? '');

   // VALIDASI KATEGORI
    $kategori_valid = ['Pokok', 'Wajib', 'Sukarela'];

    if (!in_array($simpanan_kategori, $kategori_valid)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Kategori simpanan tidak valid."
        ]);
        exit;
    }

    // ROUTING SIMPANAN SUKARELA
    if ($simpanan_kategori == 'Sukarela') {
        $periode_pembayaran = null;
        $nominal_decimal    = null;
    } else {

        // Validasi Nominal
        if (empty($nominal)) {
            echo json_encode([
                "status"  => "error",
                "message" => "Nominal simpanan wajib diisi."
            ]);
            exit;
        }

        // Ubah format uang menjadi decimal
        // 1.000.000 -> 1000000
        $nominal_decimal = str_replace('.', '', $nominal);
        $nominal_decimal = str_replace(',', '.', $nominal_decimal);

        if (!is_numeric($nominal_decimal)) {
            echo json_encode([
                "status"  => "error",
                "message" => "Format nominal tidak valid."
            ]);
            exit;
        }

        $nominal_decimal = number_format((float)$nominal_decimal, 2, '.', '');

        // Validasi Periode
        if (empty($periode_pembayaran)) {
            echo json_encode([
                "status"  => "error",
                "message" => "Periode pembayaran wajib dipilih."
            ]);
            exit;
        }
    }

    // VALIDASI DUPLIKAT NAMA
    $QryDuplikat = $Conn->prepare("
        SELECT id_simpanan_reference
        FROM simpanan_reference
        WHERE simpanan_nama = ?
        AND id_simpanan_reference != ?
        LIMIT 1
    ");

    if (!$QryDuplikat) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat validasi data."
        ]);
        exit;
    }

    $QryDuplikat->bind_param(
        "si",
        $simpanan_nama,
        $id_simpanan_reference
    );

    $QryDuplikat->execute();
    $ResultDuplikat = $QryDuplikat->get_result();

    if ($ResultDuplikat->num_rows > 0) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nama simpanan tersebut sudah digunakan."
        ]);
        $QryDuplikat->close();
        exit;
    }

    $QryDuplikat->close();

    // UPDATE DATA
    $Update = $Conn->prepare("
        UPDATE simpanan_reference SET
            simpanan_nama = ?,
            simpanan_kategori = ?,
            simpanan_keterangan = ?,
            periode_pembayaran = ?,
            nominal = ?
        WHERE id_simpanan_reference = ?
    ");

    if (!$Update) {
        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat mempersiapkan query update."
        ]);
        exit;
    }

    $Update->bind_param(
        "ssssdi",
        $simpanan_nama,
        $simpanan_kategori,
        $simpanan_keterangan,
        $periode_pembayaran,
        $nominal_decimal,
        $id_simpanan_reference
    );

    if (!$Update->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal menyimpan perubahan. " . $Update->error
        ]);
        $Update->close();
        exit;
    }

    $Update->close();

    // RESPONSE SUCCESS
    echo json_encode([
        "status"  => "success",
        "message" => "Data jenis simpanan berhasil diperbarui."
    ]);
    exit;
?>