<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci Pencarian</label>';
    
    // Jika pencarian berdasarkan status
    if ($KeywordBy === "status") {
        echo '
            <select name="keyword" class="form-control">
                <option value="">Semua</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        ';
    } else {
        if ($KeywordBy === "denda_metode") {
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';

            // Prepared Statement
            $stmt = $Conn->prepare("SELECT DISTINCT denda_metode FROM pinjaman_jenis ORDER BY denda_metode ASC");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                while ($data = $result->fetch_assoc()) {
                    $denda_metode = htmlspecialchars($data['denda_metode'], ENT_QUOTES, 'UTF-8');
                    echo '<option value="' . $denda_metode . '">' . $denda_metode . '</option>';
                }
                $stmt->close();
            }
            echo '</select>';
        }else{
            // Default input text
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>