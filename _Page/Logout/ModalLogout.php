<div class="modal fade" id="ModalLogout" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title w-100 text-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body pt-2">

                <!-- Icon / GIF -->
                <div class="mb-3">
                    <img src="assets/img/Icon/logout.gif" 
                         alt="Logout" 
                         class="img-fluid rounded"
                         style="max-height:120px;">
                </div>

                <!-- Text -->
                <p class="mb-1 fw-semibold">
                    Yakin ingin keluar?
                </p>
                <small class="text-muted">
                    Anda akan keluar dari sistem aplikasi
                </small>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 d-flex justify-content-center gap-2">

                <a href="_Page/Logout/ProsesLogout.php" 
                   class="btn btn-danger btn-sm px-3">
                    <i class="bi bi-check-circle"></i> Ya
                </a>

                <button type="button" 
                        class="btn btn-outline-secondary btn-sm px-3" 
                        data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Batal
                </button>

            </div>

        </div>
    </div>
</div>