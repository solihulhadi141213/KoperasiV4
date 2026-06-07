<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status" => "error",
            "html" => '<tr><td colspan="9" class="text-center text-danger"><small>Sesi akses sudah berakhir. Silakan login ulang.</small></td></tr>'
        ]);
        exit;
    }

    $page       = (int)($_POST['page'] ?? 1);
    $batas      = (int)($_POST['batas'] ?? 10);
    $OrderBy    = $_POST['OrderBy'] ?? 'id_barang';
    $ShortBy    = strtoupper($_POST['ShortBy'] ?? 'ASC');
    $KeywordBy  = $_POST['KeywordBy'] ?? '';
    $keyword    = trim($_POST['keyword'] ?? '');

    if ($page < 1) $page = 1;
    if ($batas < 1) $batas = 10;
    if (!in_array($ShortBy, ['ASC','DESC'])) $ShortBy = 'ASC';

    $orderMap = [
        'id_barang' => 'b.id_barang',
        'kode'      => 'b.kode',
        'nama'      => 'b.nama',
        'kategori'  => 'b.kategori',
        'satuan'    => 'b.satuan',
        'stok'      => 'b.stok',
        'status'    => 'b.status',
        'Batch'     => 'jumlah_batch',
        'Expired'   => 'jumlah_expired'
    ];

    $OrderColumn = $orderMap[$OrderBy] ?? 'b.id_barang';

    $where = [];
    $bindTypes = '';
    $bindValues = [];

    if ($keyword !== '') {

        switch ($KeywordBy) {

            case 'kode':
                $where[] = "b.kode LIKE ?";
                $bindTypes .= "s";
                $bindValues[] = "%{$keyword}%";
            break;

            case 'nama':
                $where[] = "b.nama LIKE ?";
                $bindTypes .= "s";
                $bindValues[] = "%{$keyword}%";
            break;

            case 'kategori':
                $where[] = "b.kategori LIKE ?";
                $bindTypes .= "s";
                $bindValues[] = "%{$keyword}%";
            break;

            case 'status':
                if ($keyword === '0' || $keyword === '1') {
                    $where[] = "b.status = ?";
                    $bindTypes .= "i";
                    $bindValues[] = (int)$keyword;
                }
            break;

            default:
                $where[] = "(b.kode LIKE ? OR b.nama LIKE ? OR b.kategori LIKE ?)";
                $bindTypes .= "sss";
                $bindValues[] = "%{$keyword}%";
                $bindValues[] = "%{$keyword}%";
                $bindValues[] = "%{$keyword}%";
            break;
        }
    }

    $whereSql = !empty($where) ? " WHERE ".implode(" AND ", $where) : "";

    $fromQuery = "
    FROM barang b
    LEFT JOIN (
        SELECT
            id_barang,
            COUNT(*) AS jumlah_batch,
            SUM(
                CASE
                    WHEN expired_date < CURDATE() THEN 1
                    ELSE 0
                END
            ) AS jumlah_expired
        FROM barang_batch
        WHERE status = 1
        GROUP BY id_barang
    ) bb ON b.id_barang = bb.id_barang
    $whereSql
    ";

    $sql_count = "SELECT COUNT(*) AS total $fromQuery";
    $stmt_count = $Conn->prepare($sql_count);

    if (!empty($bindValues)) {
        $stmt_count->bind_param($bindTypes, ...$bindValues);
    }

    $stmt_count->execute();
    $total_data = (int)$stmt_count->get_result()->fetch_assoc()['total'];
    $stmt_count->close();

    $total_page = ($total_data > 0) ? ceil($total_data / $batas) : 1;

    if ($page > $total_page) {
        $page = $total_page;
    }

    $posisi = ($page - 1) * $batas;

    $sql = "
    SELECT
        b.id_barang,
        b.kode,
        b.nama,
        b.kategori,
        b.satuan,
        b.stok,
        b.status,
        COALESCE(bb.jumlah_batch,0) AS jumlah_batch,
        COALESCE(bb.jumlah_expired,0) AS jumlah_expired
    $fromQuery
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

    $html = '';
    $no = $posisi + 1;

    if ($query->num_rows == 0) {

        $html .= '<tr><td colspan="9" class="text-center text-danger"><small>Tidak ada data yang ditampilkan.</small></td></tr>';

    } else {

        while ($data = $query->fetch_assoc()) {

            $id_barang  = (int)$data['id_barang'];
            $kode       = htmlspecialchars($data['kode'] ?? '', ENT_QUOTES, 'UTF-8');
            $nama       = htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8');
            $kategori   = htmlspecialchars($data['kategori'] ?? '', ENT_QUOTES, 'UTF-8');
            $satuan     = htmlspecialchars($data['satuan'] ?? '', ENT_QUOTES, 'UTF-8');

            $stok       = (float)$data['stok'];
            $status     = (int)$data['status'];

            $qty_batch = (int)$data['jumlah_batch'];
            $jumlah_expired = (int)$data['jumlah_expired'];

            $stok_label = rtrim(rtrim(number_format($stok,2,',','.'),'0'),',').' '.$satuan;

            $label_status = ($status == 1)
                ? '<span class="badge bg-success-subtle text-success">Active</span>'
                : '<span class="badge bg-danger-subtle text-danger">Inactive</span>';

            $expired_label = ($jumlah_expired > 0)
                ? '<span class="badge bg-danger-subtle text-danger">'.$jumlah_expired.' Expired</span>'
                : '<span class="badge bg-success-subtle text-success">0 Expired</span>';

            $html .= '
            <tr>
                <td class="text-center"><small class="text text-grayish">'.$no++.'</small></td>
                <td><small class="text text-grayish">'.$kode.'</small></td>
                <td>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_barang.'">
                        <small class="text-primary text-decoration-underline">'.$nama.'</small>
                    </a>
                </td>
                <td><small class="text text-grayish">'.$kategori.'</small></td>
                <td><small class="text text-grayish">'.$stok_label.'</small></td>
                <td><small class="text text-grayish">'.$qty_batch.' Item</small></td>
                <td>'.$expired_label.'</td>
                <td class="text-center">'.$label_status.'</td>
                <td class="text-center">
                    <button class="btn btn-md btn-secondary btn-floating show_detail_batch" data-id="'.$id_barang.'">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </td>
            </tr>';
        }
    }

    $stmt->close();

    echo json_encode([
        "status" => "success",
        "html" => $html,
        "page" => $page,
        "total_page" => $total_page,
        "total_data" => $total_data
    ]);
