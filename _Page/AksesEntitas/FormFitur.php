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

    // Variabel And Sanitazer
    $uuid_akses_entitas = validateAndSanitizeInput($_POST['uuid_akses_entitas']);

    // =========================================================
    // OPEN DATA ENTITAS
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
        LIMIT 1
    ");

    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan query database!<br>
                    ' . htmlspecialchars($Conn->error) . '
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
                    Gagal membuka data!<br>
                    ' . htmlspecialchars($Qry->error) . '
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

    // =========================================================
    // AMBIL REFERENSI FITUR YANG SUDAH DIPILIH
    // =========================================================
    $ReferensiFitur = [];

    $QryReferensi = $Conn->prepare("
        SELECT id_akses_fitur
        FROM akses_referensi
        WHERE uuid_akses_entitas = ?
    ");

    if ($QryReferensi) {

        $QryReferensi->bind_param(
            "s",
            $uuid_akses_entitas
        );

        $QryReferensi->execute();

        $ResultReferensi = $QryReferensi->get_result();

        while ($RowReferensi = $ResultReferensi->fetch_assoc()) {

            $ReferensiFitur[] = $RowReferensi['id_akses_fitur'];
        }

        $QryReferensi->close();
    }

?>
    <input type="hidden" 
           name="uuid_akses_entitas" 
           value="<?php echo $uuid_akses_entitas; ?>">

    <!-- INFORMASI ENTITAS -->
    <div class="row mb-3">
        <div class="col-md-6">
            <small>Level Akses / Entitas</small>
        </div>
        <div class="col-md-6">
            <small class="text-secondary">
                <?php echo $NamaAkses; ?>
            </small>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <small>Keterangan</small>
        </div>
        <div class="col-md-6">
            <small class="text-secondary">
                <?php echo $KeteranganEntitias; ?>
            </small>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <small>Jumlah Fitur</small>
        </div>
        <div class="col-md-6">
            <small class="text-secondary">
                <?php echo $jumlah_fitur; ?> Fitur
            </small>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <small>Jumlah Pengguna</small>
        </div>
        <div class="col-md-6">
            <small class="text-secondary">
                <?php echo $jumlah_pengguna; ?> Pengguna
            </small>
        </div>
    </div>


    <!-- LIST FITUR -->
    <div class="row">
        <div class="col-md-12">

            <?php

                // AMBIL SEMUA KATEGORI
                $KategoriQuery = mysqli_query(
                    $Conn,
                    "SELECT DISTINCT kategori 
                    FROM akses_fitur
                    ORDER BY kategori ASC"
                );
                if (!$KategoriQuery || mysqli_num_rows($KategoriQuery) == 0) {

                    echo '
                        <div class="alert alert-warning text-center">
                            Belum ada fitur yang tersedia.
                        </div>
                    ';

                } else {
                    while ($Kategori = mysqli_fetch_assoc($KategoriQuery)) {
                        $NamaKategori = htmlspecialchars($Kategori['kategori'],ENT_QUOTES,'UTF-8');
                        echo '
                            <hr>
                            <div class="row mt-3 mb-2">
                                <div class="col-12"><small><b>' . $NamaKategori . '</b></small></div>
                            </div>
                        ';

                        // AMBIL FITUR BERDASARKAN KATEGORI
                        $QryFitur = $Conn->prepare("
                            SELECT
                                id_akses_fitur,
                                kode,
                                nama,
                                keterangan
                            FROM akses_fitur
                            WHERE kategori = ?
                            ORDER BY nama ASC
                        ");

                        if ($QryFitur) {
                            $QryFitur->bind_param(
                                "s",
                                $NamaKategori
                            );

                            $QryFitur->execute();
                            $ResultFitur = $QryFitur->get_result();
                            while ($Fitur = $ResultFitur->fetch_assoc()) {
                                $id_akses_fitur = (int)$Fitur['id_akses_fitur'];
                                $kode = htmlspecialchars(
                                    $Fitur['kode'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                $nama = htmlspecialchars(
                                    $Fitur['nama'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                $keterangan = htmlspecialchars(
                                    $Fitur['keterangan'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                // CEK APAKAH SUDAH DIPILIH
                                $checked = '';
                                if (in_array($id_akses_fitur, $ReferensiFitur)) {
                                    $checked = 'checked';
                                }

                                echo '
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="id_akses_fitur[]" value="' . $id_akses_fitur . '" id="fitur_' . $id_akses_fitur . '" ' . $checked . '>
                                                <label class="form-check-label" for="fitur_' . $id_akses_fitur . '" >
                                                    <small>' . $nama . '</small><br>
                                                    <small class="text-secondary">' . $keterangan . '</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }
                            $QryFitur->close();
                        }
                    }
                }
            ?>

        </div>
    </div>