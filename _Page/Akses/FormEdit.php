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
<input type="hidden" name="id_akses" value="<?php echo $id_akses; ?>">
<div class="row mb-3">
    <div class="col-md-12">
        <label for="nama_akses_edit">Nama Pengguna</label>
        <input type="text" class="form-control" name="nama_akses" id="nama_akses_edit" value="<?php echo $nama_akses; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="kontak_akses_edit">Nomor Kontak</label>
        <input type="text" class="form-control" name="kontak_akses" id="kontak_akses_edit" value="<?php echo $kontak_akses; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="email_edit">Alamat Email</label>
        <input type="email" class="form-control" name="email" id="email_edit"  value="<?php echo $email; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="uuid_akses_entitas_edit">Level/Entitas</label>
        <select name="uuid_akses_entitas" id="uuid_akses_entitas_edit" class="form-control">
            <option value="">Pilih</option>
            <?php
                $query = mysqli_query($Conn, "SELECT * FROM akses_entitas ORDER BY akses ASC");
                while ($data = mysqli_fetch_array($query)) {
                    $uuid_akses_entitas_list = $data['uuid_akses_entitas'];
                    $akses_list              = $data['akses'];
                    if($uuid_akses_entitas_list==$uuid_akses_entitas){
                        echo '<option selected value="'.$uuid_akses_entitas_list.'">'.$akses_list.'</option>';
                    }else{
                        echo '<option value="'.$uuid_akses_entitas_list.'">'.$akses_list.'</option>';
                    }
                    
                }
            ?>
        </select>
    </div>
</div>