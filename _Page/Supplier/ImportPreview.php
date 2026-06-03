<?php
    // Hindari notice deprecation PhpSpreadsheet lama merusak response HTML import.
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    require '../../vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Validasi Session
    if(empty($SessionIdAkses)){
        echo '
            <tr class="table-danger">
                <td colspan="7" class="text-center">Session Berakhir</td>
            </tr>
        ';
        exit;
    }

    // Validasi File
    if(empty($_FILES['file_import']['tmp_name'])){
        echo '
            <tr class="table-danger">
                <td colspan="7" class="text-center">File Tidak Ada</td>
            </tr>
        ';
        exit;
    }

    $file_tmp  = $_FILES['file_import']['tmp_name'];
    $file_name = $_FILES['file_import']['name'];
    $file_size = $_FILES['file_import']['size'];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if($ext != 'xlsx'){
        echo '
            <tr class="table-danger">
                <td colspan="7" class="text-center">Format file harus xlsx</td>
            </tr>
        ';
        exit;
    }

    if($file_size > (2 * 1024 * 1024)){
        echo '
            <tr class="table-danger">
                <td colspan="7" class="text-center">Ukuran file maksimal 2 MB</td>
            </tr>
        ';
        exit;
    }

    try {
        $spreadsheet = IOFactory::load($file_tmp);
    } catch (Exception $e) {
        echo '
            <tr class="table-danger">
                <td colspan="7" class="text-center">Gagal membaca file excel</td>
            </tr>
        ';
        exit;
    }

    $sheet = $spreadsheet->getActiveSheet();
    $rows  = $sheet->toArray();

    $no = 0;

    foreach($rows as $key => $row){

        // Skip Header
        if($key == 0){
            continue;
        }

        $nama_supplier     = trim($row[1] ?? '');
        $kategori_supplier = trim($row[2] ?? '');
        $kontak_supplier   = trim($row[3] ?? '');
        $email_supplier    = trim($row[4] ?? '');
        $alamat_supplier   = trim($row[5] ?? '');

        // Lewati baris kosong
        if(
            $nama_supplier     == '' &&
            $kategori_supplier == '' &&
            $kontak_supplier   == '' &&
            $email_supplier    == '' &&
            $alamat_supplier   == ''
        ){
            continue;
        }

        $no++;

        $status_row = 'success';
        $keterangan = 'Berhasil Import';

        //------------------------------------
        // VALIDASI
        //------------------------------------
        if(empty($nama_supplier)){
            $status_row = 'danger';
            $keterangan = 'Nama supplier kosong';
        }

        if($status_row == 'success' && empty($kategori_supplier)){
            $status_row = 'danger';
            $keterangan = 'Kategori supplier kosong';
        }

        if(
            $status_row == 'success' &&
            !empty($email_supplier) &&
            !filter_var($email_supplier, FILTER_VALIDATE_EMAIL)
        ){
            $status_row = 'danger';
            $keterangan = 'Format email tidak valid';
        }

        // Cek duplikat nama supplier
        if($status_row == 'success'){
            $QryCheck = $Conn->prepare("
                SELECT id_supplier
                FROM supplier
                WHERE nama_supplier = ?
                LIMIT 1
            ");

            if(!$QryCheck){
                $status_row = 'danger';
                $keterangan = 'Gagal validasi duplikat';
            }else{
                $QryCheck->bind_param("s", $nama_supplier);
                $QryCheck->execute();

                $ResultCheck = $QryCheck->get_result();

                if($ResultCheck->num_rows > 0){
                    $status_row = 'danger';
                    $keterangan = 'Nama supplier sudah terdaftar';
                }

                $QryCheck->close();
            }
        }

        //------------------------------------
        // INSERT
        //------------------------------------
        if($status_row == 'success'){
            $nama_supplier_db     = htmlspecialchars($nama_supplier, ENT_QUOTES, 'UTF-8');
            $kategori_supplier_db = htmlspecialchars($kategori_supplier, ENT_QUOTES, 'UTF-8');
            $kontak_supplier_db   = htmlspecialchars($kontak_supplier, ENT_QUOTES, 'UTF-8');
            $email_supplier_db    = htmlspecialchars($email_supplier, ENT_QUOTES, 'UTF-8');
            $alamat_supplier_db   = htmlspecialchars($alamat_supplier, ENT_QUOTES, 'UTF-8');
            $status               = 1;

            $Insert = $Conn->prepare("
                INSERT INTO supplier (
                    nama_supplier,
                    kategori_supplier,
                    alamat_supplier,
                    email_supplier,
                    kontak_supplier,
                    status
                ) VALUES (
                    ?, ?, ?, ?, ?, ?
                )
            ");

            if(!$Insert){
                $status_row = 'danger';
                $keterangan = 'Gagal mempersiapkan insert';
            }else{
                $Insert->bind_param(
                    "sssssi",
                    $nama_supplier_db,
                    $kategori_supplier_db,
                    $alamat_supplier_db,
                    $email_supplier_db,
                    $kontak_supplier_db,
                    $status
                );

                if(!$Insert->execute()){
                    $status_row = 'danger';
                    $keterangan = 'Gagal insert data';
                }

                $Insert->close();
            }
        }

        $nama_supplier_html     = htmlspecialchars($nama_supplier, ENT_QUOTES, 'UTF-8');
        $kategori_supplier_html = htmlspecialchars($kategori_supplier, ENT_QUOTES, 'UTF-8');
        $kontak_supplier_html   = htmlspecialchars($kontak_supplier, ENT_QUOTES, 'UTF-8');
        $email_supplier_html    = htmlspecialchars($email_supplier, ENT_QUOTES, 'UTF-8');
        $alamat_supplier_html   = htmlspecialchars($alamat_supplier, ENT_QUOTES, 'UTF-8');
        $keterangan_html        = htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8');

        echo '
            <tr class="table-'.$status_row.'">
                <td class="small text-center text-grayish">'.$no.'</td>
                <td class="small text-grayish">'.$nama_supplier_html.'</td>
                <td class="small text-grayish">'.$kategori_supplier_html.'</td>
                <td class="small text-grayish">'.$kontak_supplier_html.'</td>
                <td class="small text-grayish">'.$email_supplier_html.'</td>
                <td class="small text-grayish">'.$alamat_supplier_html.'</td>
                <td class="small text-grayish">'.$keterangan_html.'</td>
            </tr>
        ';
    }

    if($no == 0){
        echo '
            <tr class="table-warning">
                <td colspan="7" class="text-center">Tidak ada data yang dapat diimport</td>
            </tr>
        ';
    }
?>
