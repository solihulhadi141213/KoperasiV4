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
                                <option value="stok">Stok</option>
                                <option value="Batch">Batch</option>
                                <option value="Expired">Expired</option>
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

<!-- FILTER BATCH -->
<div class="modal fade" id="ModalFilterBatch" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilterBatch">
                <input type="hidden" name="page" id="PageBatch" value="1">
                <input type="hidden" name="id_barang" id="IdBarangBatch" value="1">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="BatasBatch">Limit / Halaman</label>
                            <select name="batas" id="BatasBatch" class="form-control">
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
                            <label for="OrderByBatch">Dasar Urutan Data</label>
                            <select name="OrderBy" id="OrderByBatch" class="form-control">
                                <option value="">Pilih</option>
                                <option value="no_batch">No.Batch</option>
                                <option value="qty_batch">QTY</option>
                                <option value="expired_date">Expired Date</option>
                                <option value="reminder_date">Reminder</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="ShortByBatch">Mode Urutan Data</label>
                            <select name="ShortBy" id="ShortByBatch" class="form-control">
                                <option value="ASC">A To Z</option>
                                <option selected value="DESC">Z To A</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="KeywordByBatch">Pencarian</label>
                            <select name="KeywordBy" id="KeywordByBatch" class="form-control">
                                <option value="">Pilih</option>
                                <option value="no_batch">No.Batch</option>
                                <option value="qty_batch">QTY</option>
                                <option value="expired_date">Expired Date</option>
                                <option value="reminder_date">Reminder</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="FormFilterBatch">
                            <label for="KeywordBatch">Kata Kunci</label>
                            <input type="text" name="keyword" id="KeywordBatch" class="form-control">
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

<!-- TAMBAH BATCH -->
<div class="modal fade" id="ModalTambah" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesTambah" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-plus"></i> Tambah Batch & Expired</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormTambah">
                            <!-- Form Tambah Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiTambah">
                            <!-- Notifikasi Tambah Disini -->
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

<!-- EDIT BATCH -->
<div class="modal fade" id="ModalEdit" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesEdit" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Batch & Expired</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="FormEdit">
                            <!-- Form Tambah Disini -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="NotifikasiEdit">
                            <!-- Notifikasi Tambah Disini -->
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

<!-- HAPUS BATCH -->
<div class="modal fade" id="ModalHapus" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesHapus" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Hapus Batch & Expired</h5>
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

<!-- MODAL CETAK KODE BATCH -->
<div class="modal fade" id="ModalCetakKodeBatch" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesCetakKodeBatch">

                <input type="hidden" name="id_barang_batch" id="put_id_barang_batch">

                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-upc-scan"></i> Kode Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-12" id="PreviewBarcodeKodeBatch">
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
                            <select name="type_code" id="type_code" class="form-control" required>
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
                            <select name="tampilkan_nama_barang_for_code" id="tampilkan_nama_barang_for_code" class="form-control" required>
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