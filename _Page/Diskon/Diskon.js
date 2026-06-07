// DOCUMEN READY FUNCTION
$(document).ready(function() {

    // ================================================================================
    // FUNCTION BLOCK
    // ================================================================================
    
    //Fungsi Menampilkan Data
    function ShowTable() {

        // Target And Filter
        let target = $('#tabel_diskon');
        let data   = $('#ProsesFilter').serialize();

        target.addClass('blur-loading');

        $.ajax({
            type: 'POST',
            url: '_Page/Diskon/TabelDiskon.php',
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

    // Function Untuk Inisialiasasi Tom Select Pada Form Edit
    function initTomSelectEdit(){
        if(document.getElementById('kategori_edit')){
            new TomSelect('#kategori_edit',{
                // konfigurasi kategori
            });
        }
        if(document.getElementById('satuan_edit')){
            new TomSelect('#satuan_edit',{
                // konfigurasi satuan
            });
        }
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
            url        : '_Page/Diskon/FormFilter.php',
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

    
    //----------------------------------------------------
    // TAMBAH
    //----------------------------------------------------
   
    //----------------------------------------------------
    // TOM SELECT BARANG
    //----------------------------------------------------
    let tomSelectBarang = null;

    $('#ModalTambah').on('shown.bs.modal', function () {
        // Hindari inisialisasi berulang
        if ($('#id_barang')[0].tomselect) {
            return;
        }

        tomSelectBarang = new TomSelect('#id_barang', {
            valueField : 'id_barang',
            labelField : 'label',
            searchField: false,
            preload    : true,
            create     : false,
            maxOptions : null,
            persist    : false,
            render: {
                option: function(item, escape) {
                    return `
                        <div class="py-1">
                            <div>
                                <strong>${escape(item.nama)}</strong>
                            </div>
                            <div>
                                <small class="text-muted">${escape(item.kode)}</small>
                            </div>
                        </div>
                    `;
                },
                item: function(item, escape) {
                    return `
                        <div>
                            ${escape(item.nama)} (${escape(item.kode)})
                        </div>
                    `;
                }
            },
            load: function(query, callback) {
                let self = this;
                self.keyword    = query;
                self.currentPage = 1;

                $.ajax({
                    url      : '_Page/Diskon/SelectBarang.php',
                    type     : 'POST',
                    dataType : 'JSON',
                    data     : {
                        keyword : query,
                        page    : 1
                    },
                    success: function(response){
                        self.clearOptions();
                        if(response.status === 'success'){
                            self.hasMore = response.has_more;
                            callback(response.data);
                        }else{
                            callback([]);
                        }
                    },
                    error: function(){
                        callback([]);
                    }
                });
            },
            onInitialize: function(){
                let self = this;
                self.keyword     = '';
                self.currentPage = 1;
                self.hasMore     = true;

                self.on('type', function(str){
                    self.keyword     = str;
                    self.currentPage = 1;
                    self.hasMore     = true;
                });

                self.on('dropdown_open', function(){
                    let dropdownContent = self.dropdown_content;
                    $(dropdownContent).off('scroll.lazyload');
                    $(dropdownContent).on('scroll.lazyload', function(){
                        if(!self.hasMore){
                            return;
                        }

                        let scrollTop    = this.scrollTop;
                        let scrollHeight = this.scrollHeight;
                        let clientHeight = this.clientHeight;

                        if(scrollTop + clientHeight >= scrollHeight - 30){
                            self.hasMore = false;
                            self.currentPage++;

                            $.ajax({
                                url      : '_Page/Diskon/SelectBarang.php',
                                type     : 'POST',
                                dataType : 'JSON',
                                data     : {
                                    keyword : self.keyword,
                                    page    : self.currentPage
                                },
                                success: function(response){
                                    if(response.status === 'success'){
                                        if(response.data.length > 0){
                                            response.data.forEach(function(item){
                                                self.addOption(item);
                                            });
                                            self.refreshOptions(false);
                                        }
                                        self.hasMore = response.has_more;
                                    }else{
                                        self.hasMore = false;
                                    }
                                },
                                error: function(){
                                    self.hasMore = false;
                                }
                            });
                        }
                    });
                });
            }
        });
    });

    // Handdle Proses Tambah
    $('#ProsesTambah').submit(function(e){
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiTambah').html('');

        // Tombol sUBMIT
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
            url        : '_Page/Diskon/ProsesTambah.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

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

                    // Reset form
                    $('#ProsesTambah')[0].reset();

                    // Reset Filter
                    $('#ProsesFilter')[0].reset();

                    // Hide Modal
                    $('#ModalTambah').modal('hide');

                    // Reload Tabel
                    ShowTable();
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
    // DETAIL
    //------------------------------------------------
    
    // Modal Detail
    $('#ModalDetail').on('shown.bs.modal', function (e) {
        // Tangkap id_barang
        var id_barang_diskon = $(e.relatedTarget).data('id');

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
            url    : '_Page/Diskon/FormDetail.php',
            data   : {id_barang_diskon: id_barang_diskon},
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

        // Tangkap ID Anggota
        let id_barang_diskon = $(e.relatedTarget).data('id');

          // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');
        
          // Loading Effect
        $('#FormEdit').css({
            filter    : 'blur(4px)',
            opacity   : '0.5',
            transition: 'all 0.3s ease'
        }).html(`
            <div class = "text-center py-5">
            <div class = "spinner-border text-primary mb-2"></div>
                <div>Loading...</div>
            </div>
        `);

        // Buka Form Dengan Ajax
        $.ajax({
            type   : 'POST',
            url    : '_Page/Diskon/FormEdit.php',
            data   : { id_barang_diskon: id_barang_diskon },
            success: function (response) {
                $('#FormEdit').css('opacity', '0');
                setTimeout(function () {
                    $('#FormEdit').html(response);
                    $('#FormEdit').css({
                        filter : 'blur(0px)',
                        opacity: '1'
                    });
                    
                }, 200);
            },
            error: function () {
                $('#FormEdit').html(`
                    <div class = "alert alert-danger text-center mb-0">
                        Terjadi kesalahan saat memuat data.
                    </div>
                `);
                $('#FormEdit').css({
                    filter : 'blur(0px)',
                    opacity: '1'
                });
            }
        });
    });

    // Modal Edit Hide
    $('#ModalEdit').on('hidden.bs.modal', function () {

        $('#FormEdit').html('');
        $('#NotifikasiEdit').html('');

    });

    // Handdle Proses Edit
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
            url        : '_Page/Diskon/ProsesEdit.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    // Reset Notifikasi
                    $('#NotifikasiEdit').html(``);

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
                    ShowTable();
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
    // HAPUS
    //------------------------------------------------
    
    // Modal Hapus
    $('#ModalHapus').on('shown.bs.modal', function (e) {
        
        // Tangkap id_barang
        var id_barang_diskon = $(e.relatedTarget).data('id');

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
            url    : '_Page/Diskon/FormHapus.php',
            data   : {id_barang_diskon: id_barang_diskon},
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
            url        : '_Page/Diskon/ProsesHapus.php',
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

    
    
});
