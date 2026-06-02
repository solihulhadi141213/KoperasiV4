// DOCUMEN READY FUNCTION
$(document).ready(function() {
    
    // ================================================================================
    // FUNCTION BLOCK
    // ================================================================================
    
    //Fungsi Menampilkan Data
    function ShowTable() {

        // Target And Filter
        let target = $('#tabel_jenis_pinjaman');
        let data   = $('#ProsesFilter').serialize();

        target.addClass('blur-loading');

        $.ajax({
            type: 'POST',
            url: '_Page/JenisPinjaman/TabelJenisPinjaman.php',
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
            url        : '_Page/JenisPinjaman/FormFilter.php',
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
    // TAMBAH
    //------------------------------------------------
    
    // Modal Tambah
    $('#ModalTambah').on('shown.bs.modal', function () {

        // Reset Form
        $('#ProsesTambah')[0].reset();

        // Kosongkan Notifikasi
        $('#NotifikasiTambah').html('');

        // Disable Denda Nominal
        $('#denda_nominal')
            .val('')
            .prop('disabled', true)
            .removeAttr('required');

        // Fokus
        $('#nama_pinjaman').trigger('focus');

    });

    // Metode Denda
    $('#denda_metode').change(function(){

        let metode = $(this).val();

        if(metode === 'Harian' || metode === 'Bulanan'){

            $('#denda_nominal')
                .prop('disabled', false)
                .attr('required', true)
                .trigger('focus');

        }else{

            $('#denda_nominal')
                .val('')
                .prop('disabled', true)
                .removeAttr('required');

        }

    });

    // HANDLE SUBMIT TAMBAH
    $('#ProsesTambah').submit(function(e) {
        e.preventDefault();

        // Kosongkan Notifikasi
        $('#NotifikasiTambah').html('');

        // Handdle Tombol
        let tombol      = $('#ButtonTambah');
        let tombol_asli = tombol.html();
        tombol.prop('disabled', true);
        tombol.html(`
            <span class = "spinner-border spinner-border-sm me-2"></span>
            Menyimpan...
        `);
        
        // Kirim Data Dengan Ajax
        $.ajax({
            url     : '_Page/JenisPinjaman/ProsesTambah.php',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success: function(response) {
                
                // Jika Berhasil
                if (response.status === 'success') {

                     // Tutup modal
                    $('#ModalTambah').modal('hide');

                    // Reset form
                    $('#ProsesTambah')[0].reset();
                    $('#denda_nominal').prop('disabled', true);
                    $('#NotifikasiTambah').html('');

                      // Toast sukses
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : response.message,
                        showConfirmButton: false,
                        timer            : 3000,
                        timerProgressBar : true
                    });

                    // Reset Filter
                    $('#ProsesFilter')[0].reset();

                    // Reload table
                    ShowTable();
                
                // Jika Gagal
                } else {
                    $('#NotifikasiTambah').html(`
                        <div class = "alert alert-danger">
                            ${response.message}
                        </div>
                    `);
                }
            },

            // Jika Error
            error: function(xhr, status, error) {
                $('#NotifikasiTambah').html(`
                    <div class = "alert alert-danger">
                        Terjadi kesalahan saat menghubungi server.
                    </div>
                `);
                console.log(xhr.responseText);
            },

            complete: function() {
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // DETAIL
    //------------------------------------------------
    
    // Modal Detail
    $('#ModalDetail').on('shown.bs.modal', function (e) {
        // Tangkap id_pinjaman_jenis
        var id_pinjaman_jenis = $(e.relatedTarget).data('id');

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
            url    : '_Page/JenisPinjaman/FormDetail.php',
            data   : {id_pinjaman_jenis: id_pinjaman_jenis},
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

    //------------------------------------------------
    // EDIT
    //------------------------------------------------
   
    // Modal Edit
    $('#ModalEdit').on('shown.bs.modal', function (e) {
        // Tangkap id_pinjaman_jenis
        var id_pinjaman_jenis = $(e.relatedTarget).data('id');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormEdit')
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
            url    : '_Page/JenisPinjaman/FormEdit.php',
            data   : {id_pinjaman_jenis: id_pinjaman_jenis},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormEdit').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormEdit').html(data);

                    $('#nama_pinjaman_edit').trigger('focus');

                    // Inisialiasai Ulang Format Rupiah
                    $( '.format_uang' ).mask('000.000.000.000', {reverse: true});

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

    // Metode Denda Edit Dengan Pendelegasian
    $(document).on('change', '#denda_metode_edit', function(){
        let metode = $(this).val();
        if(metode === 'Harian' || metode === 'Bulanan'){
            $('#denda_nominal_edit')
                .prop('disabled', false)
                .attr('required', true)
                .trigger('focus');
        }else{
            $('#denda_nominal_edit')
                .val('')
                .prop('disabled', true)
                .removeAttr('required');
        }

    });

    // HANDLE SUBMIT EDIT
    $('#ProsesEdit').submit(function(e) {
        e.preventDefault();

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        // Handdle Tombol
        let tombol      = $('#ButtonEdit');
        let tombol_asli = tombol.html();
        tombol.prop('disabled', true);
        tombol.html(`
            <span class = "spinner-border spinner-border-sm me-2"></span>
            Menyimpan...
        `);
        
        // Kirim Data Dengan AJAX
        $.ajax({
            url     : '_Page/JenisPinjaman/ProsesEdit.php',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',

            success: function(response) {

                if (response.status === 'success') {

                    // Tutup modal
                    $('#ModalEdit').modal('hide');

                    // Toast sukses
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : response.message,
                        showConfirmButton: false,
                        timer            : 3000,
                        timerProgressBar : true
                    });

                    // Reload table
                    ShowTable();

                } else {
                    $('#NotifikasiEdit').html(`
                        <div class = "alert alert-danger">
                            ${response.message}
                        </div>
                    `);
                }
            },

            error: function(xhr, status, error) {
                $('#NotifikasiEdit').html(`
                    <div class = "alert alert-danger">
                        Terjadi kesalahan saat menghubungi server.
                    </div>
                `);
                console.log(xhr.responseText);
            },

            complete: function() {
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // HAPUS
    //------------------------------------------------
    
    // Modal Hapus
    $('#ModalHapus').on('shown.bs.modal', function (e) {
        
        // Tangkap id_pinjaman_jenis
        var id_pinjaman_jenis = $(e.relatedTarget).data('id');

        // Kosongkan Notifikasi
        $('#NotifikasiHapus').html('');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormHapus')
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
            url    : '_Page/JenisPinjaman/FormHapus.php',
            data   : {id_pinjaman_jenis: id_pinjaman_jenis},
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

    // Handdle Proses Hapus
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
            url        : '_Page/JenisPinjaman/ProsesHapus.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    // Reset Notifikasi
                    $('#NotifikasiHapus').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : response.message,
                        showConfirmButton: false,
                        timer            : 3000,
                        timerProgressBar : true,
                        didOpen          : (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalHapus').modal('hide');

                    // Reload Tabel
                    ShowTable();
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
    // INACTIVE
    //------------------------------------------------
    
    // Modal Inactive
    $('#ModalInactive').on('shown.bs.modal', function (e) {
        
        // Tangkap id_pinjaman_jenis
        var id_pinjaman_jenis = $(e.relatedTarget).data('id');
        
        // Kosongkan Notifikasi
        $('#NotifikasiInactive').html('');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormInactive')
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
            url    : '_Page/JenisPinjaman/FormInactive.php',
            data   : {id_pinjaman_jenis: id_pinjaman_jenis},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormInactive').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormInactive').html(data);

                    // Hilangkan blur
                    $('#FormInactive').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormInactive').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormInactive').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });

    });

    // Handdle Proses Inactive
    $('#ProsesInactive').submit(function(e){
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiInactive').html('');

        // Tombol Submit
        let tombol = $('#ButtonInactive');

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
            url        : '_Page/JenisPinjaman/ProsesInactive.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    // Reset Notifikasi
                    $('#NotifikasiInactive').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : response.message,
                        showConfirmButton: false,
                        timer            : 3000,
                        timerProgressBar : true,
                        didOpen          : (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalInactive').modal('hide');

                    // Reload Tabel
                    ShowTable();
                }else{

                    // Show Notification Error
                    $('#NotifikasiInactive').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiInactive').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // ACTIVE
    //------------------------------------------------
    
    // Modal Active
    $('#ModalActive').on('shown.bs.modal', function (e) {
        
        // Tangkap id_pinjaman_jenis
        var id_pinjaman_jenis = $(e.relatedTarget).data('id');
        
        // Kosongkan Notifikasi
        $('#NotifikasiActive').html('');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormActive')
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
            url    : '_Page/JenisPinjaman/FormActive.php',
            data   : {id_pinjaman_jenis: id_pinjaman_jenis},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormActive').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormActive').html(data);

                    // Hilangkan blur
                    $('#FormActive').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormActive').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormActive').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });

    });

    // Handdle Proses Active
    $('#ProsesActive').submit(function(e){
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiActive').html('');

        // Tombol Submit
        let tombol = $('#ButtonActive');

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
            url        : '_Page/JenisPinjaman/ProsesActive.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    // Reset Notifikasi
                    $('#NotifikasiActive').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : response.message,
                        showConfirmButton: false,
                        timer            : 3000,
                        timerProgressBar : true,
                        didOpen          : (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalActive').modal('hide');

                    // Reload Tabel
                    ShowTable();
                }else{

                    // Show Notification Error
                    $('#NotifikasiActive').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiActive').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });



});