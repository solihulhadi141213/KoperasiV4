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
<!-- Menampilkan daftar akun pengguna yang bisa akses fitur tersebut -->
<?php
// =========================================================
// DAFTAR PENGGUNA YANG MEMILIKI AKSES FITUR
// =========================================================
$QryUser = $Conn->prepare("
    SELECT
        a.id_akses,
        a.nama_akses,
        a.kontak_akses,
        a.email,
        a.image_akses,
        a.datetime_update,
        ae.akses
    FROM akses_ijin ai
    INNER JOIN akses a ON ai.id_akses = a.id_akses
    LEFT JOIN akses_entitas ae ON a.uuid_akses_entitas = ae.uuid_akses_entitas
    WHERE ai.id_akses_fitur = ?
    ORDER BY a.nama_akses ASC
");

if(!$QryUser){

    echo '
        <div class="alert alert-danger">
            <small>
                Gagal mempersiapkan query pengguna!<br>
                '.htmlspecialchars($Conn->error).'
            </small>
        </div>
    ';

    exit;
}

$QryUser->bind_param("i", $id_akses_fitur);

if(!$QryUser->execute()){

    echo '
        <div class="alert alert-danger">
            <small>
                Gagal membuka daftar pengguna!<br>
                '.htmlspecialchars($QryUser->error).'
            </small>
        </div>
    ';

    $QryUser->close();
    exit;
}

$ResultUser = $QryUser->get_result();

echo '
<div class="mb-3">
    <h6 class="text-primary">
        <i class="bi bi-people-fill"></i>
        Daftar Pengguna Yang Dapat Mengakses Fitur
    </h6>
</div>
';

if($ResultUser->num_rows == 0){

    echo '
        <div class="alert alert-warning text-center">
            <small>
                Belum ada pengguna yang memiliki akses ke fitur ini.
            </small>
        </div>
    ';

}else{

    echo '<div class="accordion" id="AccordionPengguna">';

    $no = 0;

    while($User = $ResultUser->fetch_assoc()){

        $no++;

        $id_akses        = $User['id_akses'];
        $nama_akses      = htmlspecialchars($User['nama_akses']);
        $kontak_akses    = htmlspecialchars($User['kontak_akses']);
        $email           = htmlspecialchars($User['email']);
        $akses_entitas   = htmlspecialchars($User['akses']);
        $datetime_update = empty($User['datetime_update']) ? '-' : date('d/m/Y H:i', strtotime($User['datetime_update']));

        if(!empty($User['image_akses']) && file_exists("../../assets/img/User/".$User['image_akses'])){

            $foto = "assets/img/User/".$User['image_akses'];

        }else{

            $foto = "assets/img/no-image.png";
        }

        echo '
        <div class="accordion-item">

            <h2 class="accordion-header" id="headingUser'.$no.'">

                <button
                    class="accordion-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseUser'.$no.'">

                    <div class="d-flex align-items-center w-100">

                        <img
                            src="'.$foto.'"
                            class="rounded-circle me-3"
                            style="
                                width:45px;
                                height:45px;
                                object-fit:cover;
                            "
                        >

                        <div>

                            <div>
                                <strong>'.$nama_akses.'</strong>
                            </div>

                            <small class="text-muted">
                                '.$akses_entitas.'
                            </small>

                        </div>

                    </div>

                </button>

            </h2>

            <div
                id="collapseUser'.$no.'"
                class="accordion-collapse collapse"
                data-bs-parent="#AccordionPengguna">

                <div class="accordion-body">

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <small class="text-muted">
                                ID Pengguna
                            </small>
                        </div>
                        <div class="col-md-8">
                            <small>'.$id_akses.'</small>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <small class="text-muted">
                                Email
                            </small>
                        </div>
                        <div class="col-md-8">
                            <small>'.$email.'</small>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <small class="text-muted">
                                Nomor Kontak
                            </small>
                        </div>
                        <div class="col-md-8">
                            <small>'.$kontak_akses.'</small>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <small class="text-muted">
                                Entitas
                            </small>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-primary">
                                '.$akses_entitas.'
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">
                                Update Terakhir
                            </small>
                        </div>
                        <div class="col-md-8">
                            <small>'.$datetime_update.'</small>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        ';
    }

    echo '</div>';
}

$QryUser->close();
?>