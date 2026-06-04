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
        $where = " AND kategori LIKE '%$search%'";
    }

    $query = mysqli_query(
        $Conn,
        "
        SELECT DISTINCT kategori
        FROM barang
        WHERE kategori IS NOT NULL
        AND kategori != ''
        $where
        ORDER BY kategori ASC
        LIMIT $offset,$limit
        "
    );

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $data[] = [
            "value" => $row['kategori'],
            "text"  => $row['kategori']
        ];
    }

    $count_query = mysqli_query(
        $Conn,
        "
        SELECT COUNT(DISTINCT kategori) as total
        FROM barang
        WHERE kategori IS NOT NULL
        AND kategori != ''
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