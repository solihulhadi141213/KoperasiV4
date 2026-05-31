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
        $where = " AND organization_tag LIKE '%$search%'";
    }

    $query = mysqli_query(
        $Conn,
        "
        SELECT DISTINCT organization_tag
        FROM anggota
        WHERE organization_tag IS NOT NULL
        AND organization_tag != ''
        $where
        ORDER BY organization_tag ASC
        LIMIT $offset,$limit
        "
    );

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $data[] = [
            "value" => $row['organization_tag'],
            "text"  => $row['organization_tag']
        ];
    }

    $count_query = mysqli_query(
        $Conn,
        "
        SELECT COUNT(DISTINCT organization_tag) as total
        FROM anggota
        WHERE organization_tag IS NOT NULL
        AND organization_tag != ''
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