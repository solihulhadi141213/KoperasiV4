<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci Pencarian</label>';
    
    // Jika pencarian berdasarkan organization_tag
    if ($KeywordBy === "organization_tag") {
        echo '<select name="keyword" id="keyword" class="form-control">';
        echo '  <option value="">Pilih</option>';

        // Prepared Statement
        $stmt = $Conn->prepare("SELECT DISTINCT organization_tag FROM anggota ORDER BY organization_tag ASC");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($data = $result->fetch_assoc()) {
                $organization_tag = htmlspecialchars($data['organization_tag'], ENT_QUOTES, 'UTF-8');
                echo '<option value="' . $organization_tag . '">' . $organization_tag . '</option>';
            }
            $stmt->close();
        }
        echo '</select>';
    } else {

        // Jika pencarian berdasarkan datetime_registered
        if ($KeywordBy === "datetime_registered") {
           echo '<input type="date" name="keyword" id="keyword" class="form-control">';
        } else {

           // Jika pencarian berdasarkan status
            if ($KeywordBy === "status") {
                echo '<select name="keyword" id="keyword" class="form-control">';
                echo '  <option value="">Pilih</option>';

                // Prepared Statement
                $stmt = $Conn->prepare("SELECT DISTINCT status FROM anggota ORDER BY status ASC");
                if ($stmt) {
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($data = $result->fetch_assoc()) {
                        $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
                        echo '<option value="' . $status . '">' . $status . '</option>';
                    }
                    $stmt->close();
                }
                echo '</select>';
            } else {

                // Default input text
                echo '<input type="text" name="keyword" id="keyword" class="form-control">';
            }
        }
    }
?>