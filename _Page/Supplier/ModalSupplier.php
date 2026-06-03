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
                                <option value="nama_supplier">Nama Supplier</option>
                                <option value="kategori_supplier">Kategori</option>
                                <option value="alamat_supplier">Alamat</option>
                                <option value="email_supplier">Email</option>
                                <option value="kontak_supplier">Kontak</option>
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
                                <option value="nama_supplier">Nama Supplier</option>
                                <option value="kategori_supplier">Kategori</option>
                                <option value="alamat_supplier">Alamat</option>
                                <option value="email_supplier">Email</option>
                                <option value="kontak_supplier">Kontak</option>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-plus"></i> Tambah Anggota Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nama_supplier">
                                <small>Nama Supplier <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                            <input type="text" class="form-control" name="nama_supplier" id="nama_supplier" placeholder="ex: CV.Maju Mundur" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="kategori_supplier">
                                <small><i>Kategori</i> <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                            <select name="kategori_supplier" id="kategori_supplier" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="email_supplier"><small>Email</small></label>
                            <input type="email" class="form-control" name="email_supplier" id="email_supplier">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="kontak_supplier"><small>Kontak</small></label>
                            <input type="text" class="form-control" name="kontak_supplier" id="kontak_supplier" placeholder="62">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="alamat_supplier">
                                <small>Alamat</small>
                            </label>
                            <textarea class="form-control" name="alamat_supplier" id="alamat_supplier"></textarea>
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
                <h5 class="modal-title text-dark"><i class="bi bi-info-circle"></i> Detail Supplier</h5>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Supplier</h5>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Hapus Supplier</h5>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Non Aktifkan Supplier</h5>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Aktifkan Supplier</h5>
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

<!-- EXPORT -->
<div class="modal fade" id="ModalExport" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="_Page/Supplier/ProsesExport.php" method="GET" target="_blank" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-download"></i> Export Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="status">
                                <small>Status Supplier</small>
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
                                    Semakin banyak data Supplier, maka sistem akan membutuhkan waktu lebih lama.
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
                    <h5 class="modal-title text-dark"><i class="bi bi-upload"></i> Import Data Supplier</h5>
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
                                            Buat sebuah file excel dengan nama apapun, atau download templatenya <a href="_Page/Supplier/template-supplier.xlsx" target="_blank">berikut ini.</a>
                                        </li>
                                        <li>
                                            Apabila anda menggunakan template, silahkan hapus data yang ada dan ganti dengan data anggota yang anda miliki
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
                                            <td class="text-center"><b><small>No</small></b></td>
                                            <td class="text-center"><b><small>Nama Supplier</small></b></td>
                                            <td class="text-center"><b><small>Kategori</small></b></td>
                                            <td class="text-center"><b><small>Kontak</small></b></td>
                                            <td class="text-center"><b><small>Email</small></b></td>
                                            <td class="text-center"><b><small>Alamat</small></b></td>
                                            <td class="text-center"><b><small>Keterangan</small></b></td>
                                        </tr>
                                    </thead>
                                    <tbody id="NotifikasiImport">
                                        <tr>
                                            <td colspan="7" class="text-center">
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