<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";
    include "../../_Config/GlobalFunction.php";

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
    // VALIDASI ID BARANG
    // =========================================================
    if (empty($_POST['id_barang'])) {
        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <small>ID Barang tidak ditemukan.</small>
                    </td>
                </tr>
            '
        ]);
        exit;
    }

    $id_barang = (int)$_POST['id_barang'];

    $satuan_barang = GetDetailData($Conn, 'barang','id_barang', $id_barang, 'satuan');

    // =========================================================
    // PARAMETER
    // =========================================================
    $page       = $_POST['page'] ?? 1;
    $batas      = $_POST['batas'] ?? 10;
    $OrderBy    = $_POST['OrderBy'] ?? 'expired_date';
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
    // VALIDASI ORDER
    // =========================================================
    $allowedOrder = [
        'id_barang_batch',
        'id_barang',
        'no_batch',
        'qty_batch',
        'expired_date',
        'reminder_date',
        'status'
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'expired_date';
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
        'id_barang_batch',
        'id_barang',
        'no_batch',
        'qty_batch',
        'expired_date',
        'reminder_date',
        'status'
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    $columnMap = [
        'id_barang_batch' => 'id_barang_batch',
        'id_barang'       => 'id_barang',
        'no_batch'        => 'no_batch',
        'qty_batch'       => 'qty_batch',
        'expired_date'    => 'expired_date',
        'reminder_date'   => 'reminder_date',
        'status'          => 'status'
    ];

    $OrderColumn = $columnMap[$OrderBy];

    // =========================================================
    // FILTER DASAR
    // =========================================================
    $where      = " WHERE id_barang = ? ";
    $bindTypes  = "i";
    $bindValues = [$id_barang];

    // =========================================================
    // FILTER KEYWORD
    // =========================================================
    if ($keyword !== '') {

        if (!empty($keyword_by)) {

            $FilterColumn = $columnMap[$keyword_by];

            // Status menggunakan =
            if ($keyword_by == 'status') {

                $where .= " AND $FilterColumn = ? ";

                $bindTypes .= "i";
                $bindValues[] = (int)$keyword;

            } else {

                $keywordLike = "%" . $keyword . "%";

                $where .= " AND $FilterColumn LIKE ? ";

                $bindTypes .= "s";
                $bindValues[] = $keywordLike;
            }

        } else {

            $keywordLike = "%" . $keyword . "%";

            $where .= "
                AND (
                    no_batch LIKE ?
                    OR expired_date LIKE ?
                    OR reminder_date LIKE ?
                )
            ";

            $bindTypes .= "sss";

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
        FROM barang_batch
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
    // PAGINATION
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
        SELECT *
        FROM barang_batch
        $where
        ORDER BY $OrderColumn $ShortBy
        LIMIT ?, ?
    ";

    $stmt = $Conn->prepare($sql);

    $bindTypesData = $bindTypes . "ii";

    $bindValuesData = $bindValues;
    $bindValuesData[] = $posisi;
    $bindValuesData[] = $batas;

    $stmt->bind_param($bindTypesData, ...$bindValuesData);

    $stmt->execute();

    $query = $stmt->get_result();

    // =========================================================
    // BUILD HTML
    // =========================================================
    $html = '';
    $no   = $posisi + 1;

    if ($query->num_rows == 0) {

        $html .= '
            <tr>
                <td colspan="7" class="text-center text-danger">
                    <small>Tidak ada data batch yang ditemukan.</small>
                </td>
            </tr>
        ';

    } else {

        while ($data = $query->fetch_assoc()) {

            $id_barang_batch = (int)$data['id_barang_batch'];
            $no_batch        = htmlspecialchars($data['no_batch']);
            $qty_batch       = (float)$data['qty_batch'];
            $expired_date    = date('d/m/Y', strtotime($data['expired_date']));
            $reminder_date   = date('d/m/Y', strtotime($data['reminder_date']));
            $status          = (int)$data['status'];

            $label_status = ($status == 1)
                ? '<span class="badge bg-success-subtle text-success">Tersedia</span>'
                : '<span class="badge bg-danger-subtle text-danger">Tidak Tersedia</span>';

            $html .= '
                <tr>
                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>

                    <td>
                        <small class="text-grayish">
                            '.$no_batch.'
                        </small>
                    </td>

                    <td class="text-center">
                        <small class="text text-grayish">'.$qty_batch.' '.$satuan_barang.'</small>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$expired_date.'</small>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$reminder_date.'</small>
                    </td>

                    <td class="text-center">
                        '.$label_status.'
                    </td>

                    <td class="text-center">

                        <button class="btn btn-md btn-outline-secondary btn-floating" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="javascript:void(0);"  class="dropdown-item print_batch" data-id_barang="'.$id_barang.'" data-id="'.$id_barang_batch.'">
                                    <i class="bi bi-printer"></i> Cetak
                                </a>
                                <a href="javascript:void(0);"  class="dropdown-item modal_edit_batch" data-id_barang="'.$id_barang.'" data-id="'.$id_barang_batch.'">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="javascript:void(0);"  class="dropdown-item text-danger modal_hapus_batch" data-id_barang="'.$id_barang.'" data-id="'.$id_barang_batch.'">
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