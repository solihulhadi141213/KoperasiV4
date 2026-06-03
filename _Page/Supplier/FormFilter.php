<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci</label>';
    
    // Jika pencarian berdasarkan status
    if ($KeywordBy === "status") {
        echo '
            <select name="keyword" id="keyword" class="form-control">
            <option value="">Pilih</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
            </select>
        ';
    } else {
        
        if ($KeywordBy === "kategori_supplier") {
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';

            // Prepared Statement
            $stmt = $Conn->prepare("SELECT DISTINCT kategori_supplier FROM supplier ORDER BY kategori_supplier ASC");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                while ($data = $result->fetch_assoc()) {
                    $kategori_supplier = htmlspecialchars($data['kategori_supplier'], ENT_QUOTES, 'UTF-8');
                    echo '<option value="' . $kategori_supplier . '">' . $kategori_supplier . '</option>';
                }
                $stmt->close();
            }
            echo '</select>';
        } else {
            // Default input text
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>