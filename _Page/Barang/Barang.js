// DOCUMEN READY FUNCTION
$(document).ready(function() {

    // ================================================================================
    // FUNCTION BLOCK
    // ================================================================================
    
    //Fungsi Menampilkan Data
    function ShowTable() {

        // Target And Filter
        let target = $('#tabel_barang');
        let data   = $('#ProsesFilter').serialize();

        target.addClass('blur-loading');

        $.ajax({
            type: 'POST',
            url: '_Page/Barang/TabelBarang.php',
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

    // Function Untuk Menampilkan Kode Barang
    function renderKodePreview() {

        let kode = $('#kode_barang_label').text().trim();
        let type = $('#type_code').val();

        let container = $('#PreviewBarcodeKode');

        // CLEAR aman (bukan "...")
        container.html(`
            <div class="text-center text-muted py-5">
                Loading...
            </div>
        `);

        if (!kode || kode === '-') {
            container.html('<div class="text-muted py-5">Tidak ada kode</div>');
            return;
        }

        setTimeout(() => {

            container.html(''); // 🔥 ini render ulang bersih

            if (type === 'qrcode') {
                let canvas = document.createElement("canvas");
                container.append(canvas);

                QRCode.toCanvas(canvas, kode);
                return;
            }

            let svg = document.createElement("svg");
            container.append(svg);

            JsBarcode(svg, kode, {
                format: (type === "code39") ? "CODE39" : "CODE128",
                lineColor: "#000",
                width: 2,
                height: 80,
                displayValue: true
            });

        }, 50);
    }

    function cetakHTML() {
        let kode = $('#kode_barang_label').text();
        let nama = $('#nama_barang_label').text();

        let printWindow = window.open('', '_blank');

        printWindow.document.write(`
            <html>
            <head>
                <title>Cetak Barcode</title>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode"></script>
            </head>
            <body style="text-align:center;padding:20px">

                <h3>${nama}</h3>

                <svg id="barcode"></svg>

                <script>
                    JsBarcode("#barcode", "${kode}", {
                        format: "CODE128",
                        displayValue: true
                    });

                    window.onload = function() {
                        window.print();
                    }
                </script>

            </body>
            </html>
        `);

        printWindow.document.close();
    }

    function cetakPNG() {
        let canvas = document.querySelector('#PreviewBarcodeKode canvas');

        if (!canvas) {
            alert("QR Code belum tersedia");
            return;
        }

        let link = document.createElement('a');
        link.download = 'barcode.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }

    function cetakPDF() {
        let data = $('#ProsesCetakKode').serialize();

        window.open('cetak_barcode.php?' + data, '_blank');
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
    // TAMBAH BARANG
    //------------------------------------------------
    
    // Modal Tambah
    $('#ModalTambah').on('shown.bs.modal', function () {
        // Auto Focus
        $('#kode').trigger('focus');

    });

    // Generate Nomor Induk Otomatis
    $(document).on('click', '.generate_kode_barang', function () {
        let kode = Math.floor(100000 + Math.random() * 900000);
        $('#kode').val(kode);
    });

    // Tom Select kategori
    new TomSelect('#kategori', {
        plugins     : ['virtual_scroll'],
        valueField  : 'value',
        labelField  : 'text',
        searchField : 'text',
        create      : true,
        createOnBlur: true,
        persist     : false,
        firstUrl    : function(query) {
            return '_Page/Barang/KategoriList.php?page=1&search=' + encodeURIComponent(query);
        },
        load: function(query, callback) {
            const url = this.getUrl(query);
            $.ajax({
                url     : url,
                type    : 'GET',
                dataType: 'json',
                error   : function() {
                    callback();
                },
                success: function(json) {
                    callback(json.data);
                    if (json.next_page) {
                        this.setNextUrl(
                            query,
                            '_Page/Barang/KategoriList.php?page=' +
                            json.next_page +
                            '&search=' +
                            encodeURIComponent(query)
                        );
                    }
                }.bind(this)
            });
        },
        shouldLoad: function() {
            return true;
        }
    });

    // Tom Select satuan
    new TomSelect('#satuan', {
        plugins     : ['virtual_scroll'],
        valueField  : 'value',
        labelField  : 'text',
        searchField : 'text',
        create      : true,
        createOnBlur: true,
        persist     : false,
        firstUrl    : function(query) {
            return '_Page/Barang/SatuanList.php?page=1&search=' + encodeURIComponent(query);
        },
        load: function(query, callback) {
            const url = this.getUrl(query);
            $.ajax({
                url     : url,
                type    : 'GET',
                dataType: 'json',
                error   : function() {
                    callback();
                },
                success: function(json) {
                    callback(json.data);
                    if (json.next_page) {
                        this.setNextUrl(
                            query,
                            '_Page/Barang/SatuanList.php?page=' +
                            json.next_page +
                            '&search=' +
                            encodeURIComponent(query)
                        );
                    }
                }.bind(this)
            });
        },
        shouldLoad: function() {
            return true;
        }
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
            url        : '_Page/Barang/ProsesTambah.php',
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

                    document.getElementById('kategori').tomselect.clear();
                    document.getElementById('satuan').tomselect.clear();

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

    //------------------------------------------------
    // EDIT
    //------------------------------------------------
    
    // Generate NIA Edit
    $(document).off('click', '.generate_kode_barang_edit');

    $(document).on('click', '.generate_kode_barang_edit', function () {
        let kode = Math.floor(100000 + Math.random() * 900000);
        $('#kode_edit').val(kode);
    });

    // Modal Edit
    $('#ModalEdit').on('shown.bs.modal', function (e) {

          // Tangkap ID Anggota
        let id_barang = $(e.relatedTarget).data('id');

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
            url    : '_Page/Barang/FormEdit.php',
            data   : { id_barang: id_barang },
            success: function (response) {
                $('#FormEdit').css('opacity', '0');
                setTimeout(function () {
                    $('#FormEdit').html(response);
                    $('#FormEdit').css({
                        filter : 'blur(0px)',
                        opacity: '1'
                    });

                    $( '.format_uang' ).mask('000.000.000.000', {reverse: true});

                    // Tom Select Kategori Edit
                    new TomSelect('#kategori_edit', {
                        plugins     : ['virtual_scroll'],
                        valueField  : 'value',
                        labelField  : 'text',
                        searchField : 'text',
                        create      : true,
                        createOnBlur: true,
                        persist     : false,

                        firstUrl: function(query){
                            return '_Page/Barang/KategoriList.php?page=1&search=' + encodeURIComponent(query);
                        },

                        load: function(query, callback){
                            const url = this.getUrl(query);

                            $.ajax({
                                url     : url,
                                type    : 'GET',
                                dataType: 'json',

                                error: function(){
                                    callback();
                                },

                                success: function(json){
                                    callback(json.data);

                                    if(json.next_page){
                                        this.setNextUrl(
                                            query,
                                            '_Page/Barang/KategoriList.php?page=' +
                                            json.next_page +
                                            '&search=' +
                                            encodeURIComponent(query)
                                        );
                                    }
                                }.bind(this)
                            });
                        },

                        shouldLoad: function(){
                            return true;
                        }
                    });

                    // Tom Select Satuan Edit
                    new TomSelect('#satuan_edit', {
                        plugins     : ['virtual_scroll'],
                        valueField  : 'value',
                        labelField  : 'text',
                        searchField : 'text',
                        create      : true,
                        createOnBlur: true,
                        persist     : false,

                        firstUrl: function(query){
                            return '_Page/Barang/SatuanList.php?page=1&search=' + encodeURIComponent(query);
                        },

                        load: function(query, callback){
                            const url = this.getUrl(query);

                            $.ajax({
                                url     : url,
                                type    : 'GET',
                                dataType: 'json',

                                error: function(){
                                    callback();
                                },

                                success: function(json){
                                    callback(json.data);

                                    if(json.next_page){
                                        this.setNextUrl(
                                            query,
                                            '_Page/Barang/SatuanList.php?page=' +
                                            json.next_page +
                                            '&search=' +
                                            encodeURIComponent(query)
                                        );
                                    }
                                }.bind(this)
                            });
                        },

                        shouldLoad: function(){
                            return true;
                        }
                    });

                    initTomSelectEdit();
                    
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
            url        : '_Page/Barang/ProsesEdit.php',
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
        var id_barang = $(e.relatedTarget).data('id');

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
            url    : '_Page/Barang/FormHapus.php',
            data   : {id_barang: id_barang},
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
            url        : '_Page/Barang/ProsesHapus.php',
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
        
        // Tangkap id_barang
        var id_barang = $(e.relatedTarget).data('id');
        
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
            url    : '_Page/Barang/FormInactive.php',
            data   : {id_barang: id_barang},
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
            url        : '_Page/Barang/ProsesInactive.php',
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
        
        // Tangkap id_barang
        var id_barang = $(e.relatedTarget).data('id');
        
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
            url    : '_Page/Barang/FormActive.php',
            data   : {id_barang: id_barang},
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
            url        : '_Page/Barang/ProsesActive.php',
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

    //------------------------------------------------
    // HARGA
    //------------------------------------------------
    
    // Modal Harga
    $('#ModalHarga').on('shown.bs.modal', function (e) {
        
        // Tangkap id_barang
        var id_barang = $(e.relatedTarget).data('id');
        
        // Kosongkan Notifikasi
        $('#NotifikasiHarga').html('');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormHarga')
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
            url    : '_Page/Barang/FormHarga.php',
            data   : {id_barang: id_barang},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormHarga').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormHarga').html(data);

                    // Hilangkan blur
                    $('#FormHarga').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });

                    $( '.format_uang' ).mask('000.000.000.000', {reverse: true});
                }, 200);
                
            },
            error: function () {
                $('#FormHarga').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormHarga').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });

    });

    // Handdle Proses Harga
    $('#ProsesHarga').submit(function(e){
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiHarga').html('');

        // Tombol Submit
        let tombol = $('#ButtonHarga');

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
            url        : '_Page/Barang/ProsesHarga.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    // Reset Notifikasi
                    $('#NotifikasiHarga').html(``);

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
                    $('#ModalHarga').modal('hide');

                    // Reload Tabel
                    ShowTable();
                }else{

                    // Show Notification Error
                    $('#NotifikasiHarga').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiHarga').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // IMPORT
    //------------------------------------------------

    // Saat modal dibuka
    $('#ModalImport').on('shown.bs.modal', function(){
        $('#file_import').val('');
        $('#ButtonImport').prop('disabled', true);
        $('#ButtonReset').prop('disabled', true);
        $('#NotifikasiImport').html(`
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text text-grayish">
                        Belum Ada Data Import
                    </small>
                </td>
            </tr>
        `);
    });

    // Saat File Dipilih
    $('#file_import').on('change',function(){
        // Tangkap File
        let file = this.files[0];

        // tetapkan Tombol
        $('#ButtonImport').prop('disabled',true);
        if(!file){
            return;
        }
        let ext = file.name.split('.').pop().toLowerCase();
        if(ext !== 'xlsx'){
            Swal.fire(
                'Error',
                'File harus berformat XLSX',
                'error'
            );

            $(this).val('');
            return;
        }
        if(file.size > (2 * 1024 * 1024)){

            Swal.fire(
                'Error',
                'Ukuran file maksimal 2 MB',
                'error'
            );

            $(this).val('');
            return;
        }
        $('#ButtonImport').prop('disabled',false);
    });

    // Submit Import
    $('#ProsesImport').submit(function(e){

        e.preventDefault();

        let tombol = $('#ButtonImport');
        let html_asli = tombol.html();

        tombol.prop('disabled', true);
        tombol.html(`
            <span class="spinner-border spinner-border-sm"></span>
            Processing...
        `);

        $('#NotifikasiImport').html(`
            <tr>
                <td colspan="10" class="text-center">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    Membaca file excel...
                </td>
            </tr>
        `);

        let formData = new FormData(this);

        $.ajax({
            url         : '_Page/Barang/ProsesImport.php',
            type        : 'POST',
            data        : formData,
            processData : false,
            contentType : false,

            success:function(response){

                $('#NotifikasiImport').html(response);

                $('#ButtonReset')
                    .prop('disabled', false);

            },

            error:function(){

                $('#NotifikasiImport').html(`
                    <tr>
                        <td colspan="10" class="text-center text-danger">
                            Terjadi kesalahan server
                        </td>
                    </tr>
                `);

            },

            complete:function(){

                tombol.prop('disabled', false);
                tombol.html(html_asli);

            }
        });

    });

    // Reset
    $(document).on('click','#ButtonReset',function(){

        $('#file_import').val('');

        $('#ButtonImport').prop('disabled', true);

        $('#ButtonReset').prop('disabled', true);

        $('#NotifikasiImport').html(`
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text text-grayish">
                        Belum Ada Data Import
                    </small>
                </td>
            </tr>
        `);

    });

    //------------------------------------------------
    // KODE BARANG
    //------------------------------------------------
    
    //Modal Detail Kode Barang
    $('#ModalDetailKode').on('show.bs.modal', function (e) {
        var id_barang= $(e.relatedTarget).data('id');

        //Tangkap Data Dari Form
        var type_code                      = $('#type_code').val();
        var tampilkan_nama_barang_for_code = $('#tampilkan_nama_barang_for_code').val();
        var kategori_harga_kode            = $('#kategori_harga_kode').val();
        
        //Kosongkan PreviewBarcodeKode
        $('#PreviewBarcodeKode').html("");

        // Kosongkan Notifikasi
        $('#NotifikasiCetakKode').html("");

        // Tampilkan Kode
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Barang/FormDetailKodebarang.php',
            data        : {id_barang: id_barang, type_code: type_code, tampilkan_nama_barang_for_code: tampilkan_nama_barang_for_code, kategori_harga_kode: kategori_harga_kode},
            success     : function(response){
                $('#PreviewBarcodeKode').html(response);
            }
        });

        //Apabila Kategori Harga Diubah
        $("#kategori_harga_kode").on("change", function (e) {
            var type_code=$('#type_code').val();
            var tampilkan_nama_barang_for_code=$('#tampilkan_nama_barang_for_code').val();
            var kategori_harga_kode=$('#kategori_harga_kode').val();
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Barang/FormDetailKodebarang.php',
                data        : {id_barang: id_barang, type_code: type_code, tampilkan_nama_barang_for_code: tampilkan_nama_barang_for_code, kategori_harga_kode: kategori_harga_kode},
                success     : function(response){
                    $('#PreviewBarcodeKode').html(response);
                }
            });
        });

        //Apabila tampilkan_nama_barang_for_code Diubah
        $("#tampilkan_nama_barang_for_code").on("change", function (e) {
            var type_code=$('#type_code').val();
            var tampilkan_nama_barang_for_code=$('#tampilkan_nama_barang_for_code').val();
            var kategori_harga_kode=$('#kategori_harga_kode').val();
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Barang/FormDetailKodebarang.php',
                data        : {id_barang: id_barang, type_code: type_code, tampilkan_nama_barang_for_code: tampilkan_nama_barang_for_code, kategori_harga_kode: kategori_harga_kode},
                success     : function(response){
                    $('#PreviewBarcodeKode').html(response);
                }
            });
        });

        //Apabila type_code Diubah
        $("#type_code").on("change", function (e) {
            var type_code=$('#type_code').val();
            var tampilkan_nama_barang_for_code=$('#tampilkan_nama_barang_for_code').val();
            var kategori_harga_kode=$('#kategori_harga_kode').val();
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Barang/FormDetailKodebarang.php',
                data        : {id_barang: id_barang, type_code: type_code, tampilkan_nama_barang_for_code: tampilkan_nama_barang_for_code, kategori_harga_kode: kategori_harga_kode},
                success     : function(response){
                    $('#PreviewBarcodeKode').html(response);
                }
            });
        });
    });

    function resetButton() {
        $('#ButtonCetakKode').prop('disabled', false).html('<i class="bi bi-printer"></i> Cetak Code');
    }
    $('#ProsesCetakCodeBarang').submit(function(event) {
        event.preventDefault(); // Mencegah reload halaman
        
        var formatCetak = $('#type_file_code').val(); // Ambil format yang dipilih
        var content = document.getElementById("PreviewBarcodeKode"); // Ambil elemen yang ingin dicetak

        if (!formatCetak) {
            $('#NotifikasiCetakKode').html('<div class="alert alert-danger">Silakan pilih format cetak!</div>');
            return;
        }

        // Tampilkan loading
        $('#NotifikasiCetakKode').html('<div class="alert alert-info">Sedang memproses cetak...</div>');
        $('#ButtonCetakKode').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Memproses...');

        setTimeout(() => {
            if (formatCetak === "PDF") {
                html2canvas(content, { scale: 2 }).then(canvas => {
                    var imgData = canvas.toDataURL("image/png");
                    var { jsPDF } = window.jspdf;
                    var doc = new jsPDF("p", "mm", "a4");
                    var imgWidth = 190;
                    var imgHeight = (canvas.height * imgWidth) / canvas.width;

                    doc.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);
                    doc.save("kode_barang.pdf");

                    $('#NotifikasiCetakKode').html('<div class="alert alert-success">Download PDF berhasil!</div>');
                    resetButton();
                }).catch(error => {
                    $('#NotifikasiCetakKode').html('<div class="alert alert-danger">Gagal mencetak PDF!</div>');
                    resetButton();
                });
            } else if (formatCetak === "Image") {
                html2canvas(content, { scale: 2 }).then(canvas => {
                    var imgData = canvas.toDataURL("image/png");
                    var link = document.createElement("a");
                    link.href = imgData;
                    link.download = "kode_barang.png";
                    link.click();

                    $('#NotifikasiCetakKode').html('<div class="alert alert-success">Download gambar berhasil!</div>');
                    resetButton();
                }).catch(error => {
                    $('#NotifikasiCetakKode').html('<div class="alert alert-danger">Gagal mencetak gambar!</div>');
                    resetButton();
                });
            } else if (formatCetak === "Direct") {
                var printWindow = window.open("", "", "width=800,height=600");
                printWindow.document.write('<html><head><title>Cetak Nota</title>');
                printWindow.document.write('<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">');
                printWindow.document.write('</head><body>');
                printWindow.document.write(content.innerHTML);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                    $('#NotifikasiCetakKode').html('<div class="alert alert-success">Pencetakan berhasil!</div>');
                    resetButton();
                }, 1000);
            }
        }, 1000);
    });
    
});
