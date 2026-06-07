<div class="pagetitle">
    <h1>
        <a href="">
            <i class="bi bi-box"></i> Batch & Expired</a>
        </a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Batch & Expired</li>
        </ol>
    </nav>
</div>
<section class="section dashboard" id="table_view">
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <form action="javascript:void(0);" id="ProsesBatas">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <b class="card-title"># Batch & Expired Barang</b>
                            </div>
                            <div class="col-6 mb-3 text-end">
                                <button type="button" class="btn btn-md btn-outline-secondary btn-floating" id="ReloadData" title="Reload Data">
                                    <i class="bi bi-repeat"></i>
                                </button>
                                <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilter" title="Filter Data">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <td class="text-center"><b><small>No</small></b></td>
                                    <td><b><small>Kode</small></b></td>
                                    <td><b><small>Nama Barang</small></b></td>
                                    <td><b><small>Kategori</small></b></td>
                                    <td><b><small>Stok</small></b></td>
                                    <td><b><small>Batch</small></b></td>
                                    <td><b><small>Expired</small></b></td>
                                    <td class="text-center"><b>Status</b></td>
                                    <td class="text-center"><b>Opsi</b></td>
                                </tr>
                            </thead>
                            <tbody id="tabel_batch_expired">
                                <!-- Menampilkan Tabel -->
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <small class="text-danger">No Data</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <small id="page_info">
                                Page 1 Of 100
                            </small>
                        </div>
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-md btn-outline-info btn-floating" id="prev_button">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-md btn-outline-info btn-floating" id="next_button">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<section class="section dashboard" id="detail_view">
    <!-- Detail View -->
</section>