<?php
    header('Content-Type: application/json');

    // ==========================================================
    // CONNECTION & SESSION
    // ==========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    // ==========================================================
    // VALIDASI SESSION
    // ==========================================================
    if(empty($SessionIdAkses)){
        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir. Silakan login ulang."
        ]);
        exit;
    }

    // ==========================================================
    // AMBIL DATA
    // ==========================================================
    $nama_pinjaman     = trim($_POST['nama_pinjaman'] ?? '');
    $periode_angsuran  = trim($_POST['periode_angsuran'] ?? '');
    $persen_jasa       = trim($_POST['persen_jasa'] ?? '');
    $nominal_pinjaman  = trim($_POST['nominal_pinjaman'] ?? '');
    $denda_metode      = trim($_POST['denda_metode'] ?? '');
    $denda_nominal     = trim($_POST['denda_nominal'] ?? '');

    // ==========================================================
    // VALIDASI MANDATORY
    // ==========================================================
    if(empty($nama_pinjaman)){
        echo json_encode([
            "status"  => "error",
            "message" => "Nama pinjaman tidak boleh kosong."
        ]);
        exit;
    }

    if(empty($periode_angsuran)){
        echo json_encode([
            "status"  => "error",
            "message" => "Periode angsuran tidak boleh kosong."
        ]);
        exit;
    }

    if(empty($nominal_pinjaman)){
        echo json_encode([
            "status"  => "error",
            "message" => "Nominal pinjaman tidak boleh kosong."
        ]);
        exit;
    }

    // ==========================================================
    // NORMALISASI NOMINAL
    // ==========================================================
    $nominal_pinjaman = preg_replace('/[^0-9]/', '', $nominal_pinjaman);
    $denda_nominal    = preg_replace('/[^0-9]/', '', $denda_nominal);

    // ==========================================================
    // VALIDASI ANGKA
    // ==========================================================
    if(!is_numeric($periode_angsuran) || $periode_angsuran <= 0){
        echo json_encode([
            "status"  => "error",
            "message" => "Periode angsuran tidak valid."
        ]);
        exit;
    }

    if(!is_numeric($nominal_pinjaman) || $nominal_pinjaman <= 0){
        echo json_encode([
            "status"  => "error",
            "message" => "Nominal pinjaman tidak valid."
        ]);
        exit;
    }

    // ==========================================================
    // PERSEN JASA
    // ==========================================================
    if($persen_jasa === ''){
        $persen_jasa = 0;
    }

    if(!is_numeric($persen_jasa)){
        echo json_encode([
            "status"  => "error",
            "message" => "Persen jasa harus berupa angka."
        ]);
        exit;
    }

    // ==========================================================
    // VALIDASI DENDA
    // ==========================================================
    if(empty($denda_metode)){

        $denda_metode  = null;
        $denda_nominal = 0;

    }else{

        if(!in_array($denda_metode, ['Harian', 'Bulanan'])){
            echo json_encode([
                "status"  => "error",
                "message" => "Metode denda tidak valid."
            ]);
            exit;
        }

        if(empty($denda_nominal)){
            echo json_encode([
                "status"  => "error",
                "message" => "Nominal denda wajib diisi."
            ]);
            exit;
        }

        if(!is_numeric($denda_nominal) || $denda_nominal < 0){
            echo json_encode([
                "status"  => "error",
                "message" => "Nominal denda tidak valid."
            ]);
            exit;
        }
    }

    // ==========================================================
    // VALIDASI DUPLIKAT
    // ==========================================================
    $stmt = $Conn->prepare("
        SELECT id_pinjaman_jenis
        FROM pinjaman_jenis
        WHERE nama_pinjaman = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $nama_pinjaman);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $stmt->close();

        echo json_encode([
            "status"  => "error",
            "message" => "Nama pinjaman tersebut sudah terdaftar."
        ]);
        exit;
    }

    $stmt->close();

    // ==========================================================
    // STATUS DEFAULT
    // ==========================================================
    $status = 1;

    // ==========================================================
    // INSERT DATA
    // ==========================================================
    $stmt = $Conn->prepare("
        INSERT INTO pinjaman_jenis (
            nama_pinjaman,
            periode_angsuran,
            persen_jasa,
            nominal_pinjaman,
            denda_metode,
            denda_nominal,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?
        )
    ");

    if(!$stmt){
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan query database."
        ]);
        exit;
    }

    $stmt->bind_param(
        "sidisdi",
        $nama_pinjaman,
        $periode_angsuran,
        $persen_jasa,
        $nominal_pinjaman,
        $denda_metode,
        $denda_nominal,
        $status
    );

    if($stmt->execute()){

        echo json_encode([
            "status"  => "success",
            "message" => "Data jenis pinjaman berhasil disimpan."
        ]);

    }else{

        echo json_encode([
            "status"  => "error",
            "message" => "Terjadi kesalahan saat menyimpan data."
        ]);
    }

    $stmt->close();
?>