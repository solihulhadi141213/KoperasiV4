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
   
   
    $Qry->close();
?>
    <input type="hidden" name="id_barang" value="<?php echo $id_barang; ?>">
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="kode_edit">
                <small>Kode Barang <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <div class="input-group">
                <input type="text" class="form-control" name="kode" id="kode_edit" placeholder="ex: 5542341356" value="<?php echo $kode; ?>" required>
                <a href="javascript:void(0);" class="input-group-text generate_kode_barang_edit" title="Generate Otomatis">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nama_edit">
                <small>Nama Barang <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="text" class="form-control" name="nama" id="nama_edit" placeholder="ex: Jhone Doe" value="<?php echo $nama; ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="kategori_edit"><small>Kategori <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small></label>
            <select name="kategori" id="kategori_edit" required>
                <option value="">Pilih</option>
                <option selected value="<?php echo $kategori; ?>"><?php echo $kategori; ?></option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="harga_beli_edit"><small>Harga Beli</small></label>
            <div class="input-group">
                <div class="input-group-text">Rp</div>
                <input type="text" class="form-control format_uang" name="harga_beli" id="harga_beli_edit" placeholder="0.00" value="<?php echo $harga_beli; ?>">
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="harga_jual_edit"><small>Harga Jual (Standar)</small></label>
            <div class="input-group">
                <div class="input-group-text">Rp</div>
                <input type="text" class="form-control format_uang" name="harga_jual" id="harga_jual_edit" placeholder="0.00" value="<?php echo $harga_jual; ?>">
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="stok_edit">
                <small>Stok Awal</small>
            </label>
            <input type="number" min="0" step="0.01" class="form-control" name="stok" id="stok_edit" placeholder="0.00" value="<?php echo $stok; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="stok_minimum_edit">
                <small>Stok Minimum</small>
            </label>
            <input type="number" min="0" step="0.01" class="form-control" name="stok_minimum" id="stok_minimum_edit" placeholder="0.00" value="<?php echo $stok_minimum; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="satuan_edit"><small>Satuan <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small></label>
            <select name="satuan" id="satuan_edit" required>
                <option value="">Pilih</option>
                <option selected value="<?php echo $satuan; ?>"><?php echo $satuan; ?></option>
            </select>
        </div>
    </div>
    