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
    $OrderBy    = $_POST['OrderBy'] ?? 'id_anggota';
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
        'id_anggota',
        'nia',
        'nama',
        'kontak',
        'email',
        'organization_tag',
        'rank_tag',
        'status',
        'datetime_registered',
        'datetime_leave',
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'id_anggota';
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
        'id_anggota',
        'nia',
        'nama',
        'kontak',
        'email',
        'organization_tag',
        'rank_tag',
        'status',
        'datetime_registered',
        'datetime_leave',
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    // =========================================================
    // FILTER QUERY
    // =========================================================
    $where      = "";
    $bindTypes  = "";
    $bindValues = [];

    if (!empty($keyword)) {
        $keywordLike = "%" . $keyword . "%";
        if (!empty($keyword_by)) {
            $where        .= " WHERE $keyword_by LIKE ? ";
            $bindTypes    .= "s";
            $bindValues[]  = $keywordLike;

        } else {
            $where .= "
                WHERE (
                    nia LIKE ? OR 
                    nama LIKE ? OR
                    kontak LIKE ? OR
                    email LIKE ? OR
                    organization_tag LIKE ? OR
                    rank_tag LIKE ? OR
                    status LIKE ? OR
                    datetime_registered LIKE ?
                )
            ";
            $bindTypes .= "ssssssss";
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
        }
    }

    // =========================================================
    // TOTAL DATA
    // =========================================================
    $sql_count = "SELECT COUNT(*) AS total FROM anggota $where";
    $stmt_count = $Conn->prepare($sql_count);
    if (!$stmt_count) {
        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="7" class="text-center text-danger">
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
    $sql  = "SELECT * FROM anggota $where ORDER BY $OrderBy $ShortBy LIMIT ?, ?";
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
                    <td colspan="7" class="text-center text-danger">
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
                <td colspan="7" class="text-center text-danger">
                    <small>Tidak ada data yang ditampilkan.</small>
                </td>
            </tr>
        ';

    } else {

        while ($data = $query->fetch_assoc()) {

            $id_anggota          = (int)$data['id_anggota'];
            $nia                 = htmlspecialchars($data['nia']);
            $nama                = htmlspecialchars($data['nama']);
            $kontak              = htmlspecialchars($data['kontak']);
            $email               = htmlspecialchars($data['email']);
            $organization_tag    = htmlspecialchars($data['organization_tag']);
            $rank_tag            = htmlspecialchars($data['rank_tag']);
            $status              = htmlspecialchars($data['status']);
            $datetime_registered = date('d/m/Y H:i', strtotime($data['datetime_registered']));
            $datetime_leave      = date('d/m/Y H:i', strtotime($data['datetime_leave']));

            // Routing Status
            if($status=="Active"){
                $label_status = '
                    <span class="badge bg-success-subtle text-success">Active</span>
                ';

                $tombol_lanjutan = '
                    <li>
                        <a 
                            class="dropdown-item text-danger"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#ModalInactive"
                            data-id="'.$id_anggota.'">

                            <i class="bi bi-indent"></i> Inactive
                        </a>
                    </li>
                ';
            }else{
                $label_status = '
                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                ';
                $tombol_lanjutan = '
                    <li>
                        <a 
                            class="dropdown-item text-success"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#ModalActive"
                            data-id="'.$id_anggota.'">

                            <i class="bi bi-indent"></i> Active
                        </a>
                    </li>
                ';
            }

            $html .= '
                <tr>
                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>

                    <td>
                        <a href="javascript:void(0);" 
                            data-bs-toggle="modal" 
                            data-bs-target="#ModalDetail" 
                            data-id="'.$id_anggota.'">
                            <small class="text text-primary text-decoration-underline">
                                '.$nama.'
                            </small>
                        </a>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$nia.'
                        </small>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$organization_tag.'
                        </small>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$datetime_registered.'
                        </small>
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
                                    data-id="'.$id_anggota.'">

                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                            </li>

                            <li>
                                <a 
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEdit"
                                    data-id="'.$id_anggota.'">

                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>

                            '.$tombol_lanjutan.'
                            
                            <li>
                                <a 
                                    class="dropdown-item text-danger"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalHapus"
                                    data-id="'.$id_anggota.'">

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