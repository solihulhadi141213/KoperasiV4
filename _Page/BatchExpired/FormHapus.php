<?php
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

if (empty($SessionIdAkses)) {
    echo '
    <div class="alert alert-danger">
        Sesi akses sudah berakhir.
    </div>';
    exit;
}

if (empty($_POST['id_barang_batch'])) {
    echo '
    <div class="alert alert-danger">
        ID Batch tidak ditemukan.
    </div>';
    exit;
}

$id_barang_batch = validateAndSanitizeInput($_POST['id_barang_batch']);

$stmt = $Conn->prepare("
    SELECT *
    FROM barang_batch
    WHERE id_barang_batch=?
    LIMIT 1
");

$stmt->bind_param("i", $id_barang_batch);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '
    <div class="alert alert-danger">
        Data batch tidak ditemukan.
    </div>';
    exit;
}

$data = $result->fetch_assoc();

$id_barang      = $data['id_barang'];
$no_batch       = htmlspecialchars($data['no_batch']);
$qty_batch      = $data['qty_batch'];
$expired_date   = $data['expired_date'];
$reminder_date  = $data['reminder_date'];
$status         = $data['status'];

$stmt->close();
?>

<input type="hidden" name="id_barang_batch" value="<?php echo $id_barang_batch;?>">
<input type="hidden" name="id_barang" value="<?php echo $id_barang;?>">
<div class="row mb-2">
    <div class="col-4"><small>No.Batch</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7"><small class="text text-grayish"><?php echo $no_batch;?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>QTY</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7"><small class="text text-grayish"><?php echo $qty_batch;?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Expired Date</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7"><small class="text text-grayish"><?php echo $expired_date;?></small></div>
</div>
<div class="row mb-2">
    <div class="col-4"><small>Remainder</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7"><small class="text text-grayish"><?php echo $reminder_date;?></small></div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>
                <b>PENTING</b><br>
                Data yang sudah dihapus tidak bisa dikembalikan lagi. <br>
                <b>Apakah anda yakin akan menghapus data tersebut?</b>
            </small>
        </div>
    </div>
</div>