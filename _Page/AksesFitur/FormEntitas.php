<?php
    // =========================================================
    // TIME ZONE
    // =========================================================
    date_default_timezone_set('Asia/Jakarta');

    // =========================================================
    // CONNECTION, FUNCTION, SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // =========================================================
    // VALIDASI SESI AKSES
    // =========================================================
    if (empty($SessionIdAkses)) {

        echo '
            <div class="alert alert-danger">
                <small>
                    Sesi akses sudah berakhir! Silakan login ulang!
                </small>
            </div>
        ';

        exit;
    }

    // =========================================================
    // VALIDASI MANDATORI
    // =========================================================
    if (empty($_POST['id_akses_fitur'])) {

        echo '
            <div class="alert alert-danger">
                <small>
                    ID fitur tidak boleh kosong!
                </small>
            </div>
        ';

        exit;
    }

    // =========================================================
    // SANITASI INPUT
    // =========================================================
    $id_akses_fitur = validateAndSanitizeInput($_POST['id_akses_fitur']);

    // =========================================================
    // VALIDASI FORMAT ID
    // =========================================================
    if (!is_numeric($id_akses_fitur)) {

        echo '
            <div class="alert alert-danger">
                <small>
                    Format ID fitur tidak valid!
                </small>
            </div>
        ';

        exit;
    }

    // =========================================================
    // OPEN DATA FITUR
    // =========================================================
    $Qry = $Conn->prepare("
        SELECT * 
        FROM akses_fitur 
        WHERE id_akses_fitur = ?
        LIMIT 1
    ");

    if (!$Qry) {

        echo '
            <div class="alert alert-danger">
                <small>
                    Terjadi kesalahan pada saat mempersiapkan query database!<br>
                    Keterangan : ' . htmlspecialchars($Conn->error) . '
                </small>
            </div>
        ';

        exit;
    }

    $Qry->bind_param("i", $id_akses_fitur);

    if (!$Qry->execute()) {

        echo '
            <div class="alert alert-danger">
                <small>
                    Terjadi kesalahan pada saat membuka data fitur dari database!<br>
                    Keterangan : ' . htmlspecialchars($Qry->error) . '
                </small>
            </div>
        ';

        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    // =========================================================
    // VALIDASI DATA DITEMUKAN
    // =========================================================
    if ($Result->num_rows == 0) {

        echo '
            <div class="alert alert-danger">
                <small>
                    Data fitur tidak ditemukan!
                </small>
            </div>
        ';

        $Qry->close();
        exit;
    }

    $Data = $Result->fetch_assoc();

    $Qry->close();

    // =========================================================
    // BUAT VARIABEL
    // =========================================================
    $nama       = htmlspecialchars($Data['nama'] ?? '', ENT_QUOTES, 'UTF-8');
    $kategori   = htmlspecialchars($Data['kategori'] ?? '', ENT_QUOTES, 'UTF-8');
    $kode       = htmlspecialchars($Data['kode'] ?? '', ENT_QUOTES, 'UTF-8');
    $keterangan = htmlspecialchars($Data['keterangan'] ?? '', ENT_QUOTES, 'UTF-8');

    // =========================================================
    // JUMLAH AKSES / USER
    // =========================================================
    $QryPengguna = $Conn->prepare("
        SELECT id_akses
        FROM akses_ijin
        WHERE id_akses_fitur = ?
    ");

    $JumlahPengguna = 0;

    if ($QryPengguna) {

        $QryPengguna->bind_param("i", $id_akses_fitur);

        if ($QryPengguna->execute()) {

            $ResultPengguna = $QryPengguna->get_result();

            $JumlahPengguna = $ResultPengguna->num_rows;
        }

        $QryPengguna->close();
    }

    if (empty($JumlahPengguna)) {

        $label_jumlah_pengguna = '
            <span class="badge badge-danger">
                NULL
            </span>
        ';

    } else {

        $label_jumlah_pengguna = '
            <span class="badge badge-success">
                ' . $JumlahPengguna . ' Orang
            </span>
        ';
    }

    // =========================================================
    // JUMLAH ENTITAS
    // =========================================================
    $QryEntitas = $Conn->prepare("
        SELECT uuid_akses_entitas
        FROM akses_referensi
        WHERE id_akses_fitur = ?
    ");

    $JumlahEntitas = 0;

    if ($QryEntitas) {

        $QryEntitas->bind_param("i", $id_akses_fitur);

        if ($QryEntitas->execute()) {

            $ResultEntitas = $QryEntitas->get_result();

            $JumlahEntitas = $ResultEntitas->num_rows;
        }

        $QryEntitas->close();
    }

    if (empty($JumlahEntitas)) {

        $label_jumlah_entitas = '
            <span class="badge badge-danger">
                NULL
            </span>
        ';

    } else {

        $label_jumlah_entitas = '
            <span class="badge badge-primary">
                ' . $JumlahEntitas . ' Entitas
            </span>
        ';
    }

    // =========================================================
    // TAMPILKAN DATA
    // =========================================================
    echo '
        <div class="row mb-2">
            <div class="col-4"><small>Nama Fitur</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">' . $nama . '</small></div>
        </div>

        <div class="row mb-2">
            <div class="col-4"><small>Kategori</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">' . $kategori . '</small></div>
        </div>

        <div class="row mb-2">
            <div class="col-4"><small>Kode Fitur</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">' . $kode . '</small></div>
        </div>

        <div class="row mb-2">
            <div class="col-4"><small>Keterangan</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">' . $keterangan . '</small></div>
        </div>

        <div class="row mb-2">
            <div class="col-4"><small>Jumlah Entitas</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">' . $label_jumlah_entitas . '</small></div>
        </div>

        <div class="row mb-2">
            <div class="col-4"><small>Jumlah Akses/User</small></div>
            <div class="col-1"><small>:</small></div>
            <div class="col-7"><small class="text text-muted">' . $label_jumlah_pengguna . '</small></div>
        </div>
        <hr>

    ';

?>
<?php
    // =========================================================
    // DAFTAR ENTITAS YANG MEMILIKI AKSES FITUR
    // =========================================================
    $QryListEntitas = $Conn->prepare("
        SELECT
            ae.uuid_akses_entitas,
            ae.akses,
            ae.keterangan
        FROM akses_referensi ar
        LEFT JOIN akses_entitas ae
            ON ar.uuid_akses_entitas = ae.uuid_akses_entitas
        WHERE ar.id_akses_fitur = ?
        ORDER BY ae.akses ASC
    ");

    if(!$QryListEntitas){
        echo '
            <div class="alert alert-danger">
                <small>
                    Gagal mempersiapkan query daftar entitas!<br>
                    '.htmlspecialchars($Conn->error).'
                </small>
            </div>
        ';
        exit;
    }

    $QryListEntitas->bind_param("i", $id_akses_fitur);

    if(!$QryListEntitas->execute()){
        echo '
            <div class="alert alert-danger">
                <small>
                    Gagal membuka daftar entitas!<br>
                    '.htmlspecialchars($QryListEntitas->error).'
                </small>
            </div>
        ';
        $QryListEntitas->close();
        exit;
    }

    $ResultListEntitas = $QryListEntitas->get_result();

    echo '
    <div class="mb-3">
        <h6 class="text-primary">
            <i class="bi bi-diagram-3"></i>
            Daftar Entitas Yang Memiliki Akses
        </h6>
    </div>
    ';

    if($ResultListEntitas->num_rows == 0){

        echo '
            <div class="alert alert-warning text-center">
                <small>
                    Belum ada entitas yang diberikan akses ke fitur ini.
                </small>
            </div>
        ';

    }else{

        echo '<div class="accordion" id="AccordionEntitas">';

        $no = 0;

        while($DataEntitas = $ResultListEntitas->fetch_assoc()){

            $no++;

            $uuid_entitas = htmlspecialchars($DataEntitas['uuid_akses_entitas']);
            $akses_entitas = htmlspecialchars($DataEntitas['akses']);
            $keterangan_entitas = htmlspecialchars($DataEntitas['keterangan']);

            // Hitung jumlah user pada entitas tersebut
            $JumlahUser = 0;

            $QryUser = $Conn->prepare("
                SELECT id_akses
                FROM akses
                WHERE uuid_akses_entitas = ?
            ");

            if($QryUser){

                $QryUser->bind_param("s", $uuid_entitas);

                if($QryUser->execute()){

                    $ResultUser = $QryUser->get_result();
                    $JumlahUser = $ResultUser->num_rows;
                }

                $QryUser->close();
            }

            echo '
            <div class="accordion-item">

                <h2 class="accordion-header" id="heading'.$no.'">
                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse'.$no.'">

                        <div class="w-100 d-flex justify-content-between align-items-center pe-3">

                            <span>
                                <i class="bi bi-shield-check text-success"></i>
                                '.$akses_entitas.'
                            </span>

                            <span class="badge bg-primary">
                                '.$JumlahUser.' User
                            </span>

                        </div>

                    </button>
                </h2>

                <div
                    id="collapse'.$no.'"
                    class="accordion-collapse collapse"
                    data-bs-parent="#AccordionEntitas">

                    <div class="accordion-body">

                        <div class="row mb-2">
                            <div class="col-md-4">
                                <small class="text-muted">
                                    UUID Entitas
                                </small>
                            </div>
                            <div class="col-md-8">
                                <small>
                                    '.$uuid_entitas.'
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">
                                    Keterangan
                                </small>
                            </div>
                            <div class="col-md-8">
                                <small>
                                    '.(!empty($keterangan_entitas) ? $keterangan_entitas : '-').'
                                </small>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
            ';
        }

        echo '</div>';
    }

    $QryListEntitas->close();
?>