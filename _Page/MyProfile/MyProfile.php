<div class="pagetitle">
    <h1>
        <a href=""><i class="bi bi-person-circle"></i> Profil Saya</a></a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active"> Profil Saya</li>
        </ol>
    </nav>
</div>
<section class="section dashboard">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <small>
                    Berikut ini adalah halaman profil yang digunakan untuk mengelola informasi akses anda. 
                    Pada halaman ini anda bisa melakukan perubahan data akses (Nama, Email, Password dan Foto Profile).
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">

            <div class="card">
                <div class="card-header text-center">
                    <b class="card-title">
                        <i class="bi bi-info-circle"></i> Informasi Pengguna
                    </b>
                </div>
                <div class="card-body" id="detail_profil">
                    <!-- Menampilkan Detail Profil Disini -->
                </div>
                <div class="card-footer">
                    <div class="row mb-3">
                        <div class="col col-md-12 text-center">
                            <button class="btn btn-md btn-floating btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ModalUbahIdentitasProfil">
                                <i class="bi bi-pencil"></i> 
                            </button>
                            <button class="btn btn-md btn-floating btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ModalUbahFotoProfil">
                                <i class="bi bi-image"></i> 
                            </button>
                            <button class="btn btn-md btn-floating btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ModalUbahPasswordProfil">
                                <i class="bi bi-key"></i> 
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <b class="card-title">
                        <i class="bi bi-shield-check"></i> Izin Akses Pengguna
                    </b>
                </div>
                <div class="card-body" id="izin_akses_pengguna">
                    <!-- Ijin Akses Pengguna Ditampilkan Disini -->
                </div>
            </div>
        </div>
    </div>
</section>
