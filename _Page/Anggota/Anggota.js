// DOCUMEN READY FUNCTION
$(document).ready(function() {

    // ================================================================================
    // FUNCTION BLOCK
    // ================================================================================
    
    //Fungsi Menampilkan Data
    function ShowTable() {

        // Target And Filter
        let target = $('#tabel_anggota');
        let data   = $('#ProsesFilter').serialize();

        target.addClass('blur-loading');

        $.ajax({
            type: 'POST',
            url: '_Page/Anggota/TabelAnggota.php',
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
            url        : '_Page/Anggota/FormFilter.php',
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
    // TAMBAH ANGGOTA
    //------------------------------------------------
    
    // Modal Tambah
    $('#ModalTambah').on('shown.bs.modal', function () {
        // Auto Focus
        $('#nia').trigger('focus');

    });

    // Generate Nomor Induk Otomatis
    $(document).on('click', '.generate_nia', function () {
        let nia = Math.floor(100000 + Math.random() * 900000);
        $('#nia').val(nia);
    });

    // Tom Select organization_tag
    new TomSelect('#organization_tag', {
        plugins: ['virtual_scroll'],
        valueField: 'value',
        labelField: 'text',
        searchField: 'text',
        create: true,
        createOnBlur: true,
        persist: false,
        firstUrl: function(query) {
            return '_Page/Anggota/OrganizationTag.php?page=1&search=' + encodeURIComponent(query);
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
                            '_Page/Anggota/OrganizationTag.php?page=' +
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

    // Tom Select Rank Tag
    const rankTagSelect = new TomSelect('#rank_tag', {
        valueField: 'value',
        labelField: 'text',
        searchField: 'text',

        plugins: ['virtual_scroll'],

        create: function(input) {

            input = input.trim();

            // hanya angka
            if (!/^\d+$/.test(input)) {
                return false;
            }

            return {
                value: input,
                text: input
            };
        },

        preload: true,

        firstUrl: function(query) {

            return '_Page/Anggota/RankTag.php?page=1&search=' +
                encodeURIComponent(query);
        },

        load: function(query, callback) {

            const url = this.getUrl(query);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',

                error: function() {
                    callback();
                },

                success: function(json) {

                    callback(json.data);

                    if (json.next_page) {

                        this.setNextUrl(
                            query,
                            '_Page/Anggota/RankTag.php?page=' +
                            json.next_page +
                            '&search=' +
                            encodeURIComponent(query)
                        );

                    }

                }.bind(this)
            });

        },

        render: {
            option: function(item, escape) {
                return '<div>' + escape(item.text) + '</div>';
            },
            item: function(item, escape) {
                return '<div>' + escape(item.text) + '</div>';
            }
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
            url        : '_Page/Anggota/ProsesTambah.php',
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
        // Tangkap id_anggota
        var id_anggota = $(e.relatedTarget).data('id');

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
            url    : '_Page/Anggota/FormDetail.php',
            data   : {id_anggota: id_anggota},
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
    $(document).off('click', '.generate_nia_edit');

    $(document).on('click', '.generate_nia_edit', function () {
        let nia = Math.floor(100000 + Math.random() * 900000);
        $('#nia_edit').val(nia);
    });

    // Modal Edit
    $('#ModalEdit').on('shown.bs.modal', function (e) {

          // Tangkap ID Anggota
        let id_anggota = $(e.relatedTarget).data('id');

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
            url    : '_Page/Anggota/FormEdit.php',
            data   : { id_anggota: id_anggota },
            success: function (response) {
                $('#FormEdit').css('opacity', '0');
                setTimeout(function () {
                    $('#FormEdit').html(response);
                    $('#FormEdit').css({
                        filter : 'blur(0px)',
                        opacity: '1'
                    });
                    
                      // ORGANIZATION TAG
                    let organizationTagSelect = new TomSelect('#organization_tag_edit', {
                        plugins     : ['virtual_scroll'],
                        valueField  : 'value',
                        labelField  : 'text',
                        searchField : 'text',
                        create      : true,
                        createOnBlur: true,
                        persist     : false,
                        preload     : true,
                        firstUrl    : function(query) {
                            return '_Page/Anggota/OrganizationTag.php?page=1&search=' + encodeURIComponent(query);
                        },
                        load: function(query, callback) {
                            let url = this.getUrl(query);
                            $.ajax({
                                url     : url,
                                type    : 'GET',
                                dataType: 'json',
                                error   : function() { callback(); },
                                success : function(json) {
                                    callback(json.data);
                                    if (json.next_page) {
                                        this.setNextUrl(query, '_Page/Anggota/OrganizationTag.php?page=' + json.next_page + '&search=' + encodeURIComponent(query));
                                    }
                                }.bind(this)
                            });
                        },
                        shouldLoad: function() { return true; }
                    });

                    // Set Existing Value
                    let organizationValue = $('#organization_tag_edit option:selected').val();
                    if (organizationValue) {
                        organizationTagSelect.addOption({ value: organizationValue, text: organizationValue });
                        organizationTagSelect.setValue(organizationValue, true);
                    }

                    // RANK TAG
                    let rankTagSelect = new TomSelect('#rank_tag_edit', {
                        plugins    : ['virtual_scroll'],
                        valueField : 'value',
                        labelField : 'text',
                        searchField: 'text',
                        preload    : true,
                        create     : function(input) {
                            input = input.trim();
                            if (!/^\d+$/.test(input)) { return false; }
                            return { value: input, text: input };
                        },
                        firstUrl: function(query) {
                            return '_Page/Anggota/RankTag.php?page=1&search=' + encodeURIComponent(query);
                        },
                        load: function(query, callback) {
                            let url = this.getUrl(query);
                            $.ajax({
                                url     : url,
                                type    : 'GET',
                                dataType: 'json',
                                error   : function() { callback(); },
                                success : function(json) {
                                    callback(json.data);
                                    if (json.next_page) {
                                        this.setNextUrl(query, '_Page/Anggota/RankTag.php?page=' + json.next_page + '&search=' + encodeURIComponent(query));
                                    }
                                }.bind(this)
                            });
                        },
                        render: {
                            option: function(item, escape) { return '<div>' + escape(item.text) + '</div>'; },
                            item  : function(item, escape) { return '<div>' + escape(item.text) + '</div>'; }
                        }
                    });
                      // Set Existing Value
                    let rankValue = $('#rank_tag_edit option:selected').val();
                    if (rankValue) {
                        rankTagSelect.addOption({ value: rankValue, text: rankValue });
                        rankTagSelect.setValue(rankValue, true);
                    }
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
            url        : '_Page/Anggota/ProsesEdit.php',
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
        
        // Tangkap id_anggota
        var id_anggota = $(e.relatedTarget).data('id');

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
            url    : '_Page/Anggota/FormHapus.php',
            data   : {id_anggota: id_anggota},
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
            url        : '_Page/Anggota/ProsesHapus.php',
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
        
        // Tangkap id_anggota
        var id_anggota = $(e.relatedTarget).data('id');
        
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
            url    : '_Page/Anggota/FormInactive.php',
            data   : {id_anggota: id_anggota},
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
            url        : '_Page/Anggota/ProsesInactive.php',
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
        
        // Tangkap id_anggota
        var id_anggota = $(e.relatedTarget).data('id');
        
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
            url    : '_Page/Anggota/FormActive.php',
            data   : {id_anggota: id_anggota},
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
            url        : '_Page/Anggota/ProsesActive.php',
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
    // IMPORT
    //------------------------------------------------

    // Saat modal dibuka
    $('#ModalImport').on('shown.bs.modal', function(){
        $('#file_import').val('');
        $('#ButtonImport').prop('disabled', true);
        $('#ButtonReset').prop('disabled', true);
        $('#NotifikasiImport').html(`
            <tr>
                <td colspan="9" class="text-center">
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
                <td colspan="9" class="text-center">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    Membaca file excel...
                </td>
            </tr>
        `);

        let formData = new FormData(this);

        $.ajax({
            url         : '_Page/Anggota/ImportPreview.php',
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
                        <td colspan="9" class="text-center text-danger">
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
                <td colspan="9" class="text-center">
                    <small class="text text-grayish">
                        Belum Ada Data Import
                    </small>
                </td>
            </tr>
        `);

    });
});