<?php
    
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger text-center mb-2">
                <small>
                    <b>Opss!</b><br>
                    Sesi Akses sudah berakhir! Silahkan Login Ulang.
                </small>
            </div>
        ';
        exit;
    }

    // Validasi id_barang_kategori_harga
    if(empty($_POST['id_barang_kategori_harga'])){
        echo '
            <div class="alert alert-danger text-center mb-2">
                <small>
                    <b>Opss!</b><br>
                    Anda belum memilih data manapun
                </small>
            </div>
        ';
        exit;
    }

    // Variabel And Sanitazer
    $id_barang_kategori_harga=validateAndSanitizeInput($_POST['id_barang_kategori_harga']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM barang_kategori_harga WHERE id_barang_kategori_harga = ? LIMIT 1");
    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center mb-2">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat mempersiapkan query database!<br>
                    Keterangan : ' . htmlspecialchars($Conn->error) . '
                </small>
            </div>
        ';
        exit;
    }
    $Qry->bind_param("i", $id_barang_kategori_harga);
    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center mb-2">
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
            <div class="alert alert-danger text-center mb-2">
                <small>
                    <b>Opss!</b><br>
                    Data tidak ditemukan!
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }
    $Data           = $Result->fetch_assoc();
    $kategori_harga = htmlspecialchars($Data['kategori_harga']);
    $keterangan     = $Data['keterangan'] ?? '-';
    $Qry->close();
?>
    <input type="hidden" name="id_barang_kategori_harga" value="<?php echo $id_barang_kategori_harga; ?>">
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="kategori_harga_edit">
                <small>Kategori Harga <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="text" class="form-control" name="kategori_harga" id="kategori_harga_edit" value="<?php echo $kategori_harga; ?>" placeholder="Contoh: Harga Grosir" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="keterangan_edit">
                <small>Keterangan</small>
            </label>
            <textarea name="keterangan" id="keterangan_edit" class="form-control"><?php echo $keterangan; ?></textarea>
        </div>
    </div>
    