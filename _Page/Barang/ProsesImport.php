<?php

// =========================================================
// CONNECTION & SESSION
// =========================================================
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// =========================================================
// VALIDASI SESSION
// =========================================================
if (empty($SessionIdAkses)) {
    echo '
        <tr class="table-danger">
            <td colspan="10">Session Berakhir</td>
        </tr>
    ';
    exit;
}

// =========================================================
// VALIDASI FILE
// =========================================================
if (empty($_FILES['file_import']['tmp_name'])) {
    echo '
        <tr class="table-danger">
            <td colspan="10">File Tidak Ada</td>
        </tr>
    ';
    exit;
}

$file_tmp  = $_FILES['file_import']['tmp_name'];
$file_name = $_FILES['file_import']['name'];
$file_size = $_FILES['file_import']['size'];

$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if ($ext != 'xlsx') {
    echo '
        <tr class="table-danger">
            <td colspan="10">Format file harus xlsx</td>
        </tr>
    ';
    exit;
}

if ($file_size > (2 * 1024 * 1024)) {
    echo '
        <tr class="table-danger">
            <td colspan="10">Ukuran file maksimal 2 MB</td>
        </tr>
    ';
    exit;
}

// =========================================================
// LOAD EXCEL
// =========================================================
try {

    $spreadsheet = IOFactory::load($file_tmp);

    $sheet = $spreadsheet->getActiveSheet();

    $rows = $sheet->toArray();

} catch (Exception $e) {

    echo '
        <tr class="table-danger">
            <td colspan="10">
                Gagal membaca file excel
            </td>
        </tr>
    ';
    exit;
}

// =========================================================
// VALIDASI ADA DATA
// =========================================================
if (count($rows) <= 1) {

    echo '
        <tr class="table-danger">
            <td colspan="10">
                Tidak ada data yang dapat diimport
            </td>
        </tr>
    ';
    exit;
}

// =========================================================
// PROSES IMPORT
// =========================================================
$no = 0;

$total_berhasil = 0;
$total_gagal = 0;

$kode_excel = [];

foreach ($rows as $key => $row) {

    // Skip Header
    if ($key == 0) {
        continue;
    }

    // Skip jika kolom tidak lengkap
    if (!isset($row[8])) {
        continue;
    }

    // =====================================================
    // MAPPING KOLOM
    // =====================================================
    // A = No
    // B = Kode
    // C = Nama Barang
    // D = Kategori
    // E = Stok
    // F = Stok Minimum
    // G = Satuan
    // H = Harga Beli
    // I = Harga Jual

    $kode           = trim(htmlspecialchars($row[1] ?? ''));
    $nama           = trim(htmlspecialchars($row[2] ?? ''));
    $kategori       = trim(htmlspecialchars($row[3] ?? ''));
    $stok           = trim($row[4] ?? '');
    $stok_minimum   = trim($row[5] ?? '');
    $satuan         = trim(htmlspecialchars($row[6] ?? ''));
    $harga_beli     = trim($row[7] ?? '');
    $harga_jual     = trim($row[8] ?? '');

    // Skip baris kosong
    if (
        empty($kode) &&
        empty($nama) &&
        empty($kategori) &&
        empty($satuan)
    ) {
        continue;
    }

    $no++;

    $status_row = 'success';
    $keterangan = 'Berhasil Import';

    // =====================================================
    // NORMALISASI ANGKA
    // =====================================================

    $stok = str_replace(',', '.', $stok);
    $stok_minimum = str_replace(',', '.', $stok_minimum);

    $harga_beli = preg_replace('/[^0-9]/', '', $harga_beli);
    $harga_jual = preg_replace('/[^0-9]/', '', $harga_jual);

    // =====================================================
    // VALIDASI MANDATORY
    // =====================================================

    if (empty($kode)) {
        $status_row = 'danger';
        $keterangan = 'Kode kosong';
    }

    if ($status_row == 'success' && empty($nama)) {
        $status_row = 'danger';
        $keterangan = 'Nama barang kosong';
    }

    if ($status_row == 'success' && empty($kategori)) {
        $status_row = 'danger';
        $keterangan = 'Kategori kosong';
    }

    if ($status_row == 'success' && empty($satuan)) {
        $status_row = 'danger';
        $keterangan = 'Satuan kosong';
    }

    // =====================================================
    // VALIDASI DUPLIKAT DALAM FILE
    // =====================================================

    if (
        $status_row == 'success' &&
        in_array($kode, $kode_excel)
    ) {
        $status_row = 'danger';
        $keterangan = 'Kode duplikat pada file';
    }

    $kode_excel[] = $kode;

    // =====================================================
    // VALIDASI ANGKA
    // =====================================================

    if (
        $status_row == 'success' &&
        !is_numeric($stok)
    ) {
        $status_row = 'danger';
        $keterangan = 'Stok harus angka';
    }

    if (
        $status_row == 'success' &&
        !is_numeric($stok_minimum)
    ) {
        $status_row = 'danger';
        $keterangan = 'Stok minimum harus angka';
    }

    if (
        $status_row == 'success' &&
        !is_numeric($harga_beli)
    ) {
        $status_row = 'danger';
        $keterangan = 'Harga beli harus angka';
    }

    if (
        $status_row == 'success' &&
        !is_numeric($harga_jual)
    ) {
        $status_row = 'danger';
        $keterangan = 'Harga jual harus angka';
    }

    // =====================================================
    // CEK DUPLIKAT DATABASE
    // =====================================================

    if ($status_row == 'success') {

        $QryCheck = $Conn->prepare("
            SELECT id_barang
            FROM barang
            WHERE kode=?
            LIMIT 1
        ");

        $QryCheck->bind_param(
            "s",
            $kode
        );

        $QryCheck->execute();

        $ResultCheck = $QryCheck->get_result();

        if ($ResultCheck->num_rows > 0) {

            $status_row = 'danger';
            $keterangan = 'Kode sudah digunakan';
        }

        $QryCheck->close();
    }

    // =====================================================
    // INSERT DATABASE
    // =====================================================

    if ($status_row == 'success') {

        $stok = (float)$stok;
        $stok_minimum = (float)$stok_minimum;
        $harga_beli = (float)$harga_beli;
        $harga_jual = (float)$harga_jual;

        $status = 1;

        $Insert = $Conn->prepare("
            INSERT INTO barang (
                kode,
                nama,
                kategori,
                stok,
                stok_minimum,
                satuan,
                harga_beli,
                harga_jual,
                status
            ) VALUES (
                ?,?,?,?,?,?,?,?,?
            )
        ");

        $Insert->bind_param(
            "sssddsddi",
            $kode,
            $nama,
            $kategori,
            $stok,
            $stok_minimum,
            $satuan,
            $harga_beli,
            $harga_jual,
            $status
        );

        if (!$Insert->execute()) {

            $status_row = 'danger';
            $keterangan = 'Gagal insert database';

            $total_gagal++;

        } else {

            $total_berhasil++;
        }

        $Insert->close();

    } else {

        $total_gagal++;
    }

    // =====================================================
    // OUTPUT
    // =====================================================

    echo '
        <tr class="table-'.$status_row.'">
            <td class="text-center"><small>'.$no.'</small></td>
            <td><small>'.$kode.'</small></td>
            <td><small>'.$nama.'</small></td>
            <td><small>'.$kategori.'</small></td>
            <td class="text-end"><small>'.$stok.'</small></td>
            <td class="text-end"><small>'.$stok_minimum.'</small></td>
            <td><small>'.$satuan.'</small></td>
            <td class="text-end"><small>'.number_format((float)$harga_beli,0,',','.').'</small></td>
            <td class="text-end"><small>'.number_format((float)$harga_jual,0,',','.').'</small></td>
            <td><small>'.$keterangan.'</small></td>
        </tr>
    ';
}

// =========================================================
// RINGKASAN
// =========================================================

echo '
    <tr class="table-info">
        <td colspan="10">
            <b>Import Selesai</b><br>
            Berhasil : '.$total_berhasil.' Data<br>
            Gagal : '.$total_gagal.' Data
        </td>
    </tr>
';
?>