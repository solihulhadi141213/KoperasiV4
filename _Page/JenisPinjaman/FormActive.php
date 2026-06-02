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
    $denda_metode     = $Data['denda_metode'] ?? '-';
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

    // Nominal Rupiah
    $nominal_pinjaman = "Rp " . number_format($nominal_pinjaman, 0, ',', '.');
    $denda_nominal    = "Rp " . number_format($denda_nominal, 0, ',', '.');

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
    <input type="hidden" name="id_pinjaman_jenis" value="<?php echo $id_pinjaman_jenis; ?>">
    <div class="row mb-2">
        <div class="col-4"><small>Nama Pinjaman</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$nama_pinjaman"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Nominal</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$nominal_pinjaman"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Periode Angsuran</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$periode_angsuran Bulan"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Jasa</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$persen_jasa %"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Metode Denda</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$denda_metode"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Nominal Denda</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <small class="text-grayish">
                <?php echo "$denda_nominal"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4"><small>Status</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-7">
            <?php echo "$label_status"; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <div class="alert alert-info">
                <small>
                    <b>PENTING!</b><br>
                    Dengan mengaktifkan kembali data jenis pinjaman ini maka anda bisa melakukan entry data pinjaman untuk jenis ini.<br>
                    <b>Apakah anda yakin akan mengaktifkan kembali data tersebut?</b>
                </small>
            </div>
        </div>
    </div>
    