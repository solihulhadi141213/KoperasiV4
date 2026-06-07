// DOCUMEN READY FUNCTION
$(document).ready(function() {
    // ================================================================================
    // FUNCTION BLOCK
    // ================================================================================
    
    //Fungsi Menampilkan Data
    function ShowTable() {

        // Target And Filter
        let target = $('#tabel_batch_expired');
        let data   = $('#ProsesFilter').serialize();

        target.addClass('blur-loading');

        $.ajax({
            type: 'POST',
            url: '_Page/BatchExpired/TabelBarang.php',
            data: data,
            dataType: 'json',
            success: function(res) {

                if(res.status === "success"){

                    target.fadeOut(150, function () {
                        target.html(res.html).fadeIn(150);
                    });

                    // Update info page
                    $('#page_info').html('Page ' + res.page + ' Of ' + res.total_page);

                    // Handle tombol
                    $('#prev_button').prop('disabled', res.page <= 1);
                    $('#next_button').prop('disabled', res.page >= res.total_page);

                }else{
                    target.html(res.html);
                }

                target.removeClass('blur-loading');
            }
        });
    }

    //Fungsi Menampilkan Data Batch
    function ShowTableBatch(id_barang) {

        // Add id_barang to Form
        $('#IdBarangBatch').val(id_barang);

        // Target And Filter
        let target = $('#tabel_batch_barang');
        let data   = $('#ProsesFilterBatch').serialize();

        target.addClass('blur-loading');

        $.ajax({
            type: 'POST',
            url: '_Page/BatchExpired/TabelBarangBatch.php',
            data: data,
            dataType: 'json',
            success: function(res) {

                if(res.status === "success"){

                    target.fadeOut(150, function () {
                        target.html(res.html).fadeIn(150);
                    });

                    // Update info page
                    $('#page_info_batch').html('Page ' + res.page + ' Of ' + res.total_page);

                    // Handle tombol
                    $('#prev_button_batch').prop('disabled', res.page <= 1);
                    $('#next_button_batch').prop('disabled', res.page >= res.total_page);

                }else{
                    target.html(res.html);
                }

                target.removeClass('blur-loading');
            }
        });
    }

    // ================================================================================
    // EVENT BLOCK
    // ================================================================================

    // Menampilkan Data Pertama Kali
    ShowTable();

    //------------------------------------------------
    // FILTER & PAGGING
    //------------------------------------------------

    // Modal Filter
    $('#ModalFilter').on('shown.bs.modal', function () {
        // Auto Focus
        $('#keyword').trigger('focus');

    });

    //Filter Data
    $('#ProsesFilter').submit(function(){

        // Set Halaman Menjadi 1
        $('#page').val("1");

        // Reload Tabel
        ShowTable();

        // Tutup Modal
        $('#ModalFilter').modal('hide');
    });

    //Reload
    $('#ReloadData').click(function(){

        // Reset Form
        $('#ProsesFilter')[0].reset(); 

        // Reload Tabel
        ShowTable();
    });

    //Form Keyword By
    $('#KeywordBy').change(function(){

        // Menangkap nilai keyword by
        let KeywordBy = $(this).val();

        // Kirim Ke Form Filter Dengan Ajax
        $.ajax({
            type       : 'POST',
            url        : '_Page/Barang/FormFilter.php',
            data       : {KeywordBy: KeywordBy},
            success    : function(response){
                $('#FormFilter').html(response);
            }
        });

    });

    //Pagging Next
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowTable(0);
    });

    //Pagging Previous
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowTable(0);
    });

    //------------------------------------------------
    // DETAIL
    //------------------------------------------------
    
    // Modal Detail
    $('#ModalDetail').on('shown.bs.modal', function (e) {
        // Tangkap id_barang
        var id_barang = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormDetail')
            .css({
                'filter'    : 'blur(4px)',
                'opacity'   : '0.5',
                'transition': 'all 0.3s ease'
            })
            .html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-2"></div>
                    <div>Loading...</div>
                </div>
            `);
        // View Data With Ajax
        $.ajax({
            type   : 'POST',
            url    : '_Page/Barang/FormDetail.php',
            data   : {id_barang: id_barang},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormDetail').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormDetail').html(data);

                    // Hilangkan blur
                    $('#FormDetail').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormDetail').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormDetail').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });

    });

    $(document).on('click', '.show_detail_batch', function() {

        // Tangkap id_barang
        let id_barang = $(this).data('id');

        // Hide Table View
        $('#table_view').hide();

        // Show detail_view
        $('#detail_view').show();

        // Tampilkan Data Dengan AJAX
        $.ajax({
            type   : 'POST',
            url    : '_Page/BatchExpired/_DetailView.php',
            data   : {id_barang: id_barang},
           success: function (data) {
                $('#detail_view').css('opacity', '0');
                setTimeout(function () {
                    $('#detail_view').html(data);
                    $('#detail_view').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                    // Panggil setelah HTML selesai dimuat
                    ShowTableBatch(id_barang);
                }, 200);
            },
            error: function () {
                $('#detail_view').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#detail_view').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });
    });
    //------------------------------------------------
    // FILTER BATCH
    //------------------------------------------------
    $(document).on('click', '.modal_filter', function() {
        
        // Tangkap ID Barang
        let id_barang = $(this).data('id');

        // Add id_barang to Form
        $('#IdBarangBatch').val(id_barang);

        // Hide Table View
        $('#ModalFilterBatch').modal('show');
    });

    //Filter Data Batch
    $('#ProsesFilterBatch').submit(function(){

        // Set Halaman Menjadi 1
        $('#PageBatch').val("1");

        let id_barang = $('#IdBarangBatch').val();

        // Reload Tabel
        ShowTableBatch(id_barang);

        // Tutup Modal
        $('#ModalFilterBatch').modal('hide');
    });

    //Form Keyword By Menggunakan Pendelegasian
    $(document).on('change', '#KeywordByBatch', function(){

        let KeywordBy = $(this).val();

        $.ajax({
            type    : 'POST',
            url     : '_Page/BatchExpired/FormFilterBatch.php',
            data    : {
                KeywordBy: KeywordBy
            },
            success : function(response){
                $('#FormFilterBatch').html(response);
            }
        });

    });

    //Reload Barang Batch
    $(document).on('click', '.reload_barang_batch', function(){

        // Tangkap ID Barang Dari Tombol
        let id_barang = $(this).data('id');

        // Reset Filter
        $('#ProsesFilterBatch')[0].reset(); 
        
        // Reload Data
        ShowTableBatch(id_barang);
    });

    //Pagging Next
    $(document).on('click', '#next_button_batch', function() {
        let id_barang = $('#IdBarangBatch').val();
        var page_now = parseInt($('#PageBatch').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#PageBatch').val(next_page);
        ShowTableBatch(id_barang);
    });

    //Pagging Previous
    $(document).on('click', '#prev_button_batch', function() {
        let id_barang = $('#IdBarangBatch').val();
        var page_now = parseInt($('#PageBatch').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#PageBatch').val(next_page);
        ShowTableBatch(id_barang);
    });

    //------------------------------------------------
    // BACK TO DATA
    //------------------------------------------------
    $(document).on('click', '#back_to_data', function() {

        // Hide Table View
        $('#table_view').show();

        // Show detail_view
        $('#detail_view').hide();
    });

    //------------------------------------------------
    // TAMBAH BATCH
    //------------------------------------------------
    $(document).on('click', '.modal_tambah_batch', function() {

        // Tangkap ID Barang
        let id_barang = $(this).data('id');

        // Tampilkan Modal
        $('#ModalTambah').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiTambah').html('');

        // Loading Form
        $('#FormTambah').html('Loading..');

        // Buka Form Dengan Ajax
        $.ajax({
            type   : 'POST',
            url    : '_Page/BatchExpired/FormTambah.php',
            data   : {id_barang: id_barang},
            success: function (data) {
                $('#FormTambah').html(data);
            }
        });
    });

    // Generate Nomor Induk Otomatis
    $(document).on('click', '.generate_kode_batch', function () {
        let kode = Math.floor(100000 + Math.random() * 900000);
        $('#no_batch').val(kode);
    });

    // Handdle Proses Tambah
    $('#ProsesTambah').submit(function(e){
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiTambah').html('');

        // Tombol Submit
        let tombol = $('#ButtonTambah');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form termasuk file
        let formData = new FormData(this);

        // Send To Ajax
        $.ajax({
            type       : 'POST',
            url        : '_Page/BatchExpired/ProsesTambah.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    let id_barang = response.id_barang;

                    // Reset Notifikasi
                    $('#NotifikasiTambah').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalTambah').modal('hide');

                    // Reload Tabel
                    ShowTableBatch(id_barang);
                }else{

                    // Show Notification Error
                    $('#NotifikasiTambah').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiTambah').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // EDIT BATCH
    //------------------------------------------------
    $(document).on('click', '.modal_edit_batch', function() {

        // Tangkap ID Barang
        let id_barang_batch = $(this).data('id');

        // Tampilkan Modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        // Loading Form
        $('#FormEdit').html('Loading..');

        // Buka Form Dengan Ajax
        $.ajax({
            type   : 'POST',
            url    : '_Page/BatchExpired/FormEdit.php',
            data   : {id_barang_batch: id_barang_batch},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormEdit').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormEdit').html(data);

                    // Hilangkan blur
                    $('#FormEdit').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormEdit').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormEdit').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });
    });

    // Handdle Proses Tambah
    $('#ProsesEdit').submit(function(e){
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiEdit').html('');

        // Tombol Submit
        let tombol = $('#ButtonEdit');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form termasuk file
        let formData = new FormData(this);

        // Send To Ajax
        $.ajax({
            type       : 'POST',
            url        : '_Page/BatchExpired/ProsesEdit.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    let id_barang = response.id_barang;

                    // Reset Notifikasi
                    $('#NotifikasiTambah').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalEdit').modal('hide');

                    // Reload Tabel
                    ShowTableBatch(id_barang);
                }else{

                    // Show Notification Error
                    $('#NotifikasiEdit').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiEdit').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // HAPUS BATCH
    //------------------------------------------------
    $(document).on('click', '.modal_hapus_batch', function() {

        // Tangkap ID Barang
        let id_barang_batch = $(this).data('id');

        // Tampilkan Modal
        $('#ModalHapus').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiHapus').html('');

        // Loading Form
        $('#FormHapus').html('Loading..');

        // Buka Form Dengan Ajax
        $.ajax({
            type   : 'POST',
            url    : '_Page/BatchExpired/FormHapus.php',
            data   : {id_barang_batch: id_barang_batch},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormHapus').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormHapus').html(data);

                    // Hilangkan blur
                    $('#FormHapus').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormHapus').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormHapus').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });
    });

    // Handdle Proses Tambah
    $('#ProsesHapus').submit(function(e){
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiHapus').html('');

        // Tombol Submit
        let tombol = $('#ButtonHapus');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form termasuk file
        let formData = new FormData(this);

        // Send To Ajax
        $.ajax({
            type       : 'POST',
            url        : '_Page/BatchExpired/ProsesHapus.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    let id_barang = response.id_barang;

                    // Reset Notifikasi
                    $('#NotifikasiHapus').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalHapus').modal('hide');

                    // Reload Tabel
                    ShowTableBatch(id_barang);
                }else{

                    // Show Notification Error
                    $('#NotifikasiHapus').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiHapus').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // CETAK KODE BATCH
    //------------------------------------------------

    // Fungsi AJAX untuk menampilkan Barcode/QR
    function loadCodeBatch (){
        let ProsesCetakKodeBatch = $('#ProsesCetakKodeBatch').serialize();

        // Loading PreviewBarcodeKodeBatch
        $('#PreviewBarcodeKodeBatch').html('Loading..');

        $.ajax({
            type   : 'POST',
            url    : '_Page/BatchExpired/PreviewBarcodeKodeBatch.php',
            data   : ProsesCetakKodeBatch,
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#PreviewBarcodeKodeBatch').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#PreviewBarcodeKodeBatch').html(data);

                    // Hilangkan blur
                    $('#PreviewBarcodeKodeBatch').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#PreviewBarcodeKodeBatch').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#PreviewBarcodeKodeBatch').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });
    }
    
    //Modal Detail Kode Barang
    $(document).on('click', '.print_batch', function() {
        
        // Tangkap ID Barang Batch
        let id_barang_batch = $(this).data('id');

        // Tempelkan id_barang_batch ke form put_id_barang_batch
        $('#put_id_barang_batch').val(id_barang_batch);

        // Tampilkan Modal
        $('#ModalCetakKodeBatch').modal('show');

        loadCodeBatch ();
        
    });

    // Apabila type_code diubah
    $(document).on('change', '#type_code', function() {
        
        loadCodeBatch ();
        
    });

    //------------------------------------------------
    // CETAK KODE BATCH
    //------------------------------------------------
    $(document).on('submit', '#ProsesCetakKodeBatch', function(event) {

        event.preventDefault();

        // Ambil Format Cetak
        let formatCetak = $('#type_file_code').val();

        // Ambil Area Preview
        let content = document.getElementById("PreviewBarcodeKodeBatch");

        if (!formatCetak) {

            $('#NotifikasiCetakKode').html(`
                <div class="alert alert-danger">
                    Silahkan pilih tipe cetak terlebih dahulu.
                </div>
            `);

            return;
        }

        // Loading
        $('#NotifikasiCetakKode').html(`
            <div class="alert alert-info">
                Sedang memproses cetak...
            </div>
        `);

        $('#ButtonCetakKode')
            .prop('disabled', true)
            .html(`
                <span class="spinner-border spinner-border-sm"></span>
                Memproses...
            `);

        // Reset Tombol
        function resetButton() {

            $('#ButtonCetakKode')
                .prop('disabled', false)
                .html(`
                    <i class="bi bi-printer"></i>
                    Cetak Code
                `);
        }

        setTimeout(function(){

            //------------------------------------------------
            // PDF
            //------------------------------------------------
            if(formatCetak === "PDF"){

                html2canvas(content, {
                    scale: 3,
                    useCORS: true
                }).then(function(canvas){

                    let imgData = canvas.toDataURL('image/png');

                    let { jsPDF } = window.jspdf;

                    let pdf = new jsPDF('p', 'mm', 'a4');

                    let pdfWidth  = 190;
                    let pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                    pdf.addImage(
                        imgData,
                        'PNG',
                        10,
                        10,
                        pdfWidth,
                        pdfHeight
                    );

                    pdf.save('Kode_Batch.pdf');

                    $('#NotifikasiCetakKode').html(`
                        <div class="alert alert-success">
                            Download PDF berhasil.
                        </div>
                    `);

                    resetButton();

                }).catch(function(error){

                    console.log(error);

                    $('#NotifikasiCetakKode').html(`
                        <div class="alert alert-danger">
                            Gagal membuat PDF.
                        </div>
                    `);

                    resetButton();

                });

            }

            //------------------------------------------------
            // IMAGE
            //------------------------------------------------
            else if(formatCetak === "Image"){

                html2canvas(content, {
                    scale: 3,
                    useCORS: true
                }).then(function(canvas){

                    let imgData = canvas.toDataURL('image/png');

                    let link = document.createElement('a');

                    link.href     = imgData;
                    link.download = 'Kode_Batch.png';

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    $('#NotifikasiCetakKode').html(`
                        <div class="alert alert-success">
                            Download gambar berhasil.
                        </div>
                    `);

                    resetButton();

                }).catch(function(error){

                    console.log(error);

                    $('#NotifikasiCetakKode').html(`
                        <div class="alert alert-danger">
                            Gagal membuat gambar.
                        </div>
                    `);

                    resetButton();

                });

            }

            //------------------------------------------------
            // DIRECT PRINT
            //------------------------------------------------
            else if(formatCetak === "Direct"){

                let printWindow = window.open(
                    '',
                    '',
                    'width=800,height=600'
                );

                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Cetak Kode Batch</title>

                            <link rel="stylesheet"
                                href="assets/vendor/bootstrap/css/bootstrap.min.css">

                            <style>
                                body{
                                    padding:20px;
                                    text-align:center;
                                }

                                .preview-kode-batch{
                                    border:1px solid #000;
                                    border-radius:12px;
                                    padding:15px;
                                    display:inline-block;
                                }

                                .qr-frame{
                                    border:1px solid #000;
                                    border-radius:8px;
                                    padding:8px;
                                    display:inline-block;
                                }
                            </style>
                        </head>

                        <body>
                            ${content.innerHTML}
                        </body>
                    </html>
                `);

                printWindow.document.close();

                setTimeout(function(){

                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();

                    $('#NotifikasiCetakKode').html(`
                        <div class="alert alert-success">
                            Pencetakan berhasil.
                        </div>
                    `);

                    resetButton();

                }, 1000);

            }

        }, 500);

    });
    


});