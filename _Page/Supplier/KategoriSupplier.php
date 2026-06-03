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
        $where = " AND kategori_supplier LIKE '%$search%'";
    }

    $query = mysqli_query(
        $Conn,
        "
        SELECT DISTINCT kategori_supplier
        FROM supplier
        WHERE kategori_supplier IS NOT NULL
        AND kategori_supplier != ''
        $where
        ORDER BY kategori_supplier ASC
        LIMIT $offset,$limit
        "
    );

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $data[] = [
            "value" => $row['kategori_supplier'],
            "text"  => $row['kategori_supplier']
        ];
    }

    $count_query = mysqli_query(
        $Conn,
        "
        SELECT COUNT(DISTINCT kategori_supplier) as total
        FROM supplier
        WHERE kategori_supplier IS NOT NULL
        AND kategori_supplier != ''
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