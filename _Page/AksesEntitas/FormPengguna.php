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
    if (empty($_POST['uuid_akses_entitas'])) {
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

    // Variabel dan Sanitasi
    $uuid_akses_entitas = validateAndSanitizeInput($_POST['uuid_akses_entitas']);

    // =========================================================
    // DETAIL ENTITAS
    // =========================================================
    $Qry = $Conn->prepare("
        SELECT
            ae.uuid_akses_entitas,
            ae.akses,
            ae.keterangan,

            COUNT(DISTINCT ar.id_akses_referensi) AS jumlah_fitur,
            COUNT(DISTINCT a.id_akses) AS jumlah_pengguna

        FROM akses_entitas ae

        LEFT JOIN akses a
            ON ae.uuid_akses_entitas = a.uuid_akses_entitas

        LEFT JOIN akses_referensi ar
            ON ae.uuid_akses_entitas = ar.uuid_akses_entitas

        WHERE ae.uuid_akses_entitas = ?

        GROUP BY
            ae.uuid_akses_entitas,
            ae.akses,
            ae.keterangan

        LIMIT 1
    ");

    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    Gagal mempersiapkan query database.
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
                    Gagal membuka data entitas akses.
                </small>
            </div>
        ';
        exit;
    }

    $Result = $Qry->get_result();

    // Jika data tidak ditemukan
    if ($Result->num_rows == 0) {

        echo '
            <div class="alert alert-danger text-center">
                <small>
                    Data entitas akses tidak ditemukan.
                </small>
            </div>
        ';

        $Qry->close();
        exit;
    }

    $Data = $Result->fetch_assoc();

    $NamaAkses          = htmlspecialchars($Data['akses'] ?? '', ENT_QUOTES, 'UTF-8');
    $KeteranganEntitas  = htmlspecialchars($Data['keterangan'] ?? '', ENT_QUOTES, 'UTF-8');
    $jumlah_fitur       = (int)$Data['jumlah_fitur'];
    $jumlah_pengguna    = (int)$Data['jumlah_pengguna'];

    $Qry->close();

?>

<div class="row mb-3">

    <div class="col-md-6 mb-3">
        <small class="text-muted">Level/Entitas Akses</small><br>
        <small class="text-dark">
            <?php echo $NamaAkses; ?>
        </small>
    </div>

    <div class="col-md-6 mb-3">
        <small class="text-muted">Jumlah Pengguna</small><br>
        <small class="text-dark">
            <?php echo $jumlah_pengguna; ?> Pengguna
        </small>
    </div>

    <div class="col-md-12 mb-3">
        <small class="text-muted">Keterangan</small><br>
        <small class="text-secondary">
            <?php echo $KeteranganEntitas; ?>
        </small>
    </div>

    <div class="col-md-12 mb-2">
        <hr>
    </div>

</div>

<?php

    // =========================================================
    // DAFTAR PENGGUNA
    // =========================================================
    $QryUser = $Conn->prepare("
        SELECT
            id_akses,
            nama_akses,
            email,
            image_akses,
            kontak_akses,
            datetime_daftar
        FROM akses
        WHERE uuid_akses_entitas = ?
        ORDER BY nama_akses ASC
    ");

    if (!$QryUser) {

        echo '
            <div class="alert alert-danger text-center">
                <small>
                    Gagal mempersiapkan query daftar pengguna.
                </small>
            </div>
        ';

        exit;
    }

    $QryUser->bind_param("s", $uuid_akses_entitas);

    if (!$QryUser->execute()) {

        echo '
            <div class="alert alert-danger text-center">
                <small>
                    Gagal membuka daftar pengguna.
                </small>
            </div>
        ';

        exit;
    }

    $ResultUser = $QryUser->get_result();

    // Jika tidak ada pengguna
    if ($ResultUser->num_rows == 0) {

        echo '
            <div class="alert alert-warning text-center">
                <small>
                    Belum ada pengguna pada level/entitas akses ini.
                </small>
            </div>
        ';

        $QryUser->close();
        exit;
    }

    while ($User = $ResultUser->fetch_assoc()) {

        $nama_akses   = htmlspecialchars($User['nama_akses'] ?? '', ENT_QUOTES, 'UTF-8');
        $email        = htmlspecialchars($User['email'] ?? '', ENT_QUOTES, 'UTF-8');
        $kontak       = htmlspecialchars($User['kontak_akses'] ?? '', ENT_QUOTES, 'UTF-8');
        $image_akses  = htmlspecialchars($User['image_akses'] ?? '', ENT_QUOTES, 'UTF-8');

        // Default image
        if (!empty($image_akses) && file_exists("../../assets/img/User/".$image_akses)) {

            $foto = "assets/img/User/".$image_akses;

        } else {

            $foto = "assets/img/no-access.png";
        }

        echo '
            <div class="card mb-3 border-0 shadow-sm">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="me-3">

                            <img 
                                src="'.$foto.'"
                                class="rounded-circle border"
                                width="60"
                                height="60"
                                style="object-fit: cover;"
                            >

                        </div>

                        <div class="flex-grow-1">

                            <div>
                                <small class="fw-bold text-dark">
                                    '.$nama_akses.'
                                </small>
                            </div>

                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-envelope"></i>
                                    '.$email.'
                                </small>
                            </div>

                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-telephone"></i>
                                    '.$kontak.'
                                </small>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        ';
    }

    $QryUser->close();
?>