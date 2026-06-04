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
                    <td colspan="5" class="text-center text-danger">
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
    $OrderBy    = $_POST['OrderBy'] ?? 'id_barang_kategori_harga';
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

    $posisi = ($page - 1) * $batas;

    // =========================================================
    // VALIDASI ORDER BY
    // =========================================================
    $allowedOrder = [
        'id_barang_kategori_harga',
        'kategori_harga',
        'keterangan',
        'Record'
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'id_barang_kategori_harga';
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
       'id_barang_kategori_harga',
        'kategori_harga',
        'keterangan'
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    // Mapping order dan filter agar aman saat query memakai alias tabel.
    $orderColumnMap = [
        'id_barang_kategori_harga' => 'bkh.id_barang_kategori_harga',
        'kategori_harga'           => 'bkh.kategori_harga',
        'keterangan'               => 'bkh.keterangan',
        'Record'                   => 'Record'
    ];

    $filterColumnMap = [
        'id_barang_kategori_harga' => 'bkh.id_barang_kategori_harga',
        'kategori_harga'           => 'bkh.kategori_harga',
        'keterangan'               => 'bkh.keterangan'
    ];

    $OrderColumn = $orderColumnMap[$OrderBy];

    // =========================================================
    // FILTER QUERY
    // =========================================================
    $where      = "";
    $bindTypes  = "";
    $bindValues = [];

    if ($keyword !== '') {

        $keywordLike = "%" . $keyword . "%";

        if (!empty($keyword_by)) {

            $FilterColumn = $filterColumnMap[$keyword_by];
            $where = " WHERE $FilterColumn LIKE ? ";
            $bindTypes = "s";
            $bindValues[] = $keywordLike;

        } else {

            $where = "
                WHERE (
                    bkh.kategori_harga LIKE ? OR
                    bkh.keterangan LIKE ?
                )
            ";

            $bindTypes = "ss";
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
        }
    }

    // =========================================================
    // TOTAL DATA
    // =========================================================
    $sql_count = "SELECT COUNT(*) AS total FROM barang_kategori_harga bkh $where";
    $stmt_count = $Conn->prepare($sql_count);
    if (!$stmt_count) {
        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="5" class="text-center text-danger">
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
    $sql  = "
        SELECT
            bkh.id_barang_kategori_harga,
            bkh.kategori_harga,
            bkh.keterangan,
            COUNT(bh.id_barang_harga) AS Record
        FROM barang_kategori_harga bkh
        LEFT JOIN barang_harga bh
            ON bh.id_barang_kategori_harga = bkh.id_barang_kategori_harga
        $where
        GROUP BY
            bkh.id_barang_kategori_harga,
            bkh.kategori_harga,
            bkh.keterangan
        ORDER BY $OrderColumn $ShortBy
        LIMIT ?, ?
    ";
    $stmt = $Conn->prepare($sql);
    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan = "7" class = "text-center text-danger">
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
                    <td colspan="5" class="text-center text-danger">
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
                <td colspan="5" class="text-center text-danger">
                    <small>Tidak ada data yang ditampilkan.</small>
                </td>
            </tr>
        ';

    } else {

        while ($data = $query->fetch_assoc()) {
            $id_barang_kategori_harga = (int)$data['id_barang_kategori_harga'];
            $kategori_harga           = htmlspecialchars($data['kategori_harga'] ?? '', ENT_QUOTES, 'UTF-8');
            $keterangan               = htmlspecialchars($data['keterangan'] ?? '', ENT_QUOTES, 'UTF-8');
            $Record                   = (int)$data['Record'];

            $html .= '
                <tr>
                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>

                    <td>
                        <a href="javascript:void(0);" 
                            data-bs-toggle="modal" 
                            data-bs-target="#ModalDetail" 
                            data-id="'.$id_barang_kategori_harga.'">
                            <small class="text text-primary text-decoration-underline">
                                '.$kategori_harga.'
                            </small>
                        </a>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$keterangan.'
                        </small>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$Record.' Record
                        </small>
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
                                    data-id="'.$id_barang_kategori_harga.'">

                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                            </li>
                            <li>
                                <a 
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEdit"
                                    data-id="'.$id_barang_kategori_harga.'">

                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                            <li>
                                <a 
                                    class="dropdown-item text-danger"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalHapus"
                                    data-id="'.$id_barang_kategori_harga.'">

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
