<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-grid"></i> Dashboard
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row">
        <div class="col-md-12" id="notifikasi_proses">
            <!-- Kejadian Kegagalan Menampilkan Data Akan Ditampilkan Disini -->
        </div>
    </div>

    <!-- Menampilkan Data Count -->
    <div class="row">
        
        <div class="col-xxl-3 col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card sales-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-box"></i>
                        </div>
                        <div class="ps-3">
                            <b>Barang</b>
                            <p class="text-muted small pt-2 ps-1">200.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card transsaction-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="ps-3">
                            <b>Anggota</b>
                            <p class="text-muted small pt-2 ps-1">200.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card customers-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-coin"></i>
                        </div>
                        <div class="ps-3">
                            <b>Beban</b>
                            <p class="text-muted small pt-2 ps-1">200.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card revenue-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="ps-3">
                            <b>Pendapatan</b>
                            <p class="text-muted small pt-2 ps-1">200.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="row align-items-stretch">

        <div class="col-md-9 mb-3">
            <div class="card h-100">
                <div class="card-body" id="chart">
                    <!-- Grafik Beban dan Pendapatan -->
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body" id="pie">
                    <!-- Grafik Beban VS Pendapatan -->
                </div>
            </div>
        </div>

    </div>

    <div class="row align-items-stretch">

        <div class="col-md-9 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <b class="card-title">
                        <i class="bi bi-calendar"></i> Penjualan Terbaru
                    </b>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <td class="text-center"><small><b>No</b></small></td>
                                    <td class="text-left"><small><b>Tanggal</b></small></td>
                                    <td class="text-left"><small><b>Anggota</b></small></td>
                                    <td class="text-left"><small><b>Nominal</b></small></td>
                                    <td class="text-left"><small><b>Status</b></small></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><small class="text-muted">1</small></td>
                                    <td class="text-left"><small class="text-muted">01/01/2025 08:00</small></td>
                                    <td class="text-left"><small class="text-muted">Solihul hadi</small></td>
                                    <td class="text-left"><small class="text-muted">Rp 20.000</small></td>
                                    <td class="text-left">
                                        <span class="badge bg-success-subtle text-success">Lunas</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center"><small class="text-muted">2</small></td>
                                    <td class="text-left"><small class="text-muted">01/01/2025 08:00</small></td>
                                    <td class="text-left"><small class="text-muted">Solihul hadi</small></td>
                                    <td class="text-left"><small class="text-muted">Rp 20.000</small></td>
                                    <td class="text-left">
                                        <span class="badge bg-success-subtle text-success">Lunas</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center"><small class="text-muted">3</small></td>
                                    <td class="text-left"><small class="text-muted">01/01/2025 08:00</small></td>
                                    <td class="text-left"><small class="text-muted">Solihul hadi</small></td>
                                    <td class="text-left"><small class="text-muted">Rp 20.000</small></td>
                                    <td class="text-left">
                                        <span class="badge bg-success-subtle text-success">Lunas</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center"><small class="text-muted">4</small></td>
                                    <td class="text-left"><small class="text-muted">01/01/2025 08:00</small></td>
                                    <td class="text-left"><small class="text-muted">Solihul hadi</small></td>
                                    <td class="text-left"><small class="text-muted">Rp 20.000</small></td>
                                    <td class="text-left">
                                        <span class="badge bg-success-subtle text-success">Lunas</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center"><small class="text-muted">5</small></td>
                                    <td class="text-left"><small class="text-muted">01/01/2025 08:00</small></td>
                                    <td class="text-left"><small class="text-muted">Solihul hadi</small></td>
                                    <td class="text-left"><small class="text-muted">Rp 20.000</small></td>
                                    <td class="text-left">
                                        <span class="badge bg-success-subtle text-success">Lunas</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">Page 1 Of 100</div>
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-sm btn-floating btn-outline-secondary">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-floating btn-outline-secondary">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            
            <div class="card h-100">
                <div class="card-header">
                    <b class="card-title">
                        <i class="bi bi-award"></i> Top Anggota
                    </b>
                </div>
                <div class="card-body">
                    
                    <div class="row mb-3 border-1 border-bottom">
                        <div class="col-12">
                            <small><b>Solihul Hadi</b></small>
                        </div>
                        <div class="col-6 mb-2"><small class="text-muted">01/01/2025</small></div>
                        <div class="col-6 mb-2 text-end"><small class="text-success">Rp 11.500.000</small></div>
                    </div>
                    <div class="row mb-3 border-1 border-bottom">
                        <div class="col-12">
                            <small><b>Dewi Widiastuti</b></small>
                        </div>
                        <div class="col-6 mb-2"><small class="text-muted">01/01/2025</small></div>
                        <div class="col-6 mb-2 text-end"><small class="text-success">Rp 5.500.000</small></div>
                    </div>
                    <div class="row mb-3 border-1 border-bottom">
                        <div class="col-12">
                            <small><b>Windy Yanuariska</b></small>
                        </div>
                        <div class="col-6 mb-2"><small class="text-muted">01/01/2025</small></div>
                        <div class="col-6 mb-2 text-end"><small class="text-success">Rp 4.400.000</small></div>
                    </div>
                    <div class="row mb-3 border-1 border-bottom">
                        <div class="col-12">
                            <small><b>Anna Nur Fadilah</b></small>
                        </div>
                        <div class="col-6 mb-2"><small class="text-muted">01/01/2025</small></div>
                        <div class="col-6 mb-2 text-end"><small class="text-success">Rp 3.400.000</small></div>
                    </div>
                    <div class="row mb-3 border-1 border-bottom">
                        <div class="col-12">
                            <small><b>Syamsul Maarif</b></small>
                        </div>
                        <div class="col-6 mb-2"><small class="text-muted">01/01/2025</small></div>
                        <div class="col-6 mb-2 text-end"><small class="text-success">Rp 3.200.000</small></div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-sm btn-floating btn-outline-secondary">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-floating btn-outline-secondary">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>



</section>
