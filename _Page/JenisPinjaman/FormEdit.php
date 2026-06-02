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

    // Validasi id_pinjaman_jenis
    if(empty($_POST['id_pinjaman_jenis'])){
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
    $id_pinjaman_jenis=validateAndSanitizeInput($_POST['id_pinjaman_jenis']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM pinjaman_jenis WHERE id_pinjaman_jenis = ? LIMIT 1");
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
    $Qry->bind_param("i", $id_pinjaman_jenis);
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
    $Data             = $Result->fetch_assoc();
    $nama_pinjaman    = htmlspecialchars($Data['nama_pinjaman']);
    $denda_metode     = $Data['denda_metode'];
    $status           = htmlspecialchars($Data['status']);
    
    // Routing Int dan Decimal
    if(empty($Data['periode_angsuran'])){
        $periode_angsuran = 0;
    }else{
        $periode_angsuran = $Data['periode_angsuran'];
    }
    if(empty($Data['persen_jasa'])){
        $persen_jasa = 0;
    }else{
        $persen_jasa = $Data['persen_jasa'];
    }
    if(empty($Data['nominal_pinjaman'])){
        $nominal_pinjaman = 0;
    }else{
        $nominal_pinjaman = $Data['nominal_pinjaman'];
    }
    if(empty($Data['denda_nominal'])){
        $denda_nominal = 0;
    }else{
        $denda_nominal = $Data['denda_nominal'];
    }
    $Qry->close();
?>
    <input type="hidden" name="id_pinjaman_jenis" value="<?php echo $id_pinjaman_jenis; ?>">
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nama_pinjaman_edit">
                <small>Nama Pinjaman <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="text" class="form-control" name="nama_pinjaman" id="nama_pinjaman_edit" placeholder="Contoh: Pinjaman Konsumtif" value="<?php echo $nama_pinjaman; ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="periode_angsuran_edit">
                <small>Periode Angsuran <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="number" step="1" min="1" class="form-control" name="periode_angsuran" id="periode_angsuran_edit" value="<?php echo $periode_angsuran; ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="persen_jasa_edit">
                <small>Persen Jasa</small>
            </label>
            <input type="number" step="0.01" min="0" class="form-control" name="persen_jasa" id="persen_jasa_edit" value="<?php echo $persen_jasa; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nominal_pinjaman_edit">
                <small>Nominal Pinjaman <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="text" class="form-control format_uang" name="nominal_pinjaman" id="nominal_pinjaman_edit" value="<?php echo number_format($nominal_pinjaman,0,',','.'); ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="denda_metode_edit">
                <small>Metode Denda</small>
            </label>
            <select name="denda_metode" class="form-control" id="denda_metode_edit">
                <option <?php if($denda_metode==""){echo "selected";} ?> value="">Tidak Ada</option>
                <option <?php if($denda_metode=="Harian"){echo "selected";} ?> value="Harian">Harian</option>
                <option <?php if($denda_metode=="Bulanan"){echo "selected";} ?> value="Bulanan">Bulanan</option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="denda_nominal_edit">
                <small>Nominal Denda</small>
            </label>
            <input type="text" <?php if($denda_metode==""){echo "disabled";} ?> class="form-control format_uang" name="denda_nominal" id="denda_nominal_edit" value="<?php echo number_format($denda_nominal,0,',','.'); ?>" placeholder="Rp">
        </div>
    </div>
    