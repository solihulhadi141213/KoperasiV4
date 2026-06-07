<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci Pencarian</label>';
    
    // Jika pencarian berdasarkan kategori
    if ($KeywordBy === "expired_date") {
        echo '<input type="date" name="keyword" id="KeywordBatch" class="form-control">';
    }

    if ($KeywordBy === "reminder_date") {
        echo '<input type="date" name="keyword" id="KeywordBatch" class="form-control">';
    }
    if ($KeywordBy === "status") {

        // Jika pencarian berdasarkan status
        if ($KeywordBy === "status") {
            echo '
                <select name="keyword" id="keyword" class="form-control">
                    <option value="">Pilih</option>
                    <option value="1">Tersedia</option>
                    <option value="0">Tidak Tersedia</option>
                </select>
            ';
        } else {

            // Default input text
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>