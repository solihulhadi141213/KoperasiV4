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
                    Sesi akses sudah berakhir! Silahkan Login Ulang.
                </small>
            </div>
        ';
        exit;
    }

    // Validasi id_akses
    if(empty($_POST['id_akses'])){
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
    $id_akses=validateAndSanitizeInput($_POST['id_akses']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM akses WHERE id_akses = ? LIMIT 1");
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
    $Qry->bind_param("s", $id_akses);
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
    $Data               = $Result->fetch_assoc();
    $uuid_akses_entitas = htmlspecialchars($Data['uuid_akses_entitas']);
    $nama_akses         = htmlspecialchars($Data['nama_akses']);
    $kontak_akses       = htmlspecialchars($Data['kontak_akses']);
    $image_akses        = htmlspecialchars($Data['image_akses']);
    $email              = htmlspecialchars($Data['email']);
    $akses              = htmlspecialchars($Data['akses']);
    $image_akses        = htmlspecialchars($Data['image_akses']);
    $datetime_daftar    = date('d/m/Y H:i',strtotime($Data['datetime_daftar']));
    $datetime_update    = date('d/m/Y H:i',strtotime($Data['datetime_update']));
    $Qry->close();
?>
<div class="row mb-3">
    <div class="col-6"><small>Nama Pengguna</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$nama_akses"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Nomor Kontak</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$kontak_akses"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Alamat Email</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$email"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Level/Entitas Akses</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$akses"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Tanggal Daftar</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$datetime_daftar"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Update</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$datetime_update"; ?>
        </small>
    </div>
</div>
<hr>

<!-- Menampilkan Daftar Fitur Pengguna -->

 <?php
    // Ambil daftar fitur yang dimiliki user
    $FiturUser = [];

    $QryIjin = $Conn->prepare("
        SELECT id_akses_fitur
        FROM akses_ijin
        WHERE id_akses = ?
    ");

    if($QryIjin){
        $QryIjin->bind_param("i", $id_akses);
        $QryIjin->execute();

        $ResultIjin = $QryIjin->get_result();

        while($RowIjin = $ResultIjin->fetch_assoc()){
            $FiturUser[] = $RowIjin['id_akses_fitur'];
        }

        $QryIjin->close();
    }

    // Ambil kategori
    $QryKategori = mysqli_query(
        $Conn,
        "SELECT DISTINCT kategori
        FROM akses_fitur
        ORDER BY kategori ASC"
    );

    if(mysqli_num_rows($QryKategori) > 0){
?>

<div class="accordion" id="AccordionFitur">

<?php
        $NoKategori = 0;

        while($DataKategori = mysqli_fetch_assoc($QryKategori)){

            $NoKategori++;

            $kategori = $DataKategori['kategori'];

            // Hitung jumlah fitur kategori
            $JumlahFitur = mysqli_num_rows(
                mysqli_query(
                    $Conn,
                    "SELECT id_akses_fitur
                    FROM akses_fitur
                    WHERE kategori='$kategori'"
                )
            );
?>

    <div class="accordion-item">

        <h2 class="accordion-header" id="heading<?php echo $NoKategori; ?>">
            <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse<?php echo $NoKategori; ?>"
            >
                <div class="d-flex justify-content-between w-100 me-3">
                    <span>
                        <i class="bi bi-folder2-open"></i>
                        <?php echo htmlspecialchars($kategori); ?>
                    </span>

                    <span class="badge bg-primary">
                        <?php echo $JumlahFitur; ?>
                    </span>
                </div>
            </button>
        </h2>

        <div
            id="collapse<?php echo $NoKategori; ?>"
            class="accordion-collapse collapse"
            data-bs-parent="#AccordionFitur"
        >

            <div class="accordion-body p-2">

                <?php
                    $QryFitur = $Conn->prepare("
                        SELECT *
                        FROM akses_fitur
                        WHERE kategori = ?
                        ORDER BY nama ASC
                    ");

                    $QryFitur->bind_param("s", $kategori);
                    $QryFitur->execute();

                    $ResultFitur = $QryFitur->get_result();

                    while($DataFitur = $ResultFitur->fetch_assoc()){

                        $id_akses_fitur = $DataFitur['id_akses_fitur'];
                        $nama_fitur     = $DataFitur['nama'];
                        $keterangan     = $DataFitur['keterangan'];

                        $PunyaAkses = in_array($id_akses_fitur, $FiturUser);
                ?>

                    <div class="border-bottom py-2">

                        <div class="d-flex align-items-start">

                            <div class="me-2">

                                <?php if($PunyaAkses){ ?>
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                <?php }else{ ?>
                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                <?php } ?>

                            </div>

                            <div class="flex-grow-1">

                                <div class="fw-bold">
                                    <?php echo htmlspecialchars($nama_fitur); ?>
                                </div>

                                <?php if(!empty($keterangan)){ ?>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($keterangan); ?>
                                    </small>
                                <?php } ?>

                            </div>

                        </div>

                    </div>

                <?php
                    }

                    $QryFitur->close();
                ?>

            </div>

        </div>

    </div>

<?php
        }
?>

</div>

<?php
    }else{
?>
    <div class="alert alert-warning text-center">
        Tidak ada fitur yang terdaftar.
    </div>
<?php
    }
?>