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
    $OrderBy    = $_POST['OrderBy'] ?? 'id_barang';
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
        'id_barang',
        'kode',
        'nama',
        'kategori',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'status'
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
        'id_barang',
        'kode',
        'nama',
        'kategori',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'status'
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    $columnMap = [
        'id_barang'    => 'id_barang',
        'kode'         => 'kode',
        'nama'         => 'nama',
        'kategori'     => 'kategori',
        'satuan'       => 'satuan',
        'harga_beli'   => 'harga_beli',
        'harga_jual'   => 'harga_jual',
        'stok'         => 'stok',
        'stok_minimum' => 'stok_minimum',
        'status'       => 'status'
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
                    kategori LIKE ? OR
                    satuan LIKE ? OR
                    harga_beli LIKE ? OR
                    harga_jual LIKE ? OR
                    stok LIKE ? OR
                    stok_minimum LIKE ? OR
                    status LIKE ?
                )
            ";
            $bindTypes = "sssssssss";
            $bindValues[] = $keywordLike;
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
    $sql_count = "SELECT COUNT(*) AS total FROM barang $where";
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
            id_barang,
            kode,
            nama,
            kategori,
            satuan,
            harga_beli,
            harga_jual,
            stok,
            stok_minimum,
            status
        FROM barang
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
            $id_barang    = (int)$data['id_barang'];
            $kode         = htmlspecialchars($data['kode'] ?? '', ENT_QUOTES, 'UTF-8');
            $nama         = htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8');
            $kategori     = htmlspecialchars($data['kategori'] ?? '', ENT_QUOTES, 'UTF-8');
            $satuan       = htmlspecialchars($data['satuan'] ?? '', ENT_QUOTES, 'UTF-8');
            $harga_beli   = (float)($data['harga_beli'] ?? 0);
            $harga_jual   = (float)($data['harga_jual'] ?? 0);
            $stok         = (float)($data['stok'] ?? 0);
            $status       = (int)($data['status'] ?? 0);

            $harga_beli_rupiah = "Rp " . number_format($harga_beli, 0, ',', '.');
            $harga_jual_rupiah = "Rp " . number_format($harga_jual, 0, ',', '.');
            $stok_label        = rtrim(rtrim(number_format($stok, 2, ',', '.'), '0'), ',') . ' ' . $satuan;

            if ($status == 1) {
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
                            data-id="'.$id_barang.'">

                            <i class="bi bi-indent"></i> Inactive
                        </a>
                    </li>
                ';
            } else {
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
                            data-id="'.$id_barang.'">

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
                            data-bs-target="#ModalDetailKode"
                            data-id="'.$id_barang.'">
                            <small class="text text-info">
                                '.$kode.'
                            </small>
                        </a>
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
                        <small class="text text-grayish">'.$stok_label.'</small>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$harga_beli_rupiah.'</small>
                    </td>

                    <td>
                        <small class="text text-grayish">'.$harga_jual_rupiah.'</small>
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
                                    data-id="'.$id_barang.'">

                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEdit"
                                    data-id="'.$id_barang.'">

                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalHarga"
                                    data-id="'.$id_barang.'">

                                    <i class="bi bi-tag"></i> Multi Harga
                                </a>
                            </li>

                            '.$tombol_lanjutan.'

                            <li>
                                <a
                                    class="dropdown-item text-danger"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalHapus"
                                    data-id="'.$id_barang.'">

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
