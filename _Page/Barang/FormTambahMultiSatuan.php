<?php
    if(empty($_POST['id_barang'])){
        echo '
            <div class="alert alert-danger">
                <small>
                    <b>Opss!</b><br>
                    ID Barang Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }
    $id_barang = $_POST['id_barang'];
?>

<input type="hidden" name="id_barang" value="<?php echo $id_barang; ?>">
<div class="row mb-3">
    <div class="col-12">
        <label for="satuan_multi">
            <small>Nama Satuan</small>
        </label>
        <input type="text" name="satuan" id="satuan_multi" class="form-control" >
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="isi_multi">
            <small>Isi Satuan</small>
        </label>
        <input type="number" step="1" min="1" name="isi" id="isi_multi" class="form-control" >
    </div>
</div>
<script>
    // Enable Button 'ButtonEditMultiSatuan'
    $('#ButtonTambahMultiSatuan').prop('disabled', false);
</script>