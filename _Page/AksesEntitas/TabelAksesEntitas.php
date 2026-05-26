<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        <small>Sesi akses sudah berakhir. Silakan login ulang.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    // =========================================================
    // PARAMETER
    // =========================================================
    $page       = $_POST['page'] ?? 1;
    $batas      = $_POST['batas'] ?? 10;
    $OrderBy    = $_POST['OrderBy'] ?? 'akses';
    $ShortBy    = $_POST['ShortBy'] ?? 'ASC';
    $keyword_by = $_POST['keyword_by'] ?? '';
    $keyword    = trim($_POST['keyword'] ?? '');

    // =========================================================
    // VALIDASI PAGE & LIMIT
    // =========================================================
    $page  = (int)$page;
    $batas = (int)$batas;

    if ($page <= 0) {
        $page = 1;
    }

    if ($batas <= 0) {
        $batas = 10;
    }

    $posisi = ($page - 1) * $batas;

    // =========================================================
    // VALIDASI ORDER BY
    // =========================================================
    $allowedOrder = [
        'akses',
        'keterangan'
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'akses';
    }

    // =========================================================
    // VALIDASI SORT
    // =========================================================
    $ShortBy = strtoupper($ShortBy);

    if (!in_array($ShortBy, ['ASC', 'DESC'])) {
        $ShortBy = 'ASC';
    }

    // =========================================================
    // VALIDASI FILTER
    // =========================================================
    $allowedKeywordBy = [
        'akses',
        'keterangan'
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    // =========================================================
    // FILTER QUERY
    // =========================================================
    $where = "";
    $bindTypes = "";
    $bindValues = [];

    if (!empty($keyword)) {

        $keywordLike = "%" . $keyword . "%";

        if (!empty($keyword_by)) {

            $where .= " WHERE ae.$keyword_by LIKE ? ";
            $bindTypes .= "s";
            $bindValues[] = $keywordLike;

        } else {

            $where .= "
                WHERE (
                    ae.akses LIKE ?
                    OR ae.keterangan LIKE ?
                )
            ";

            $bindTypes .= "ss";
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
        }
    }

    // =========================================================
    // TOTAL DATA
    // =========================================================
    $sql_count = "
        SELECT COUNT(*) AS total
        FROM akses_entitas ae
        $where
    ";

    $stmt_count = $Conn->prepare($sql_count);

    if (!empty($bindValues)) {
        $stmt_count->bind_param($bindTypes, ...$bindValues);
    }

    $stmt_count->execute();

    $result_count = $stmt_count->get_result();
    $data_count   = $result_count->fetch_assoc();

    $total_data = (int)$data_count['total'];

    $stmt_count->close();

    // =========================================================
    // TOTAL PAGE
    // =========================================================
    $total_page = ($total_data > 0) ? ceil($total_data / $batas) : 1;

    if ($page > $total_page) {
        $page = $total_page;
    }

    $posisi = ($page - 1) * $batas;

    // =========================================================
    // QUERY DATA
    // =========================================================
    $sql = "
        SELECT
            ae.uuid_akses_entitas,
            ae.akses,
            ae.keterangan,

            COUNT(DISTINCT ai.id_akses_ijin) AS jumlah_fitur,
            COUNT(DISTINCT a.id_akses) AS jumlah_pengguna

        FROM akses_entitas ae

        LEFT JOIN akses a
            ON ae.uuid_akses_entitas = a.uuid_akses_entitas

        LEFT JOIN akses_ijin ai
            ON a.id_akses = ai.id_akses

        $where

        GROUP BY ae.uuid_akses_entitas

        ORDER BY ae.$OrderBy $ShortBy

        LIMIT ?, ?
    ";

    $stmt = $Conn->prepare($sql);

    if (!$stmt) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        <small>Gagal mempersiapkan query data.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    // =========================================================
    // BIND PARAMETER
    // =========================================================
    $bindTypesData = $bindTypes . "ii";

    $bindValuesData = $bindValues;
    $bindValuesData[] = $posisi;
    $bindValuesData[] = $batas;

    $stmt->bind_param($bindTypesData, ...$bindValuesData);

    // =========================================================
    // EXECUTE
    // =========================================================
    if (!$stmt->execute()) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        <small>Terjadi kesalahan saat mengambil data.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    $query = $stmt->get_result();

    // =========================================================
    // BUILD HTML
    // =========================================================
    $html = '';
    $no   = 1 + $posisi;

    if ($query->num_rows == 0) {

        $html .= '
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <small>Tidak ada data yang ditampilkan.</small>
                </td>
            </tr>
        ';

    } else {

        while ($data = $query->fetch_assoc()) {

            $uuid_akses_entitas = htmlspecialchars($data['uuid_akses_entitas']);
            $akses              = htmlspecialchars($data['akses']);
            $keterangan = strip_tags($data['keterangan'] ?? '');
            $keterangan = htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8');
            if (mb_strlen($keterangan) > 30) {
                $keterangan = mb_substr($keterangan, 0, 30) . '...';
            }
            $jumlah_fitur       = (int)$data['jumlah_fitur'];
            $jumlah_pengguna    = (int)$data['jumlah_pengguna'];

            // Routing Tombol
            if(empty($jumlah_fitur)){
                $tombol_jumlah_fitur = '
                    <button class="btn btn-sm btn-outline-secondary">
                       0 Fitur
                    </button>
                ';
            }else{
                $tombol_jumlah_fitur = '
                    <button class="btn btn-sm btn-secondary">
                       '.$jumlah_fitur.' Fitur
                    </button>
                ';
            }

             if(empty($jumlah_pengguna)){
                $tombol_jumlah_pengguna = '
                    <button class="btn btn-sm btn-outline-secondary">
                       0 Pengguna
                    </button>
                ';
            }else{
                $tombol_jumlah_pengguna = '
                    <button class="btn btn-sm btn-secondary">
                       '.$jumlah_pengguna.' Pengguna
                    </button>
                ';
            }


            $html .= '
                <tr>

                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>

                    <td>
                        <a href="javascript:void(0);"  data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$uuid_akses_entitas.'">
                            <small class="text text-primary">'.$akses.'</small>
                        </a>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$keterangan.'</small>
                    </td>

                    <td>'.$tombol_jumlah_fitur.'</td>
                    <td>'.$tombol_jumlah_pengguna.'</td>

                    <td class="text-center">

                        <button 
                            class="btn btn-md btn-outline-secondary btn-floating"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalDetail"
                                    data-id="'.$uuid_akses_entitas.'">

                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEdit"
                                    data-id="'.$uuid_akses_entitas.'">

                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item text-danger"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalDelete"
                                    data-id="'.$uuid_akses_entitas.'">

                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </li>

                        </ul>

                    </td>

                </tr>
            ';
        }
    }

    $stmt->close();

    // =========================================================
    // RESPONSE
    // =========================================================
    echo json_encode([
        "status"      => "success",
        "html"        => $html,
        "page"        => $page,
        "total_page"  => $total_page,
        "total_data"  => $total_data
    ]);
?>