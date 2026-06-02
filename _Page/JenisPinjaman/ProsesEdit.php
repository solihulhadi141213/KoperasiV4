<?php
    header('Content-Type: application/json');

    // ======================================================
    // CONNECTION & SESSION
    // ======================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    // ======================================================
    // VALIDASI SESSION
    // ======================================================
    if(empty($SessionIdAkses)){
        echo json_encode([
            "status" => "error",
            "message" => "Sesi akses sudah berakhir."
        ]);
        exit;
    }

    // ======================================================
    // VALIDASI ID
    // ======================================================
    if(empty($_POST['id_pinjaman_jenis'])){
        echo json_encode([
            "status" => "error",
            "message" => "ID Pinjaman tidak valid."
        ]);
        exit;
    }

    $id_pinjaman_jenis = (int)$_POST['id_pinjaman_jenis'];

    $nama_pinjaman    = trim($_POST['nama_pinjaman'] ?? '');
    $periode_angsuran = trim($_POST['periode_angsuran'] ?? '');
    $persen_jasa      = trim($_POST['persen_jasa'] ?? '');
    $nominal_pinjaman = trim($_POST['nominal_pinjaman'] ?? '');
    $denda_metode     = trim($_POST['denda_metode'] ?? '');
    $denda_nominal    = trim($_POST['denda_nominal'] ?? '');

    // ======================================================
    // VALIDASI
    // ======================================================
    if(empty($nama_pinjaman)){
        echo json_encode([
            "status"=>"error",
            "message"=>"Nama pinjaman tidak boleh kosong."
        ]);
        exit;
    }

    if(empty($periode_angsuran)){
        echo json_encode([
            "status"=>"error",
            "message"=>"Periode angsuran tidak boleh kosong."
        ]);
        exit;
    }

    if(empty($nominal_pinjaman)){
        echo json_encode([
            "status"=>"error",
            "message"=>"Nominal pinjaman tidak boleh kosong."
        ]);
        exit;
    }

    // ======================================================
    // FORMAT ANGKA
    // ======================================================
    $nominal_pinjaman = preg_replace('/[^0-9]/', '', $nominal_pinjaman);
    $denda_nominal    = preg_replace('/[^0-9]/', '', $denda_nominal);

    if(empty($persen_jasa)){
        $persen_jasa = 0;
    }

    // ======================================================
    // VALIDASI DENDA
    // ======================================================
    if(empty($denda_metode)){

        $denda_metode  = null;
        $denda_nominal = 0;

    }else{

        if(!in_array($denda_metode,['Harian','Bulanan'])){

            echo json_encode([
                "status"=>"error",
                "message"=>"Metode denda tidak valid."
            ]);
            exit;
        }

        if(empty($denda_nominal)){

            echo json_encode([
                "status"=>"error",
                "message"=>"Nominal denda wajib diisi."
            ]);
            exit;
        }
    }

    // ======================================================
    // CEK DUPLIKAT
    // ======================================================
    $stmt = $Conn->prepare("
        SELECT id_pinjaman_jenis
        FROM pinjaman_jenis
        WHERE nama_pinjaman = ?
        AND id_pinjaman_jenis != ?
    ");

    $stmt->bind_param(
        "si",
        $nama_pinjaman,
        $id_pinjaman_jenis
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

        echo json_encode([
            "status"=>"error",
            "message"=>"Nama pinjaman sudah digunakan."
        ]);

        $stmt->close();
        exit;
    }

    $stmt->close();

    // ======================================================
    // UPDATE
    // ======================================================
    $stmt = $Conn->prepare("
        UPDATE pinjaman_jenis SET
            nama_pinjaman=?,
            periode_angsuran=?,
            persen_jasa=?,
            nominal_pinjaman=?,
            denda_metode=?,
            denda_nominal=?
        WHERE id_pinjaman_jenis=?
    ");

    if(!$stmt){

        echo json_encode([
            "status"=>"error",
            "message"=>"Gagal mempersiapkan query."
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
        $id_pinjaman_jenis
    );

    if($stmt->execute()){

        echo json_encode([
            "status"=>"success",
            "message"=>"Data jenis pinjaman berhasil diperbarui."
        ]);

    }else{

        echo json_encode([
            "status"=>"error",
            "message"=>"Terjadi kesalahan saat memperbarui data."
        ]);
    }

    $stmt->close();
?>