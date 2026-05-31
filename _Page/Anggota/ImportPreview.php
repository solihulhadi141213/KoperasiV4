<?php

include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Validasi Session
if(empty($SessionIdAkses)){
    echo '
        <tr class="table-danger">
            <td colspan="9">Session Berakhir</td>
        </tr>
    ';
    exit;
}

// Validasi File
if(empty($_FILES['file_import']['tmp_name'])){
    echo '
        <tr class="table-danger">
            <td colspan="9">File Tidak Ada</td>
        </tr>
    ';
    exit;
}

$file_tmp  = $_FILES['file_import']['tmp_name'];
$file_name = $_FILES['file_import']['name'];
$file_size = $_FILES['file_import']['size'];

$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if($ext!='xlsx'){
    echo '
        <tr class="table-danger">
            <td colspan="9">Format file harus xlsx</td>
        </tr>
    ';
    exit;
}

if($file_size > (2*1024*1024)){
    echo '
        <tr class="table-danger">
            <td colspan="9">Ukuran file maksimal 2 MB</td>
        </tr>
    ';
    exit;
}

$spreadsheet = IOFactory::load($file_tmp);

$sheet = $spreadsheet->getActiveSheet();

$rows = $sheet->toArray();

$no=0;

foreach($rows as $key=>$row){

    // Skip Header
    if($key==0){
        continue;
    }

    $no++;

    $nama = trim($row[1] ?? '');
    $nia = trim($row[2] ?? '');
    $kontak = trim($row[3] ?? '');
    $email = trim($row[4] ?? '');
    $organization = trim($row[5] ?? '');
    $rank = trim($row[6] ?? '');
    $tanggal_daftar = trim($row[7] ?? '');

    $status_row='success';
    $keterangan='Berhasil Import';

    //------------------------------------
    // VALIDASI
    //------------------------------------

    if(empty($nama)){
        $status_row='danger';
        $keterangan='Nama kosong';
    }

    if(empty($nia)){
        $status_row='danger';
        $keterangan='NIA kosong';
    }

    if(!empty($rank) && !ctype_digit($rank)){
        $status_row='danger';
        $keterangan='Rank harus angka';
    }

    if(!empty($email) && !filter_var($email,FILTER_VALIDATE_EMAIL)){
        $status_row='danger';
        $keterangan='Format email tidak valid';
    }

    // Cek NIA Duplikat
    if($status_row=='success'){

        $QryCheck=$Conn->prepare("
            SELECT id_anggota
            FROM anggota
            WHERE nia=?
            LIMIT 1
        ");

        $QryCheck->bind_param("s",$nia);
        $QryCheck->execute();

        $ResultCheck=$QryCheck->get_result();

        if($ResultCheck->num_rows>0){

            $status_row='danger';
            $keterangan='NIA sudah digunakan';

        }

        $QryCheck->close();

    }

    //------------------------------------
    // INSERT
    //------------------------------------

    if($status_row=='success'){

        $status='Active';
        $datetime_leave=NULL;

        $Insert=$Conn->prepare("
            INSERT INTO anggota (
                nama,
                nia,
                kontak,
                email,
                organization_tag,
                rank_tag,
                status,
                datetime_registered,
                datetime_leave
            ) VALUES (
                ?,?,?,?,?,?,?,?,?
            )
        ");

        $Insert->bind_param(
            "sssssssss",
            $nama,
            $nia,
            $kontak,
            $email,
            $organization,
            $rank,
            $status,
            $tanggal_daftar,
            $datetime_leave
        );

        if(!$Insert->execute()){

            $status_row='danger';
            $keterangan='Gagal insert';

        }

        $Insert->close();

    }

    echo '
        <tr class="table-'.$status_row.'">
            <td class="small text-center text-grayish">'.$no.'</td>
            <td class="small text-grayish">'.$nama.'</td>
            <td class="small text-grayish">'.$nia.'</td>
            <td class="small text-grayish">'.$kontak.'</td>
            <td class="small text-grayish">'.$email.'</td>
            <td class="small text-grayish">'.$organization.'</td>
            <td class="small text-center text-grayish">'.$rank.'</td>
            <td class="small text-grayish">'.$tanggal_daftar.'</td>
            <td class="small text-grayish">'.$keterangan.'</td>
        </tr>
    ';
}
?>