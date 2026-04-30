<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci Pencarian</label>';

    // Jika pencarian berdasarkan kategori
    if ($KeywordBy === "kategori") {

        echo '<select name="keyword" id="keyword" class="form-control">';
        echo '  <option value="">Pilih</option>';

        // Prepared Statement
        $stmt = $Conn->prepare("
            SELECT DISTINCT kategori 
            FROM akses_fitur 
            WHERE kategori IS NOT NULL 
            AND kategori != ''
            ORDER BY kategori ASC
        ");

        if ($stmt) {

            $stmt->execute();

            $result = $stmt->get_result();

            while ($data = $result->fetch_assoc()) {

                $kategori = htmlspecialchars($data['kategori'], ENT_QUOTES, 'UTF-8');

                echo '<option value="' . $kategori . '">' . $kategori . '</option>';
            }

            $stmt->close();
        }

        echo '</select>';

    } else {

        // Default input text
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }
?>