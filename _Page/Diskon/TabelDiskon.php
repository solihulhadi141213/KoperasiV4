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
                    <td colspan="9" class="text-center text-danger">
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
    $OrderBy    = $_POST['OrderBy'] ?? 'id_barang_diskon';
    $ShortBy    = $_POST['ShortBy'] ?? 'ASC';
    $keyword_by = $_POST['KeywordBy'] ?? '';
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

    // =========================================================
    // VALIDASI ORDER BY
    // =========================================================
    $allowedOrder = [
        'id_barang_diskon',
        'id_barang',
        'kode',
        'nama',
        'kategori',
        'diskon',
        'datetime_start',
        'datetime_end'
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'id_barang';
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
        'kode',
        'nama',
        'kategori'
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    $columnMap = [
        'id_barang_diskon' => 'bd.id_barang_diskon',
        'id_barang'        => 'bd.id_barang',
        'kode'             => 'b.kode',
        'nama'             => 'b.nama',
        'kategori'         => 'b.kategori',
        'diskon'           => 'bd.diskon',
        'datetime_start'   => 'bd.datetime_start',
        'datetime_end'     => 'bd.datetime_end'
    ];
    $OrderColumn = $columnMap[$OrderBy];

    // =========================================================
    // FILTER QUERY
    // =========================================================
    $where      = "";
    $bindTypes  = "";
    $bindValues = [];

    if ($keyword !== '') {
        $keywordLike = "%" . $keyword . "%";

        if (!empty($keyword_by)) {
            $FilterColumn = $columnMap[$keyword_by];
            $where = " WHERE $FilterColumn LIKE ? ";
            $bindTypes = "s";
            $bindValues[] = $keywordLike;
        } else {
            $where = "
                WHERE (
                    kode LIKE ? OR
                    nama LIKE ? OR
                    kategori LIKE ?
                )
            ";
            $bindTypes = "sss";
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
        }
    }

    // =========================================================
    // TOTAL DATA
    // =========================================================
    $sql_count = "
        SELECT COUNT(*) AS total

        FROM barang_diskon bd

        INNER JOIN barang b
            ON bd.id_barang = b.id_barang

        $where
    ";
    $stmt_count = $Conn->prepare($sql_count);
    if (!$stmt_count) {
        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        <small>Gagal mempersiapkan query count.</small>
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
    $total_data   = (int)$data_count['total'];
    $stmt_count->close();

    // =========================================================
    // TOTAL PAGE & POSISI
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
            bd.id_barang_diskon,
            bd.id_barang,
            bd.diskon,
            bd.datetime_start,
            bd.datetime_end,

            b.kode,
            b.nama,
            b.kategori

        FROM barang_diskon bd

        INNER JOIN barang b
            ON bd.id_barang = b.id_barang

        $where

        ORDER BY $OrderColumn $ShortBy
        LIMIT ?, ?
    ";

    $stmt = $Conn->prepare($sql);
    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="9" class="text-center text-danger">
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
    $bindTypesData    = $bindTypes . "ii";
    $bindValuesData   = $bindValues;
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
                    <td colspan="9" class="text-center text-danger">
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
                <td colspan="9" class="text-center text-danger">
                    <small>Tidak ada data yang ditampilkan.</small>
                </td>
            </tr>
        ';
    } else {
        while ($data = $query->fetch_assoc()) {
            $id_barang_diskon = $data['id_barang_diskon'];
            $id_barang        = $data['id_barang'];
            $kode             = htmlspecialchars($data['kode']);
            $nama             = htmlspecialchars($data['nama']);
            $kategori         = htmlspecialchars($data['kategori']);
            $diskon           = (float)$data['diskon'];
            $datetime_start   = $data['datetime_start'];
            $datetime_end     = $data['datetime_end'];
            $today = date('Y-m-d');

            // Status Diskon
            if ($today < $datetime_start) {

                $label_status = '
                    <span class="badge bg-warning-subtle text-warning">
                        Menunggu
                    </span>
                ';

            } elseif ($today > $datetime_end) {

                $label_status = '
                    <span class="badge bg-danger-subtle text-danger">
                        Berakhir
                    </span>
                ';

            } else {

                $label_status = '
                    <span class="badge bg-success-subtle text-success">
                        Aktif
                    </span>
                ';
            }

            // Format Diskon
            $diskon_label = number_format($diskon, 0, ',', '.') . '%';

            // Tampilkan Data
            $html .= '
                <tr>
                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>
                    <td>
                        <small class="text text-grayish">'.$kode.'</small>
                    </td>
                    <td>
                        <a href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#ModalDetail"
                            data-id="'.$id_barang.'">
                            <small class="text text-primary text-decoration-underline">
                                '.$nama.'
                            </small>
                        </a>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$kategori.'</small>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$datetime_start.'</small>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$datetime_end.'</small>
                    </td>

                    <td class="text-center">
                        <small class="text text-grayish">'.$diskon_label.'</small>
                    </td>

                    <td class="text-center">
                        '.$label_status.'
                    </td>

                    <td class="text-center">
                        <button
                            class="btn btn-md btn-outline-secondary btn-floating"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalDetail"
                                    data-id="'.$id_barang_diskon.'">

                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEdit"
                                    data-id="'.$id_barang_diskon.'">

                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item text-danger"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalHapus"
                                    data-id="'.$id_barang_diskon.'">

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
