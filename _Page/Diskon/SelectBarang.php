<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";

//-----------------------------------------------------
// PARAMETER
//-----------------------------------------------------
$page    = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$keyword = trim($_POST['keyword'] ?? '');

if($page < 1){
    $page = 1;
}

//-----------------------------------------------------
// PAGING
//-----------------------------------------------------
$limit  = 10;
$offset = ($page - 1) * $limit;

//-----------------------------------------------------
// FILTER
//-----------------------------------------------------
$where  = "";
$params = [];
$types  = "";

if(!empty($keyword)){

    $where = "
        WHERE
            nama LIKE ?
            OR kode LIKE ?
    ";

    $search = "%".$keyword."%";

    $params[] = $search;
    $params[] = $search;

    $types .= "ss";
}

//-----------------------------------------------------
// QUERY
//-----------------------------------------------------
$sql = "
    SELECT
        id_barang,
        kode,
        nama
    FROM barang
    $where
    ORDER BY nama ASC
    LIMIT ?, ?
";

$stmt = $Conn->prepare($sql);

if(!$stmt){

    echo json_encode([
        "status"   => "error",
        "message"  => $Conn->error,
        "data"     => [],
        "has_more" => false
    ]);
    exit;
}

$types .= "ii";

$params[] = $offset;
$params[] = $limit;

$stmt->bind_param($types, ...$params);

if(!$stmt->execute()){

    echo json_encode([
        "status"   => "error",
        "message"  => $stmt->error,
        "data"     => [],
        "has_more" => false
    ]);
    exit;
}

$result = $stmt->get_result();

$data = [];

while($row = $result->fetch_assoc()){

    $data[] = [
        "id_barang" => $row['id_barang'],
        "kode"      => $row['kode'],
        "nama"      => $row['nama'],
        "label"     => $row['nama'].' ('.$row['kode'].')'
    ];
}

$stmt->close();

//-----------------------------------------------------
// RESPONSE
//-----------------------------------------------------
echo json_encode([
    "status"    => "success",
    "page"      => $page,
    "data"      => $data,
    "has_more"  => (count($data) >= $limit)
]);
exit;
?>