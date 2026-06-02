<?php
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    header('Content-Type: application/json');

    // ======================================
    // VALIDASI SESSION
    // ======================================
    if(empty($SessionIdAkses)){
        echo json_encode([
            "status" => "error",
            "message" => "Sesi login berakhir. Silahkan login kembali."
        ]);
        exit;
    }

    // ======================================
    // AMBIL DATA
    // ======================================
    $simpanan_nama        = trim($_POST['simpanan_nama'] ?? '');
    $simpanan_kategori    = trim($_POST['simpanan_kategori'] ?? '');
    $periode_pembayaran   = trim($_POST['periode_pembayaran'] ?? '');
    $nominal              = trim($_POST['nominal'] ?? '');
    $simpanan_keterangan  = trim($_POST['simpanan_keterangan'] ?? '');

    $status = 1;

    // ======================================
    // VALIDASI MANDATORY
    // ======================================
    if(empty($simpanan_nama)){
        echo json_encode([
            "status" => "error",
            "message" => "Nama simpanan tidak boleh kosong."
        ]);
        exit;
    }

    if(empty($simpanan_kategori)){
        echo json_encode([
            "status" => "error",
            "message" => "Kategori simpanan tidak boleh kosong."
        ]);
        exit;
    }

    // ======================================
    // VALIDASI KATEGORI
    // ======================================
    $kategori_valid = ['Pokok','Wajib','Sukarela'];

    if(!in_array($simpanan_kategori, $kategori_valid)){
        echo json_encode([
            "status" => "error",
            "message" => "Kategori simpanan tidak valid."
        ]);
        exit;
    }

    // ======================================
    // NORMALISASI DATA BERDASARKAN KATEGORI
    // ======================================
    if($simpanan_kategori == 'Pokok'){

        $periode_pembayaran = 'Sekali';

        if(empty($nominal)){
            echo json_encode([
                "status" => "error",
                "message" => "Nominal simpanan pokok wajib diisi."
            ]);
            exit;
        }

    }elseif($simpanan_kategori == 'Wajib'){

        if(empty($periode_pembayaran)){
            echo json_encode([
                "status" => "error",
                "message" => "Periode pembayaran wajib dipilih."
            ]);
            exit;
        }

        if(!in_array($periode_pembayaran,['Bulan','Tahun'])){
            echo json_encode([
                "status" => "error",
                "message" => "Periode pembayaran tidak valid."
            ]);
            exit;
        }

        if(empty($nominal)){
            echo json_encode([
                "status" => "error",
                "message" => "Nominal simpanan wajib harus diisi."
            ]);
            exit;
        }

    }elseif($simpanan_kategori == 'Sukarela'){

        $periode_pembayaran = NULL;
        $nominal = NULL;
    }

    // ======================================
    // FORMAT NOMINAL
    // Hapus titik, koma, Rp, spasi
    // ======================================
    if(!empty($nominal)){
        $nominal = preg_replace('/[^0-9]/', '', $nominal);

        if(empty($nominal)){
            $nominal = 0;
        }
    }

    // ======================================
    // VALIDASI DUPLIKAT NAMA
    // ======================================
    $stmt = $Conn->prepare("
        SELECT id_simpanan_reference
        FROM simpanan_reference
        WHERE simpanan_nama = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $simpanan_nama);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo json_encode([
            "status" => "error",
            "message" => "Nama simpanan tersebut sudah digunakan."
        ]);
        exit;
    }

    // ======================================
    // INSERT DATA
    // ======================================
    $query = "
        INSERT INTO simpanan_reference (
            simpanan_nama,
            simpanan_kategori,
            simpanan_keterangan,
            periode_pembayaran,
            nominal,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )
    ";

    $stmt = $Conn->prepare($query);

    $stmt->bind_param(
        "ssssdi",
        $simpanan_nama,
        $simpanan_kategori,
        $simpanan_keterangan,
        $periode_pembayaran,
        $nominal,
        $status
    );

    $hasil = $stmt->execute();

    if($hasil){

        echo json_encode([
            "status" => "success",
            "message" => "Data jenis simpanan berhasil disimpan."
        ]);

    }else{

        echo json_encode([
            "status" => "error",
            "message" => "Terjadi kesalahan saat menyimpan data."
        ]);
    }

    $stmt->close();
    $Conn->close();
?>