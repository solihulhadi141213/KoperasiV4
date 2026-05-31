<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    $response = [
        "status"  => "error",
        "message" => "Terjadi kesalahan."
    ];
    if (empty($SessionIdAkses)) {
        $response = [
            "status"  => "error",
            "message" => "Sesi Akses Sudah Berakhir! Silahkan Login Ulang"
        ];
        echo json_encode($response);
        exit;
    }

    try {

        // Validasi metode
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            throw new Exception('Metode request tidak valid.');
        }

        // Ambil data
        $nia              = trim($_POST['nia'] ?? '');
        $nama             = trim($_POST['nama'] ?? '');
        $kontak           = trim($_POST['kontak'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $organization_tag = trim($_POST['organization_tag'] ?? '');
        $rank_tag         = trim($_POST['rank_tag'] ?? '');

        // Validasi wajib
        if(empty($nia)){
            throw new Exception('Nomor Induk Anggota (NIA) tidak boleh kosong.');
        }

        if(empty($nama)){
            throw new Exception('Nama anggota tidak boleh kosong.');
        }

        if(empty($organization_tag)){
            throw new Exception('Organization Tag tidak boleh kosong.');
        }

        if($rank_tag === ''){
            throw new Exception('Rank Tag tidak boleh kosong.');
        }

        // Rank harus angka
        if(!is_numeric($rank_tag)){
            throw new Exception('Rank Tag hanya boleh berupa angka.');
        }

        // Validasi email
        if(!empty($email)){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                throw new Exception('Format email tidak valid.');
            }
        }

        // Escape data
        $nia              = mysqli_real_escape_string($Conn, $nia);
        $nama             = mysqli_real_escape_string($Conn, $nama);
        $kontak           = mysqli_real_escape_string($Conn, $kontak);
        $email            = mysqli_real_escape_string($Conn, $email);
        $organization_tag = mysqli_real_escape_string($Conn, $organization_tag);
        $rank_tag         = (int)$rank_tag;

        // Cek duplikasi NIA
        $cek = mysqli_query(
            $Conn,
            "SELECT id_anggota 
            FROM anggota 
            WHERE nia='$nia'
            LIMIT 1"
        );

        if(mysqli_num_rows($cek) > 0){
            throw new Exception('Nomor Induk Anggota (NIA) sudah digunakan.');
        }

        // Simpan
        $datetime_registered = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO anggota (
                nia,
                nama,
                kontak,
                email,
                organization_tag,
                rank_tag,
                status,
                datetime_registered
            ) VALUES (
                '$nia',
                '$nama',
                ".(!empty($kontak) ? "'$kontak'" : "NULL").",
                ".(!empty($email) ? "'$email'" : "NULL").",
                '$organization_tag',
                '$rank_tag',
                'Active',
                '$datetime_registered'
            )
        ";

        $insert = mysqli_query($Conn, $query);

        if(!$insert){
            throw new Exception(mysqli_error($Conn));
        }

        $response = [
            "status"  => "success",
            "message" => "Data anggota berhasil ditambahkan."
        ];

    } catch (Exception $e){

        $response = [
            "status"  => "error",
            "message" => $e->getMessage()
        ];
    }

    echo json_encode($response);
?>