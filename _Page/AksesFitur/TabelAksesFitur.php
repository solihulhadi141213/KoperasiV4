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
                    <td colspan="7" class="text-center text-danger">
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
    $OrderBy    = $_POST['OrderBy'] ?? 'id_akses_fitur';
    $ShortBy    = $_POST['ShortBy'] ?? 'DESC';
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
        'id_akses_fitur',
        'kategori',
        'nama',
        'kode',
        'keterangan'
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'id_akses_fitur';
    }

    // =========================================================
    // VALIDASI SORTING
    // =========================================================
    $ShortBy = strtoupper($ShortBy);

    if (!in_array($ShortBy, ['ASC', 'DESC'])) {
        $ShortBy = 'DESC';
    }

    // =========================================================
    // VALIDASI FILTER COLUMN
    // =========================================================
    $allowedKeywordBy = [
        'kategori',
        'nama',
        'kode',
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

            $where .= " WHERE af.$keyword_by LIKE ? ";
            $bindTypes .= "s";
            $bindValues[] = $keywordLike;

        } else {

            $where .= "
                WHERE (
                    af.kategori   LIKE ? OR
                    af.nama       LIKE ? OR
                    af.kode       LIKE ? OR
                    af.keterangan LIKE ?
                )
            ";

            $bindTypes .= "ssss";

            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
        }
    }

    // =========================================================
    // COUNT TOTAL DATA
    // =========================================================
    $sql_count = "
        SELECT COUNT(*) AS total
        FROM akses_fitur af
        $where
    ";

    $stmt_count = $Conn->prepare($sql_count);

    if (!$stmt_count) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <small>Gagal mempersiapkan query total data.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    if (!empty($bindValues)) {
        $stmt_count->bind_param($bindTypes, ...$bindValues);
    }

    $stmt_count->execute();

    $result_count = $stmt_count->get_result();
    $data_count   = $result_count->fetch_assoc();

    $total_data = (int)($data_count['total'] ?? 0);

    $stmt_count->close();

    // =========================================================
    // TOTAL PAGE
    // =========================================================
    $total_page = ($total_data > 0) ? ceil($total_data / $batas) : 1;

    // =========================================================
    // VALIDASI PAGE TIDAK LEBIH BESAR
    // =========================================================
    if ($page > $total_page) {
        $page = $total_page;
    }

    $posisi = ($page - 1) * $batas;

    // =========================================================
    // QUERY DATA
    // =========================================================
    $sql = "
        SELECT 
            af.*,

            COUNT(DISTINCT ar.uuid_akses_entitas) AS jumlah_entitas,

            COUNT(DISTINCT ai.id_akses) AS jumlah_pengguna

        FROM akses_fitur af

        LEFT JOIN akses_referensi ar 
            ON af.id_akses_fitur = ar.id_akses_fitur

        LEFT JOIN akses_ijin ai
            ON af.id_akses_fitur = ai.id_akses_fitur

        $where

        GROUP BY af.id_akses_fitur

        ORDER BY af.$OrderBy $ShortBy

        LIMIT ?, ?
    ";

    $stmt = $Conn->prepare($sql);

    if (!$stmt) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="7" class="text-center text-danger">
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
    // EXECUTE QUERY
    // =========================================================
    if (!$stmt->execute()) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <small>Terjadi kesalahan saat mengambil data fitur.</small>
                    </td>
                </tr>
            '
        ]);

        $stmt->close();
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
                <td colspan="7" class="text-center text-danger">
                    <small>Tidak ada data fitur yang ditampilkan.</small>
                </td>
            </tr>
        ';

    } else {

        while ($data = $query->fetch_assoc()) {

            // =================================================
            // ESCAPE OUTPUT
            // =================================================
            $id_akses_fitur = (int)$data['id_akses_fitur'];

            $nama       = htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8');
            $kategori   = htmlspecialchars($data['kategori'] ?? '', ENT_QUOTES, 'UTF-8');
            $kode       = htmlspecialchars($data['kode'] ?? '', ENT_QUOTES, 'UTF-8');

            $JumlahEntitas  = (int)($data['jumlah_entitas'] ?? 0);
            $JumlahPengguna = (int)($data['jumlah_pengguna'] ?? 0);

            // =================================================
            // LABEL ENTITAS
            // =================================================
            if ($JumlahEntitas > 0) {

                $labelEntitas = '
                    <button class="btn btn-sm btn-secondary">
                        <small>' . $JumlahEntitas . ' Entitas</small>
                    </button>
                ';

            } else {

                $labelEntitas = '
                    <button class="btn btn-sm btn-outline-secondary">
                        <small>0 Entitas</small>
                    </button>
                ';
            }

            // =================================================
            // LABEL USER
            // =================================================
            if ($JumlahPengguna > 0) {

                $labelUser = '
                    <button class="btn btn-sm btn-secondary">
                        <small>' . $JumlahPengguna . ' User</small>
                    </button>
                ';

            } else {

                $labelUser = '
                    <button class="btn btn-sm btn-outline-secondary">
                        <small>0 User</small>
                    </button>
                ';
            }

            // =================================================
            // HTML ROW
            // =================================================
            $html .= '
            <tr>
                <td>
                    <small>' . $no++ . '</small>
                </td>

                <td>
                    <a href="javascript:void(0);" 
                        class="text-primary text-decoration-none"
                        data-bs-toggle="modal"
                        data-bs-target="#ModalDetailFitur"
                        data-id="' . $id_akses_fitur . '">

                        <small>' . $nama . '</small>
                    </a>
                </td>

                <td>
                    <small>' . $kategori . '</small>
                </td>

                <td>
                    <span class="p-1 bg-secondary-subtle rounded-2">
                        <small class="text-muted">' . $kode . '</small>
                    </span>
                </td>

                <td>
                    ' . $labelEntitas . '
                </td>

                <td>
                    ' . $labelUser . '
                </td>

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
                                data-bs-target="#ModalDetailFitur"
                                data-id="' . $id_akses_fitur . '">

                                <i class="bi bi-info-circle"></i> Detail Fitur
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                                href="javascript:void(0);"
                                data-bs-toggle="modal"
                                data-bs-target="#ModalEditFitur"
                                data-id="' . $id_akses_fitur . '">

                                <i class="bi bi-pencil"></i> Edit Fitur
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item text-danger"
                                href="javascript:void(0);"
                                data-bs-toggle="modal"
                                data-bs-target="#ModalHapusFitur"
                                data-id="' . $id_akses_fitur . '">

                                <i class="bi bi-trash"></i> Hapus Fitur
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
    // RESPONSE JSON
    // =========================================================
    echo json_encode([
        "status"      => "success",
        "html"        => $html,
        "page"        => $page,
        "total_page"  => $total_page,
        "total_data"  => $total_data
    ]);
?>