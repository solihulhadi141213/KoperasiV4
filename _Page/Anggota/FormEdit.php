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

    // Validasi id_anggota
    if(empty($_POST['id_anggota'])){
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
    $id_anggota=validateAndSanitizeInput($_POST['id_anggota']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM anggota WHERE id_anggota = ? LIMIT 1");
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
    $Qry->bind_param("i", $id_anggota);
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
    $Data                = $Result->fetch_assoc();
    $nia                 = htmlspecialchars($Data['nia']);
    $nama                = htmlspecialchars($Data['nama']);
    $kontak              = htmlspecialchars($Data['kontak']);
    $email               = htmlspecialchars($Data['email']);
    $organization_tag    = htmlspecialchars($Data['organization_tag']);
    $rank_tag            = htmlspecialchars($Data['rank_tag']);
    $status              = htmlspecialchars($Data['status']);
    $datetime_registered = htmlspecialchars($Data['datetime_registered']);
    $datetime_leave      = htmlspecialchars($Data['datetime_leave']);
   
    $Qry->close();
?>
    <input type="hidden" name="id_anggota" value="<?php echo $id_anggota; ?>">
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nia_edit">
                <small>Nomor Induk Anggota (NIA) <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <div class="input-group">
                <input type="text" class="form-control" name="nia" id="nia_edit" placeholder="ex: 5542341356" value="<?php echo $nia; ?>" required>
                <a href="javascript:void(0);" class="input-group-text generate_nia_edit" title="Generate Otomatis">
                    <i class="bi bi-repeat"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nama_edit">
                <small>Nama Anggota <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="text" class="form-control" name="nama" id="nama_edit" placeholder="ex: Jhone Doe" value="<?php echo $nama; ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="kontak_edit"><small>Nomor Kontak</small></label>
            <input type="text" class="form-control" name="kontak" id="kontak_edit" placeholder="62" value="<?php echo $kontak; ?>" >
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="email_edit"><small>Alamat Email</small></label>
            <input type="email" class="form-control" name="email" id="email_edit" placeholder="example_email@domain.com" value="<?php echo $email; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="organization_tag_edit">
                <small><i>Organization Tag</i> <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <select name="organization_tag" id="organization_tag_edit" required>
                <option value="">Pilih</option>
                <option value="<?php echo $organization_tag; ?>" selected>
                    <?php echo $organization_tag; ?>
                </option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="rank_tag_edit">
                <small><i>Rank Tag</i> <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <select name="rank_tag" id="rank_tag_edit" required>
                <option value="">Pilih</option>
                <option value="<?php echo $rank_tag; ?>" selected>
                    <?php echo $rank_tag; ?>
                </option>
            </select>
        </div>
    </div>
    