<?php
    // Connection, Global Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi Akses
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger">
                Sesi akses sudah berakhir. Silahkan Login Ulang!
            </div>
        ';
        exit;
    }

    // Validasi Mandatory
    if (empty($_POST['id_barang_batch'])) {
        echo '
            <div class="alert alert-danger">
                ID Batch tidak boleh kosong.
            </div>
        ';
        exit;
    }
    if (empty($_POST['type_code'])) {
        echo '
            <div class="alert alert-danger">
                Tipe Code Tidak Boleh Kosong!
            </div>
        ';
        exit;
    }
    if (empty($_POST['tampilkan_nama_barang_for_code'])) {
        echo '
            <div class="alert alert-danger">
                Informasi Tampilkan Nama Barang Tidak Boleh Kosong
            </div>
        ';
        exit;
    }
    if (empty($_POST['type_file_code'])) {
        echo '
            <div class="alert alert-danger">
                Tipe File Tidak Boleh Kosong!
            </div>
        ';
        exit;
    }

    $id_barang_batch                = validateAndSanitizeInput($_POST['id_barang_batch']);
    $type_code                      = validateAndSanitizeInput($_POST['type_code']);
    $tampilkan_nama_barang_for_code = validateAndSanitizeInput($_POST['tampilkan_nama_barang_for_code']);
    $type_file_code                 = validateAndSanitizeInput($_POST['type_file_code']);
    if (empty($_POST['kategori_harga_kode'])) {
        $kategori_harga_kode = "";
    }else{
        $kategori_harga_kode = validateAndSanitizeInput($_POST['kategori_harga_kode']);
    }

    // Buka Data barang_batch dengan prepared statment
    $stmt = $Conn->prepare("
        SELECT *
        FROM barang_batch
        WHERE id_barang_batch = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id_barang_batch);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo '
            <div class="alert alert-danger">
                Data batch tidak ditemukan.
            </div>
        ';
        exit;
    }

    $data = $result->fetch_assoc();

    $id_barang      = $data['id_barang'];
    $no_batch       = htmlspecialchars($data['no_batch']);
    $qty_batch      = $data['qty_batch'];
    $expired_date   = $data['expired_date'];
    $reminder_date  = $data['reminder_date'];
    $status         = $data['status'];
    $stmt->close();

    //Buka Data Barang Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $Qry->bind_param("s", $id_barang);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi Kesalahan Pada Saat Membuka Data Barang!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }

    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    //Buat Variabel
    $kode_barang     = $Data['kode'];
    $nama_barang     = $Data['nama'];
    $kategori_barang = $Data['kategori'];
    $satuan_barang   = $Data['satuan'];
    $harga_beli      = $Data['harga_beli'];
    $harga_jual      = $Data['harga_jual'];

    //Lakukan pembulatan
    $harga_jual = (float) $harga_jual; // Konversi ke float
    $harga_jual = ($harga_jual == floor($harga_jual)) ? (int)$harga_jual : $harga_jual;
    //Format Harga RP
    $harga_jual_format = "Rp " . number_format($harga_jual,0,',','.');

    //Buka Kategori Harga
    if (empty($_POST['kategori_harga_kode'])) {
        $harga_barang="-";
    } else {
        //Buat Variabel
        $kategori_harga_kode=$_POST['kategori_harga_kode'];
        if($kategori_harga_kode=="Standar"){
            $harga_barang=$harga_jual_format;
        }else{
              //Buka Kategori Harga
            $id_barang_kategori_harga = $_POST['kategori_harga_kode'];
            $QryHarga                 = $Conn->prepare("SELECT * FROM barang_harga WHERE id_barang_kategori_harga = ? AND id_barang = ?");
            $QryHarga->bind_param("ii", $id_barang_kategori_harga, $id_barang);
            if (!$QryHarga->execute()) {
                $harga_barang = $Conn->error;
            }else{
                $ResultHarga = $QryHarga->get_result();
                $DataHarga   = $ResultHarga->fetch_assoc();
                $QryHarga->close();
                
                //Buat Variabel 
                if(!empty($DataHarga['harga'])){
                    $harga_barang = $DataHarga['harga'];
                    $harga_barang = "Rp " . number_format($harga_barang,0,',','.');
                }else{
                    $harga_barang = 0;
                    $harga_barang = "Rp " . number_format($harga_barang,0,',','.');
                }
                
            }
        }
    }
    if (empty($_POST['tampilkan_nama_barang_for_code'])) {
        $nama_barang="";
    }else{
        if($_POST['tampilkan_nama_barang_for_code']=="Tidak"){
            $nama_barang="";
        }
    }
    //Buat Element untuk preview
    if($type_code=="code128"||$type_code=="code39"||$type_code=="code25"){
        echo '
            <div class="preview-kode-batch">
                <div class="text-center">
                    <b class="name_of_product">BATCH.'.$no_batch.'</b><br>
                    <small class="name_of_product">'.$nama_barang.'</small><br>

                    <img
                        src="assets/vendor/barcode.php?text='.$no_batch.'&size=65&codetype='.$type_code.'"
                        alt="'.$no_batch.'"
                    /><br>

                    <small class="price_of_product">'.$harga_barang.'</small>
                </div>
            </div>
        ';
        exit;
    }
    include "../../assets/vendor/phpqrcode/qrlib.php";
    // Mulai output buffering
    $level  = QR_ECLEVEL_H;  // Tingkat koreksi error (H = High)
    $size   = 4;             // Ukuran skala
    $margin = 0;             // Margin (default 4)

    // Mulai output buffering
    ob_start();
    QRcode::png($no_batch, null, $level, $size, $margin);
    $imageString = base64_encode(ob_get_contents());
    ob_end_clean();
    echo '
        <div class="preview-kode-batch">
            <div class="row align-items-center">

                <div class="col-4 text-center">
                    <div class="qr-frame">
                        <img
                            src="data:image/png;base64,'.$imageString.'"
                            alt="QR Code"
                            width="100%"
                            class="image_qr_code"
                        >
                    </div>
                </div>

                <div class="col-8">
                    <b>BATCH.'.$no_batch.'</b><br>

                    <small class="name_of_product">
                        '.$nama_barang.'
                    </small><br>

                    <small class="price_of_product">
                        '.$harga_barang.'
                    </small>
                </div>

            </div>
        </div>
    ';
?>