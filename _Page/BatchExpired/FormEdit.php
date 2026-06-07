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

<div class="row mb-3">
    <div class="col-md-12">
        <label>
            <small>Nomor Batch</small>
        </label>
        <input
            type="text"
            class="form-control"
            name="no_batch"
            value="<?php echo $no_batch;?>"
            required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label>
            <small>Qty Batch</small>
        </label>
        <input
            type="number"
            min="0"
            step="0.01"
            class="form-control"
            name="qty_batch"
            value="<?php echo $qty_batch;?>">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label>
            <small>Expired Date</small>
        </label>
        <input
            type="date"
            class="form-control"
            name="expired_date"
            value="<?php echo $expired_date;?>"
            required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label>
            <small>Reminder Date</small>
        </label>
        <input
            type="date"
            class="form-control"
            name="reminder_date"
            value="<?php echo $reminder_date;?>">
    </div>
</div>

<div class="form-check">
    <input
        class="form-check-input"
        type="checkbox"
        name="status"
        value="1"
        id="status_batch_edit"
        <?php if($status==1){echo 'checked';}?>>

    <label class="form-check-label" for="status_batch_edit">
        <small>Barang Tersedia</small>
    </label>
</div>