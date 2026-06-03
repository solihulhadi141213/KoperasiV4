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

    // Validasi id_supplier
    if(empty($_POST['id_supplier'])){
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
    $id_supplier = validateAndSanitizeInput($_POST['id_supplier']);

    // Open Data With Prepared Statement
    $Qry = $Conn->prepare("SELECT * FROM supplier WHERE id_supplier = ? LIMIT 1");
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

    $Qry->bind_param("i", $id_supplier);
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

    $Data              = $Result->fetch_assoc();
    $id_supplier       = (int)$Data['id_supplier'];
    $nama_supplier     = htmlspecialchars($Data['nama_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $kategori_supplier = htmlspecialchars($Data['kategori_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $alamat_supplier   = htmlspecialchars($Data['alamat_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $email_supplier    = htmlspecialchars($Data['email_supplier'] ?? '', ENT_QUOTES, 'UTF-8');
    $kontak_supplier   = htmlspecialchars($Data['kontak_supplier'] ?? '', ENT_QUOTES, 'UTF-8');

    $Qry->close();
?>
    <input type="hidden" name="id_supplier" id="id_supplier_edit" value="<?php echo $id_supplier; ?>">

    <div class="row mb-3">
        <div class="col-md-12">
            <label for="nama_supplier_edit">
                <small>Nama Supplier <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <input type="text" class="form-control" name="nama_supplier" id="nama_supplier_edit" placeholder="ex: CV.Maju Mundur" value="<?php echo $nama_supplier; ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="kategori_supplier_edit">
                <small><i>Kategori</i> <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
            </label>
            <select name="kategori_supplier" id="kategori_supplier_edit" required>
                <option value="">Pilih</option>
                <?php if(!empty($kategori_supplier)){ ?>
                    <option value="<?php echo $kategori_supplier; ?>" selected><?php echo $kategori_supplier; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="email_supplier_edit"><small>Email</small></label>
            <input type="email" class="form-control" name="email_supplier" id="email_supplier_edit" value="<?php echo $email_supplier; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="kontak_supplier_edit"><small>Kontak</small></label>
            <input type="text" class="form-control" name="kontak_supplier" id="kontak_supplier_edit" placeholder="62" value="<?php echo $kontak_supplier; ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="alamat_supplier_edit">
                <small>Alamat</small>
            </label>
            <textarea class="form-control" name="alamat_supplier" id="alamat_supplier_edit"><?php echo $alamat_supplier; ?></textarea>
        </div>
    </div>

    <script>
        if (document.querySelector('#kategori_supplier_edit')) {
            new TomSelect('#kategori_supplier_edit', {
                plugins     : ['virtual_scroll'],
                valueField  : 'value',
                labelField  : 'text',
                searchField : 'text',
                create      : true,
                createOnBlur: true,
                persist     : false,
                firstUrl    : function(query) {
                    return '_Page/Supplier/KategoriSupplier.php?page=1&search=' + encodeURIComponent(query);
                },

                load: function(query, callback) {
                    const url = this.getUrl(query);

                    $.ajax({
                        url     : url,
                        type    : 'GET',
                        dataType: 'json',
                        error   : function() {
                            callback();
                        },
                        success: function(json) {
                            callback(json.data);

                            if (json.next_page) {
                                this.setNextUrl(
                                    query,
                                    '_Page/Supplier/KategoriSupplier.php?page=' +
                                    json.next_page +
                                    '&search=' +
                                    encodeURIComponent(query)
                                );
                            }
                        }.bind(this)
                    });
                },

                shouldLoad: function() {
                    return true;
                }
            });
        }
    </script>
