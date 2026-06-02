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
                                <option value="nama_pinjaman">Nama Pinjaman</option>
                                <option value="periode_angsuran">Periode Angsuran</option>
                                <option value="persen_jasa">Persen Jasa</option>
                                <option value="nominal_pinjaman">Nominal</option>
                                <option value="denda_metode">Metode Denda</option>
                                <option value="denda_nominal">Nominal Denda</option>
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
                                <option value="nama_pinjaman">Nama Pinjaman</option>
                                <option value="periode_angsuran">Periode Angsuran</option>
                                <option value="persen_jasa">Persen Jasa</option>
                                <option value="nominal_pinjaman">Nominal</option>
                                <option value="denda_metode">Metode Denda</option>
                                <option value="denda_nominal">Nominal Denda</option>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-plus"></i> Tambah Jenis Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nama_pinjaman">
                                <small>Nama Pinjaman <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                            <input type="text" class="form-control" name="nama_pinjaman" id="nama_pinjaman" placeholder="Contoh: Pinjaman Konsumtif" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="periode_angsuran">
                                <small>Periode Angsuran <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                            <input type="number" step="1" min="1" class="form-control" name="periode_angsuran" id="periode_angsuran" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="persen_jasa">
                                <small>Persen Jasa</small>
                            </label>
                            <input type="number" step="0.01" min="0" class="form-control" name="persen_jasa" id="persen_jasa">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nominal_pinjaman">
                                <small>Nominal Pinjaman <i class="bi bi-exclamation-circle" title="Wajib Diisi"></i></small>
                            </label>
                            <input type="text" class="form-control format_uang" name="nominal_pinjaman" id="nominal_pinjaman" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="denda_metode">
                                <small>Metode Denda</small>
                            </label>
                            <select name="denda_metode" class="form-control" id="denda_metode">
                                <option value="">Tidak Ada</option>
                                <option value="Harian">Harian</option>
                                <option value="Bulanan">Bulanan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="denda_nominal">
                                <small>Nominal Denda</small>
                            </label>
                            <input type="text" disabled class="form-control format_uang" name="denda_nominal" id="denda_nominal" placeholder="Rp">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark"><i class="bi bi-info-circle"></i> Detail Jenis Pinjaman</h5>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Edit Jenis Pinjaman</h5>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Hapus Jenis Pinjaman</h5>
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
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Non Aktifkan Jenis Pinjaman</h5>
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
                        <i class="bi bi-check"></i> Ya, Lanjutkan
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
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil"></i> Aktifkan Jenis Pinjaman</h5>
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
                        <i class="bi bi-check"></i> Ya, Lanjutkan
                    </button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
