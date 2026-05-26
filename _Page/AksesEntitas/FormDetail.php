<?php
    
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opss!</b><br>
                    Sesi akses sudah berakhir! Silahkan Login Ulang.
                </small>
            </div>
        ';
        exit;
    }

    // Validasi uuid_akses_entitas
    if(empty($_POST['uuid_akses_entitas'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opss!</b><br>
                    Anda belum memilih data manapun
                </small>
            </div>
        ';
        exit;
    }

    // Variabel And Sanitazer
    $uuid_akses_entitas=validateAndSanitizeInput($_POST['uuid_akses_entitas']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("
        SELECT
            ae.uuid_akses_entitas,
            ae.akses,
            ae.keterangan,

            COUNT(DISTINCT ai.id_akses_ijin) AS jumlah_fitur,
            COUNT(DISTINCT a.id_akses) AS jumlah_pengguna
        FROM akses_entitas ae
        LEFT JOIN akses a ON ae.uuid_akses_entitas = a.uuid_akses_entitas
        LEFT JOIN akses_ijin ai ON a.id_akses = ai.id_akses
        WHERE ae.uuid_akses_entitas = ? 
        LIMIT 1
    ");
    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat mempersiapkan query database!<br>
                    Keterangan : ' . htmlspecialchars($Conn->error) . '
                </small>
            </div>
        ';
        exit;
    }
    $Qry->bind_param("s", $uuid_akses_entitas);
    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center">
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
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opss!</b><br>
                    Data tidak ditemukan!
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }
    $Data               = $Result->fetch_assoc();
    $NamaAkses          = htmlspecialchars($Data['akses'] ?? '', ENT_QUOTES, 'UTF-8');
    $KeteranganEntitias = htmlspecialchars($Data['keterangan'] ?? '', ENT_QUOTES, 'UTF-8');
    $jumlah_fitur       = (int)$Data['jumlah_fitur'];
    $jumlah_pengguna    = (int)$Data['jumlah_pengguna'];
    $Qry->close();

    // Menghitung Jumlah Fitur Dan Pengguna
?>
    <div class="row mb-3">
        <div class="col-md-6"><small>Level Akses / Entitas</small></div>
        <div class="col-md-6">
            <small class="text-grayish">
                <?php echo "$NamaAkses"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6"><small>Keterangan</small></div>
        <div class="col-md-6">
            <small class="text-grayish">
                <?php echo "$KeteranganEntitias"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6"><small>Jumlah Fitur</small></div>
        <div class="col-md-6">
            <small class="text-grayish">
                <?php echo "$jumlah_fitur Fitur"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6"><small>Jumlah Akses Pengguna</small></div>
        <div class="col-md-6">
            <small class="text-grayish">
                <?php echo "$jumlah_pengguna Pengguna"; ?>
            </small>
        </div>
    </div>