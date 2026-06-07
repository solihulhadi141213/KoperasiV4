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

    // Validasi id_barang
    if(empty($_POST['id_barang'])){
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
    $id_barang=validateAndSanitizeInput($_POST['id_barang']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM barang WHERE id_barang = ? LIMIT 1");
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
    $Qry->bind_param("i", $id_barang);
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
    $kode              = htmlspecialchars($Data['kode'] ?? '', ENT_QUOTES, 'UTF-8');
    $nama              = htmlspecialchars($Data['nama'] ?? '', ENT_QUOTES, 'UTF-8');
    $kategori          = htmlspecialchars($Data['kategori'] ?? '', ENT_QUOTES, 'UTF-8');
    $satuan            = htmlspecialchars($Data['satuan'] ?? '', ENT_QUOTES, 'UTF-8');
    $harga_beli        = (float)($Data['harga_beli'] ?? 0);
    $harga_jual        = (float)($Data['harga_jual'] ?? 0);
    $stok              = (float)($Data['stok'] ?? 0);
    $stok_minimum      = (float)($Data['stok_minimum'] ?? 0);
    $status            = (int)($Data['status'] ?? 0);
    $harga_beli_rupiah = "Rp " . number_format($harga_beli, 0, ',', '.');
    $harga_jual_rupiah = "Rp " . number_format($harga_jual, 0, ',', '.');
    $stok_label        = rtrim(rtrim(number_format($stok, 2, ',', '.'), '0'), ',') . ' ' . $satuan;
   
    $Qry->close();
?>

<input type="hidden" name="id_barang" value="<?php echo $id_barang; ?>">

<div class="row mb-3">
    <div class="col-md-12">
        <label for="no_batch">
            <small>Nomor Batch <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
        </label>
        <div class="input-group">
            <input type="text" class="form-control" name="no_batch" id="no_batch" placeholder="ex: 5542341356" required>
            <a href="javascript:void(0);" class="input-group-text generate_kode_batch" title="Generate Otomatis">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="qty_batch">
            <small>QTY <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
        </label>
        <input type="number" min="0" class="form-control" name="qty_batch" id="qty_batch">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="expired_date">
            <small>Expired Data <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
        </label>
        <input type="date" class="form-control" name="expired_date" id="expired_date" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="reminder_date">
            <small>Reminder <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
        </label>
        <input type="date" class="form-control" name="reminder_date" id="reminder_date" required>
    </div>
</div>
<div class="form-check mb-2">
    <input class="form-check-input" type="checkbox" name="status" id="status_batch" value="1" checked="">
    <label class="form-check-label" for="status_batch">
        <small>Barang Tersedia</small>
    </label>
</div>

<script>
    setTimeout(function(){

    let obj = document.getElementById('no_batch');

    if(obj){
        obj.focus();

        console.log('Focus Target:', obj);
        console.log('Active Element:', document.activeElement);
    }

}, 500);
</script>
