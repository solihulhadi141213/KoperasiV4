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

    if ($status == 1) {
        $label_status = '
            <span class="badge bg-success-subtle text-success">Active</span>
        ';
    } else {
        $label_status = '
            <span class="badge bg-danger-subtle text-danger">Inactive</span>
        ';
    }
    $Qry->close();
?>
    <input type="hidden" name="id_barang" value="<?php echo $id_barang; ?>">
    <div class="row mb-2">
        <div class="col-4"><small>Kode</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$kode"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Nama Barang</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$nama"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Kategori</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$kategori"; ?>
            </small>
        </div>
    </div>
    <hr>
    <div class="row mb-3">
        <div class="col-md-12">
            <small><b>Multi Harga Barang</b></small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="harga_jual_edit2"><small>Harga Jual (Standar)</small></label>
            <div class="input-group">
                <div class="input-group-text">Rp</div>
                <input type="text" class="form-control format_uang" name="harga_jual" id="harga_jual_edit2" placeholder="0.00" value="<?php echo $harga_jual; ?>">
            </div>
        </div>
    </div>

    <?php
        $QryHarga = $Conn->prepare("
            SELECT 
                k.id_barang_kategori_harga,
                k.kategori_harga,
                k.keterangan,
                h.harga
            FROM barang_kategori_harga k
            LEFT JOIN barang_harga h 
                ON h.id_barang_kategori_harga = k.id_barang_kategori_harga 
                AND h.id_barang = ?
            ORDER BY k.id_barang_kategori_harga DESC
        ");

        $QryHarga->bind_param("i", $id_barang);
        $QryHarga->execute();
        $ResultHarga = $QryHarga->get_result();
    ?>

    <?php 
        while ($row = $ResultHarga->fetch_assoc()) { 
            $id_kategori   = $row['id_barang_kategori_harga'];
            $nama_kategori = htmlspecialchars($row['kategori_harga'], ENT_QUOTES, 'UTF-8');
            $keterangan    = $row['keterangan'] ?? '';
            $harga         = $row['harga'] ?? '';
    ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <label>
                    <small><?php echo $nama_kategori; ?></small>
                </label>

                <div class="input-group">
                    <div class="input-group-text">Rp</div>
                    <input type="text" name="harga_kategori[<?php echo $id_kategori; ?>]" class="form-control format_uang" value="<?php echo $harga !== null ? $harga : ''; ?>" placeholder="0.00">
                </div>
                <small>
                    <small class="text text-grayish"><?php echo $keterangan; ?></small>
                </small>
            </div>
        </div>
    <?php } ?>