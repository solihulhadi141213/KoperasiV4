<!-- ======= Footer ======= -->
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="node_modules/jquery/dist/jquery.min.js"type="text/javascript"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
<script src="node_modules/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script src="node_modules/jQuery-Mask-Plugin/dist/jquery.mask.min.js" type="text/javascript"></script>
<script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js" type="text/javascript"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

<!-- Apex chart -->
<?php
    if(empty($_GET['Page'])){
        echo '<script src="node_modules/apexcharts/dist/apexcharts.min.js"></script>';
    }
?>

<!-- Numerical -->
<script type="text/javascript">
    $(document).ready(function(){
        // Format mata uang.
        $( '#kembalian' ).mask('000.000.000.000', {reverse: true});
        $( '#pembayaran' ).mask('000.000.000.000', {reverse: true});
        $( '#jumlah_transaksi' ).mask('000.000.000.000', {reverse: true});
        $( '#jumlah_transaksi_edit' ).mask('000.000.000.000', {reverse: true});
        $( '#pembayaran_edit' ).mask('000.000.000.000', {reverse: true});
        $( '#kembalian_edit' ).mask('000.000.000.000', {reverse: true});
        $( '.format_uang' ).mask('000.000.000.000', {reverse: true});
    })
</script>

<!-- Scan QR -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<!-- Select2 (Tidak Digunakan Dulu) -->
<!-- <script src="node_modules/select2/dist/js/select2.min.js"></script> -->

<!-- Tom Select -->
<script src="node_modules/tom-select/dist/js/tom-select.complete.min.js"></script>
