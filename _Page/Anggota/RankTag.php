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

if ($search !== "") {

    $search = mysqli_real_escape_string($Conn, $search);

    $where = " AND CAST(rank_tag AS CHAR) LIKE '%$search%'";
}

$query = mysqli_query(
    $Conn,
    "
    SELECT DISTINCT rank_tag
    FROM anggota
    WHERE rank_tag IS NOT NULL
    $where
    ORDER BY rank_tag ASC
    LIMIT $offset,$limit
    "
);

$data = [];

while ($row = mysqli_fetch_assoc($query)) {

    $data[] = [
        'value' => $row['rank_tag'],
        'text'  => $row['rank_tag']
    ];
}

$count = mysqli_fetch_assoc(
    mysqli_query(
        $Conn,
        "
        SELECT COUNT(DISTINCT rank_tag) as total
        FROM anggota
        WHERE rank_tag IS NOT NULL
        $where
        "
    )
);

$total_pages = ceil($count['total'] / $limit);

header('Content-Type: application/json');

echo json_encode([
    'data'      => $data,
    'next_page' => ($page < $total_pages)
        ? ($page + 1)
        : null
]);