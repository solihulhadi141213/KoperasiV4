<!-- FILTER -->
<div class="modal fade" id="ModalFilter" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilter">
                <input type="hidden" name="page" id="page" value="1">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="batas">Limit / Halaman</label>
                            <select name="batas" id="batas" class="form-control">
                                <option value="5">5</option>
                                <option selected value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="250">250</option>
                                <option value="500">500</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="OrderBy">Dasar Urutan Data</label>
                            <select name="OrderBy" id="OrderBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="kode">Kode</option>
                                <option value="nama">Nama</option>
                                <option value="kategori">Kategori</option>
                                <option value="satuan">Satuan</option>
                                <option value="harga_beli">Harga Beli</option>
                                <option value="harga_jual">Harga Jual</option>
                                <option value="stok">Stok</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="ShortBy">Mode Urutan Data</label>
                            <select name="ShortBy" id="ShortBy" class="form-control">
                                <option value="ASC">A To Z</option>
                                <option selected value="DESC">Z To A</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="KeywordBy">Pencarian</label>
                            <select name="KeywordBy" id="KeywordBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="kode">Kode</option>
                                <option value="nama">Nama</option>
                                <option value="kategori">Kategori</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="FormFilter">
                            <label for="keyword">Kata Kunci</label>
                            <input type="text" name="keyword" id="keyword" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TAMBAH -->
<div class="modal fade" id="ModalTambah" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesTambah" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-plus"></i> Tambah Barang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="kode">
                                <small>Kode Barang <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="kode" id="kode" placeholder="ex: 5542341356" required>
                                <a href="javascript:void(0);" class="input-group-text generate_kode_barang" title="Generate Otomatis">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nama">
                                <small>Nama Barang <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                            <input type="text" class="form-control" name="nama" id="nama" placeholder="ex: Jhone Doe" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="kategori"><small>Kategori <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small></label>
                            <select name="kategori" id="kategori" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="harga_beli"><small>Harga Beli</small></label>
                            <div class="input-group">
                                <div class="input-group-text">Rp</div>
                                <input type="text" class="form-control format_uang" name="harga_beli" id="harga_beli" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="harga_jual"><small>Harga Jual (Standar)</small></label>
                            <div class="input-group">
                                <div class="input-group-text">Rp</div>
                                <input type="text" class="form-control format_uang" name="harga_jual" id="harga_jual" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="stok">
                                <small>Stok Awal</small>
                            </label>
                            <input type="number" min="0" step="0.01" class="form-control" name="stok" id="stok" placeholder="0.00">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="stok_minimum">
                                <small>Stok Minimum</small>
                            </label>
                            <input type="number" min="0" step="0.01" class="form-control" name="stok_minimum" id="stok_minimum" placeholder="0.00">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="satuan"><small>Satuan <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small></label>
                            <select name="satuan" id="satuan" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12" id="NotifikasiTambah">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonTambah">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DETAIL -->
<div class="modal fade" id="ModalDetail" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark"><i class="bi bi-info-circle"></i> Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="FormDetail">
                        <!-- Form Detail Disini -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT -->
<div class="modal fade" id="ModalEdit" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesEdit" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormEdit">
                            <!-- Form Edit Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiEdit">
                            <!-- Notifikasi Edit Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonEdit">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- HAPUS -->
<div class="modal fade" id="ModalHapus" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesHapus" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Hapus Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormHapus">
                            <!-- Form Hapus Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiHapus">
                            <!-- Notifikasi Hapus Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonHapus">
                        <i class="bi bi-check"></i> Ya, Hapus
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- INACTIVE -->
<div class="modal fade" id="ModalInactive" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesInactive" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Non Aktifkan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormInactive">
                            <!-- Form Edit Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiInactive">
                            <!-- Notifikasi Edit Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonInactive">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ACTIVE -->
<div class="modal fade" id="ModalActive" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesActive" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Aktifkan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormActive">
                            <!-- Form Edit Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiActive">
                            <!-- Notifikasi Edit Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonActive">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MULTI HARGA -->
<div class="modal fade" id="ModalHarga" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesHarga" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Multi Harga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormHarga">
                            <!-- Form Harga Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiHarga">
                            <!-- Notifikasi Harga Disini -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonHarga">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EXPORT -->
<div class="modal fade" id="ModalExport" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/Barang/ProsesExport.php" method="GET" target="_blank" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="status">
                                <small>Status Barang</small>
                            </label>
                            
                            <div class="d-flex flex-wrap gap-3 mb-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status1" value="" checked="">
                                    <label class="form-check-label" for="status1">
                                        <small class="text text-grayish">All Data</small>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status2" value="1">
                                    <label class="form-check-label" for="status2">
                                        <small class="text text-grayish">Active</small>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status3" value="0">
                                    <label class="form-check-label" for="status3">
                                        <small class="text text-grayish">Inactive</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert-warning text-center">
                                <small>
                                    <b>PENTING!</b><br>
                                    Semakin banyak data Barang, maka sistem akan membutuhkan waktu lebih lama.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonExport">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- IMPORT -->
<div class="modal fade" id="ModalImport" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesImport" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-upload"></i> Import Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <small>
                                    <b>Petunjuk Import</b><br>
                                    <ol>
                                        <li>
                                            Buat sebuah file excel dengan nama apapun, atau download templatenya <a href="_Page/Barang/TemplateBarang.xlsx" target="_blank">berikut ini.</a>
                                        </li>
                                        <li>
                                            Apabila anda menggunakan template, silahkan hapus data yang ada dan ganti dengan data Barang yang anda miliki
                                        </li>
                                        <li>
                                            Apabila anda membuatnya sendiri, ikuti urutan kolom sesuai pada tabel di bawah keterangan ini.
                                        </li>
                                        <li>
                                            Sistem akan mengabaikan baris awal (dianggap sebagai header)
                                        </li>
                                    </ol>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="file_import">
                                <small>Upload File</small>
                            </label>
                            <input type="file" name="file_import" id="file_import" class="form-control">
                            <small>
                                <small class="text text-grayish">Ukuran file maksimal 2 mb dengan extension .xlsx</small>
                            </small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-import-wrapper">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <td class="text-center">
                                                <b><small>No</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Kode</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Barang</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Kategori</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Stok</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Stok Min</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Satuan</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Harga Beli</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Harga Jual</small></b>
                                            </td>
                                            <td class="text-center">
                                                <b><small>Keterangan</small></b>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody id="NotifikasiImport">
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                <small class="text text-grayish">Belum Ada Data Import</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <button type="button" disabled class="btn btn-secondary w-100" id="ButtonReset">
                                <i class="bi bi-upload"></i> Reset Form
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" disabled class="btn btn-primary" id="ButtonImport">
                        <i class="bi bi-upload"></i> Import
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- DETAIL KODE -->
<div class="modal fade" id="ModalDetailKode" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesCetakCodeBarang">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-upc-scan"></i> Kode Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12" id="PreviewBarcodeKode">
                            <!-- Preview Barcode Kode akan tampil disini -->
                        </div>
                    </div>
                    <div class="row mb-3 border-1 border-top">
                        <div class="col-12 mb-2 mt-2"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="type_code"><small>Tipe Code</small></label>
                        </div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <select name="type_code" id="type_code" class="form-control">
                                <option value="code128">Barcode (code128)</option>
                                <option value="code39">Barcode (code39)</option>
                                <option value="code25">Barcode (code25)</option>
                                <option value="qrcode">Qrcode</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="tampilkan_nama_barang_for_code"><small>Nama Barang</small></label>
                        </div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <select name="tampilkan_nama_barang_for_code" id="tampilkan_nama_barang_for_code" class="form-control">
                                <option value="Tampilkan">Tampilkan</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="kategori_harga_kode"><small>Kategori Harga</small></label>
                        </div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <select name="kategori_harga_kode" id="kategori_harga_kode" class="form-control">
                                <option value="Standar">Harga Jual Standar</option>
                                <?php
                                    $stmt = $Conn->prepare("SELECT id_barang_kategori_harga, kategori_harga FROM barang_kategori_harga ORDER BY id_barang_kategori_harga DESC");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($data = $result->fetch_assoc()) {
                                        $id_barang_kategori_harga = $data['id_barang_kategori_harga'];
                                        $kategori_harga           = $data['kategori_harga'];
                                        echo '<option value="'.$id_barang_kategori_harga.'">'.$kategori_harga.'</option>';
                                    }
                                    $stmt->close();
                                ?>
                                <option value="">Jangan Tampilkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="type_file_code"><small>Tipe Cetak</small></label>
                        </div>
                        <div class="col-1"><small>:</small></div>
                        <div class="col-7">
                            <select name="type_file_code" id="type_file_code" class="form-control">
                                <option value="Direct">Direct</option>
                                <option value="Image">Image</option>
                                <option value="PDF">PDF</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiCetakKode">
                            <!-- Notifikasi Cetak Code Disni -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonCetakKode">
                        <i class="bi bi-printer"></i> Cetak Code
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MULTI SATUAN -->
<div class="modal fade" id="ModalMultiSatuan" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">
                    <i class="bi bi-box"></i> Multi Satuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12" id="FormMultiSatuan">
                      <!-- Menampilkan Form Multi Satuan (Menampilkan Detail Barang Dan Tombol Multi Satuan) -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td class="text-center"><b><small>No</small></b></td>
                                        <td><b><small>Satuan</small></b></td>
                                        <td><b><small>Isi</small></b></td>
                                        <td><b><small>Stok</small></b></td>
                                        <td class="text-center"><b>Opsi</b></td>
                                    </tr>
                                </thead>
                                <tbody id="TabelMultiSatuan">
                                    <!-- Menampilkan Tabel -->
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <small class="text-danger">No Data</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH MULTI SATUAN -->
<div class="modal fade" id="ModalTambahMultiSatuan" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content bg-primary-subtle">

            <form action="javascript:void(0);" id="ProsesTambahMultiSatuan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-plus"></i> Tambah Multi Satuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12" id="FormTambahMultiSatuan">

                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12" id="NotifikasiTambahMultiSatuan">

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonTambahMultiSatuan">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- MODAL EDIT MULTI SATUAN -->
<div class="modal fade" id="ModalEditMultiSatuan" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content bg-warning-subtle">

            <form action="javascript:void(0);" id="ProsesEditMultiSatuan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Multi Satuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12" id="FormEditMultiSatuan">

                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12" id="NotifikasiEditMultiSatuan">

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonEditMultiSatuan">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- MODAL HAPUS MULTI SATUAN -->
<div class="modal fade" id="ModalHapusMultiSatuan" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-danger-subtle">

            <form action="javascript:void(0);" id="ProsesHapusMultiSatuan">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Hapus Multi Satuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12" id="FormHapusMultiSatuan">

                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12" id="NotifikasiHapusMultiSatuan">

                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="ButtonHapusMultiSatuan">
                        <i class="bi bi-check"></i> Ya, Hapus
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>