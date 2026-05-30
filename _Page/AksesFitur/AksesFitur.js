//Fungsi Menampilkan Data
function filterAndLoadTable() {
    let target = $('#MenampilkanTabelFitur');
    let data   = $('#ProsesFilter').serialize();

    target.addClass('blur-loading');

    $.ajax({
        type: 'POST',
        url: '_Page/AksesFitur/TabelAksesFitur.php',
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
//Fungsi Generate Kode
function generateRandomString(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}
//Menampilkan Data Pertama Kali
$(document).ready(function() {
    filterAndLoadTable();
    
    //Reload
    $(document).on('click', '#ReloadData', function() {
        // Reset Filter
        $('#ProsesFilter')[0].reset();
        $('#page').val('1');
        filterAndLoadTable();
    });

    //Pagging Next
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        filterAndLoadTable(0);
    });

    //Pagging Previous
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        filterAndLoadTable(0);
    });
    
    //Submit Filter Data
    $('#ProsesFilter').submit(function(){
        $('#page').val("1");
        filterAndLoadTable();
        $('#ModalFilter').modal('hide');
    });
    
    //KeywordBy Changed
    $('#KeywordBy').change(function(){
        var KeywordBy = $('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AksesFitur/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //=======================================
    // TAMBAH FITUR
    //=======================================

    //Generate Kode
     $(document).on('click', '.generate_kode_fitur', function() {
        var randomString = generateRandomString(19);
        $('.kode_fitur').val(randomString);
    });

    //Proses Tambah Fitur
    $('#ProsesTambahFitur').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);

        // Simpan & ubah tombol jadi loading
        let button = $('#ButtonTambahAksesFitur');
        let buttonText = button.html();
        button.html('...').prop('disabled', true);

        // Reset notifikasi
        $('#NotifikasiTambahAksesFitur').html('');

        // Kirim data
        $.ajax({
            type: 'POST',
            url: '_Page/AksesFitur/ProsesTambahFitur.php',
            data: form.serialize(),
            dataType: 'json',
            success: function (response) {

                if (response.status === 'success') {

                    // Reset paging & filter
                    $('#page').val("1");
                    $("#ProsesFilter")[0].reset();

                    // Reset form
                    form[0].reset();

                    // Tutup modal
                    $('#ModalTambahFitur').modal('hide');

                    // Notifikasi sukses
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : 'Tambah Fitur Akses Berhasil!',
                        showConfirmButton: false,
                        timer            : 2000,
                        timerProgressBar : true
                    });

                    // Reload data
                    filterAndLoadTable();

                } else {
                    $('#NotifikasiTambahAksesFitur').html(
                        '<div class="alert alert-danger"><small>' +
                        (response.message || 'Terjadi kesalahan') +
                        '</small></div>'
                    );
                }
            },
            error: function (xhr) {
                $('#NotifikasiTambahAksesFitur').html(
                    '<div class="alert alert-danger"><small>Server error</small></div>'
                );
            },
            complete: function () {
                // restore tombol (selalu jalan)
                button.html(buttonText).prop('disabled', false);
            }
        });
    });

    //=======================================
    // DETAIL FITUR
    //=======================================
    $('#ModalDetailFitur').on('show.bs.modal', function (e) {
        var id_akses_fitur = $(e.relatedTarget).data('id');
        $('#FormDetailFitur').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AksesFitur/FormDetailFitur.php',
            data        : {id_akses_fitur: id_akses_fitur},
            success     : function(data){
                $('#FormDetailFitur').html(data);
            }
        });
    });

    //=======================================
    // EDIT FITUR
    //=======================================
    $('#ModalEditFitur').on('show.bs.modal', function (e) {
        // Catch id_akses_fitur 
        var id_akses_fitur = $(e.relatedTarget).data('id');

        // Clear Notification
        $('#NotifikasiEditFitur').html('');

        // Loading Form
        $('#FormEditFitur').html("Loading...");

        // Show Form With AJAX
        $.ajax({
            type    : 'POST',
            url     : '_Page/AksesFitur/FormEditFitur.php',
            data    : {id_akses_fitur: id_akses_fitur},
            success : function(data){
                $('#FormEditFitur').html(data);
            }
        });
    });

    //Proses Edit Fitur
    $('#ProsesEditFitur').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);

        // Simpan & ubah tombol jadi loading
        let button     = $('#ButtonEditFitur');
        let buttonText = button.html();
        button.html('...').prop('disabled', true);

        // Reset notifikasi
        $('#NotifikasiEditFitur').html('');

        // Kirim data
        $.ajax({
            type: 'POST',
            url: '_Page/AksesFitur/ProsesEditFitur.php',
            data: form.serialize(),
            dataType: 'json',
            success: function (response) {

                if (response.status === 'success') {

                    // Tutup modal
                    $('#ModalEditFitur').modal('hide');

                    // Notifikasi sukses
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : 'Edit Fitur Akses Berhasil!',
                        showConfirmButton: false,
                        timer            : 2000,
                        timerProgressBar : true
                    });

                    // Reload data
                    filterAndLoadTable();

                } else {
                    $('#NotifikasiEditFitur').html(
                        '<div class="alert alert-danger"><small>' +
                        (response.message || 'Terjadi kesalahan') +
                        '</small></div>'
                    );
                }
            },
            error: function (xhr) {
                $('#NotifikasiEditFitur').html(
                    '<div class="alert alert-danger"><small>Server error</small></div>'
                );
            },
            complete: function () {
                // restore tombol (selalu jalan)
                button.html(buttonText).prop('disabled', false);
            }
        });
    });

    //=======================================
    // HAPUS FITUR
    //=======================================
    $('#ModalHapusFitur').on('show.bs.modal', function (e) {
        var id_akses_fitur = $(e.relatedTarget).data('id');

        // Clear Notification
        $('#NotifikasiHapusFitur').html('');

        // Loading Form
        $('#FormHapusFitur').html("Loading...");

        // Open Form With Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AksesFitur/FormHapusFitur.php',
            data        : {id_akses_fitur: id_akses_fitur},
            success     : function(data){
                $('#FormHapusFitur').html(data);
            }
        });
    });

    //Submit ProsesHapusFitur
    $('#ProsesHapusFitur').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);

        // Simpan & ubah tombol jadi loading
        let button     = $('#ButtonHapusFitur');
        let buttonText = button.html();
        button.html('...').prop('disabled', true);

        // Reset notifikasi
        $('#NotifikasiHapusFitur').html('');

        // Kirim data
        $.ajax({
            type: 'POST',
            url: '_Page/AksesFitur/ProsesHapusFitur.php',
            data: form.serialize(),
            dataType: 'json',
            success: function (response) {

                if (response.status === 'success') {

                    // Tutup modal
                    $('#ModalHapusFitur').modal('hide');

                    // Notifikasi sukses
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : 'Hapus Fitur Berhasil!',
                        showConfirmButton: false,
                        timer            : 2000,
                        timerProgressBar : true
                    });

                    // Reload data
                    filterAndLoadTable();

                } else {
                    $('#NotifikasiHapusFitur').html(
                        '<div class="alert alert-danger"><small>' +
                        (response.message || 'Terjadi kesalahan') +
                        '</small></div>'
                    );
                }
            },
            error: function (xhr) {
                $('#NotifikasiHapusFitur').html(
                    '<div class="alert alert-danger"><small>Server error</small></div>'
                );
            },
            complete: function () {
                // restore tombol (selalu jalan)
                button.html(buttonText).prop('disabled', false);
            }
        });
    });

    //=======================================
    // MODAL ENTITAS
    //=======================================
    $('#ModalEntitas').on('show.bs.modal', function (e) {
        var id_akses_fitur = $(e.relatedTarget).data('id');
        $('#FormEntitas').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AksesFitur/FormEntitas.php',
            data        : {id_akses_fitur: id_akses_fitur},
            success     : function(data){
                $('#FormEntitas').html(data);
            }
        });
    });

    //=======================================
    // MODAL PENGGUNA
    //=======================================
    $('#ModalPengguna').on('show.bs.modal', function (e) {
        var id_akses_fitur = $(e.relatedTarget).data('id');
        $('#FormPengguna').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AksesFitur/FormPengguna.php',
            data        : {id_akses_fitur: id_akses_fitur},
            success     : function(data){
                $('#FormPengguna').html(data);
            }
        });
    });


});
