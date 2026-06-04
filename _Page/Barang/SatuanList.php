<?php
    include "../../_Config/Connection.php";

    $page   = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = 20;
    $offset = ($page - 1) * $limit;
    $search = "";

    if (!empty($_GET['search'])) {
        $search = trim($_GET['search']);
    }

    $where = "";

    if (!empty($search)) {
        $search = mysqli_real_escape_string($Conn, $search);
        $where = " AND satuan LIKE '%$search%'";
    }

    $query = mysqli_query(
        $Conn,
        "
        SELECT DISTINCT satuan
        FROM barang
        WHERE satuan IS NOT NULL
        AND satuan != ''
        $where
        ORDER BY satuan ASC
        LIMIT $offset,$limit
        "
    );

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $data[] = [
            "value" => $row['satuan'],
            "text"  => $row['satuan']
        ];
    }

    $count_query = mysqli_query(
        $Conn,
        "
        SELECT COUNT(DISTINCT satuan) as total
        FROM barang
        WHERE satuan IS NOT NULL
        AND satuan != ''
        $where
        "
    );

    $count_data = mysqli_fetch_assoc($count_query);
    $total_pages = ceil($count_data['total'] / $limit);
    header('Content-Type: application/json');
    echo json_encode([
        "data"      => $data,
        "next_page" => ($page < $total_pages) ? ($page + 1) : null
    ]);