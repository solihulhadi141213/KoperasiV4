<?php
    // Connection dan Session
    date_default_timezone_set('Asia/Jakarta');
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    // Validasi Sesi Akses
    if(empty($SessionIdAkses)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Login Sudah Berakhir, Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }
    // Ambil daftar kategori fitur
    $StmtKategori = $Conn->prepare("
        SELECT DISTINCT kategori 
        FROM akses_fitur 
        ORDER BY kategori ASC
    ");

    $StmtKategori->execute();
    $ResultKategori = $StmtKategori->get_result();

    if($ResultKategori->num_rows == 0){

        echo '
            <div class="alert alert-danger">
                Belum ada data fitur aplikasi.
            </div>
        ';

    }else{

        echo '<div class="accordion" id="accordionIzinAkses">';

        $no = 1;

        while($DataKategori = $ResultKategori->fetch_assoc()){

            $kategori   = $DataKategori['kategori'];
            $collapseId = 'collapseKategori'.$no;
            $headingId  = 'headingKategori'.$no;

            // Ambil jumlah fitur
            $StmtJumlah = $Conn->prepare("
                SELECT COUNT(*) as total 
                FROM akses_fitur 
                WHERE kategori = ?
            ");

            $StmtJumlah->bind_param("s", $kategori);
            $StmtJumlah->execute();

            $ResultJumlah = $StmtJumlah->get_result();
            $DataJumlah   = $ResultJumlah->fetch_assoc();

            $jumlah_fitur = $DataJumlah['total'];

            $StmtJumlah->close();

            echo '
                <div class="accordion-item">

                    <h2 class="accordion-header" id="'.$headingId.'">

                        <button 
                            class="accordion-button collapsed" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#'.$collapseId.'">

                            <i class="bi bi-folder2-open me-2 text-primary"></i>

                            <div class="w-100 d-flex justify-content-between align-items-center me-3">

                                <span>
                                    '.$kategori.'
                                </span>

                                <span class="badge bg-primary">
                                    '.$jumlah_fitur.' Fitur
                                </span>

                            </div>

                        </button>

                    </h2>

                    <div 
                        id="'.$collapseId.'" 
                        class="accordion-collapse collapse" 
                        data-bs-parent="#accordionIzinAkses">

                        <div class="accordion-body p-0">

                            <div class="list-group list-group-flush">
            ';

            // Ambil fitur berdasarkan kategori
            $StmtFitur = $Conn->prepare("
                SELECT 
                    id_akses_fitur,
                    kode,
                    nama,
                    keterangan
                FROM akses_fitur
                WHERE kategori = ?
                ORDER BY nama ASC
            ");

            $StmtFitur->bind_param("s", $kategori);
            $StmtFitur->execute();

            $ResultFitur = $StmtFitur->get_result();

            while($DataFitur = $ResultFitur->fetch_assoc()){

                $id_akses_fitur = $DataFitur['id_akses_fitur'];
                $kode           = $DataFitur['kode'];
                $nama           = $DataFitur['nama'];
                $keterangan     = $DataFitur['keterangan'];

                // Validasi izin akses
                $StmtIzin = $Conn->prepare("
                    SELECT ai.id_akses_ijin
                    FROM akses_ijin ai
                    JOIN akses_fitur af 
                        ON ai.id_akses_fitur = af.id_akses_fitur
                    WHERE ai.id_akses = ?
                    AND af.kode = ?
                    LIMIT 1
                ");

                $StmtIzin->bind_param("is", $SessionIdAkses, $kode);
                $StmtIzin->execute();

                $ResultIzin = $StmtIzin->get_result();

                $punya_izin = $ResultIzin->num_rows > 0;

                $StmtIzin->close();

                if($punya_izin){

                    $icon_status = '
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    ';

                    $badge_status = '
                        <span class="badge bg-success">
                            Diizinkan
                        </span>
                    ';

                }else{

                    $icon_status = '
                        <i class="bi bi-x-circle text-secondary fs-5"></i>
                    ';

                    $badge_status = '
                        <span class="badge bg-secondary">
                            Tidak Ada Akses
                        </span>
                    ';
                }

                echo '
                    <div class="list-group-item">

                        <div class="d-flex justify-content-between align-items-start">

                            <div class="me-3">

                                <div class="fw-bold">
                                    '.$nama.'
                                </div>

                                <small class="text-muted">
                                    '.$keterangan.'
                                </small>

                            </div>

                            <div class="text-end">

                                '.$icon_status.'

                            </div>

                        </div>

                    </div>
                ';
            }

            $StmtFitur->close();

            echo '
                            </div>

                        </div>

                    </div>

                </div>
            ';

            $no++;
        }

        echo '</div>';

        $StmtKategori->close();
    }
?>