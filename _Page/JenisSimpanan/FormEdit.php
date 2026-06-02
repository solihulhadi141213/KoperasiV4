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

    // Validasi id_simpanan_reference
    if(empty($_POST['id_simpanan_reference'])){
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
    $id_simpanan_reference=validateAndSanitizeInput($_POST['id_simpanan_reference']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM simpanan_reference WHERE id_simpanan_reference = ? LIMIT 1");
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
    $Qry->bind_param("i", $id_simpanan_reference);
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
    $Data                = $Result->fetch_assoc();
    $simpanan_nama       = htmlspecialchars($Data['simpanan_nama']);
    $simpanan_kategori   = htmlspecialchars($Data['simpanan_kategori']);
    $simpanan_keterangan = htmlspecialchars($Data['simpanan_keterangan']);
    $periode_pembayaran  = $Data['periode_pembayaran'] ?? '-';
    $status              = htmlspecialchars($Data['status']);
    
    // Routing Nominal
    if(empty($Data['nominal'])){
        $nominal = 0;
    }else{
        $nominal = htmlspecialchars($Data['nominal']);
    }
    // Nominal Rupiah
    $nominal_rupiah = "Rp " . number_format($nominal, 0, ',', '.');

    // Routing Status
    if($status==1){
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
    <input type="hidden" name="id_simpanan_reference" value="<?php echo $id_simpanan_reference; ?>">
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="simpanan_nama_edit">
                <small>Nama Simpanan <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="text" class="form-control" name="simpanan_nama" id="simpanan_nama_edit" placeholder="Contoh: Simpanan Hari Raya" value="<?php echo $simpanan_nama; ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="simpanan_kategori_edit">
                <small>Kategori <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <select class="form-control" name="simpanan_kategori" id="simpanan_kategori_edit" required>
                <option <?php if($simpanan_kategori==""){echo "selected";} ?> value="">Pilih</option>
                <option <?php if($simpanan_kategori=="Pokok"){echo "selected";} ?> value="Pokok">Pokok</option>
                <option <?php if($simpanan_kategori=="Wajib"){echo "selected";} ?> value="Wajib">Wajib</option>
                <option <?php if($simpanan_kategori=="Sukarela"){echo "selected";} ?> value="Sukarela">Sukarela</option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="periode_pembayaran_edit">
                <small>Periode Pembayaran</small>
            </label>
            <select name="periode_pembayaran" class="form-control" id="periode_pembayaran_edit">
                <option <?php if($periode_pembayaran==""){echo "selected";} ?> value="">Pilih</option>
                <option <?php if($periode_pembayaran=="Sekali"){echo "selected";} ?> value="Sekali">Sekali</option>
                <option <?php if($periode_pembayaran=="Tahun"){echo "selected";} ?> value="Tahun">Tahun</option>
                <option <?php if($periode_pembayaran=="Bulan"){echo "selected";} ?> value="Bulan">Bulan</option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nominal_edit">
                <small>Nominal</small>
            </label>
            <input type="text" class="form-control format_uang" name="nominal" id="nominal_edit" placeholder="Rp" value="<?php echo $nominal; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="simpanan_keterangan_edit">
                <small>Keterangan</small>
            </label>
            <textarea name="simpanan_keterangan" id="simpanan_keterangan_edit" class="form-control"><?php echo $simpanan_keterangan; ?></textarea>
        </div>
    </div>
    