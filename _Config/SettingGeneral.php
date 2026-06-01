<?php
    // Nilai default
    $id_setting_general = 0;
    $title_page         = "";
    $kata_kunci         = "";
    $deskripsi          = "";
    $alamat_bisnis      = "";
    $email_bisnis       = "";
    $telepon_bisnis     = "";
    $favicon            = "";
    $logo               = "";
    $base_url           = "";
    $AuthorAplikasi     = "";

    // Query
    $sql = "SELECT * FROM setting_general LIMIT 1";
    $stmt = $Conn->prepare($sql);

    if ($stmt) {
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $DataSettingGeneral = $result->fetch_assoc();

            $id_setting_general = $DataSettingGeneral['id_setting_general'] ?? 0;
            $title_page         = $DataSettingGeneral['title_page'] ?? "";
            $kata_kunci         = $DataSettingGeneral['kata_kunci'] ?? "";
            $deskripsi          = $DataSettingGeneral['deskripsi'] ?? "";
            $alamat_bisnis      = $DataSettingGeneral['alamat_bisnis'] ?? "";
            $email_bisnis       = $DataSettingGeneral['email_bisnis'] ?? "";
            $telepon_bisnis     = $DataSettingGeneral['telepon_bisnis'] ?? "";
            $favicon            = $DataSettingGeneral['favicon'] ?? "";
            $logo               = $DataSettingGeneral['logo'] ?? "";
            $base_url           = $DataSettingGeneral['base_url'] ?? "";
            $AuthorAplikasi     = $DataSettingGeneral['author'] ?? "";
        }

        $stmt->close();
    }
?>