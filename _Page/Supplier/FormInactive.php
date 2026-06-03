<?php
    
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Sesi Akses sudah berakhir! Silahkan Login Ulang.
                </small>
            </div>
        ';
        exit;
    }

    // Validasi id_supplier
    if(empty($_POST['id_supplier'])){
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Anda belum memilih data manapun
                </small>
            </div>
        ';
        exit;
    }

    // Variabel And Sanitazer
    $id_supplier = validateAndSanitizeInput($_POST['id_supplier']);

    // Open Data With Prepared Statement
    $Qry = $Conn->prepare("SELECT * FROM supplier WHERE id_supplier = ? LIMIT 1");
    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat mempersiapkan query database!<br>
                    Keterangan : ' . htmlspecialchars($Conn->error) . '
                </small>
            </div>
        ';
        exit;
    }

    $Qry->bind_param("i", $id_supplier);
    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat membuka data dari database!<br>
                    Keterangan : ' . htmlspecialchars($Qry->error) . '
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    // Jika Tidak Ditemukan
    if ($Result->num_rows == 0) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Data tidak ditemukan!
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }

    $Data              = $Result->fetch_assoc();
    $nama_supplier     = htmlspecialchars($Data['nama_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $kategori_supplier = htmlspecialchars($Data['kategori_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $alamat_supplier   = htmlspecialchars($Data['alamat_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $email_supplier    = htmlspecialchars($Data['email_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $kontak_supplier   = htmlspecialchars($Data['kontak_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $status            = (int)($Data['status'] ?? 0);

    if(empty($alamat_supplier)){
        $alamat_supplier = "-";
    }
    if(empty($email_supplier)){
        $email_supplier = "-";
    }
    if(empty($kontak_supplier)){
        $kontak_supplier = "-";
    }

    // Routing Status
    if($status == 1){
        $label_status = '
            <span class="badge bg-success-subtle text-success">Active</span>
        ';
    }else{
        $label_status = '
            <span class="badge bg-danger-subtle text-danger">Inactive</span>
        ';
    }

    $Qry->close();
?>
    <input type="hidden" name="id_supplier" id="id_supplier_edit" value="<?php echo $id_supplier; ?>">
    <div class="row mb-3">
        <div class="col-4"><small>Nama Supplier</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$nama_supplier"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Kategori Supplier</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$kategori_supplier"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Nomor Kontak</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$kontak_supplier"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Email</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$email_supplier"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Alamat</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo nl2br($alamat_supplier); ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Status</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <?php echo "$label_status"; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <div class="alert alert-warning">
                <small>
                    <b>PENTING!</b><br>
                    Dengan menonaktifkan data supplier ini, maka selanjutnya anda tidak bisa melakukan transaksi dengan supplier tersebut. <br>
                    <b>Apakah anda yakin akan menonaktifkan data tersebut?</b>
                </small>
            </div>
        </div>
    </div>
    
