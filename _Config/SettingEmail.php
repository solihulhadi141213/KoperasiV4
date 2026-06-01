<?php
    // Default
    $email_gateway    = "";
    $password_gateway = "";
    $url_provider     = "";
    $port_gateway     = "";
    $nama_pengirim    = "";
    $url_service      = "";

    $id_setting_email_gateway = 1;

    $stmt = mysqli_prepare(
        $Conn,
        "SELECT * FROM setting_email_gateway
        WHERE id_setting_email_gateway = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_setting_email_gateway
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($result)){

        $email_gateway    = $row['email_gateway'];
        $password_gateway = $row['password_gateway'];
        $url_provider     = $row['url_provider'];
        $port_gateway     = $row['port_gateway'];
        $nama_pengirim    = $row['nama_pengirim'];
        $url_service      = $row['url_service'];
    }

    mysqli_stmt_close($stmt);
?>