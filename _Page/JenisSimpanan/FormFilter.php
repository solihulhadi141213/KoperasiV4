<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci Pencarian</label>';
    
    // Jika pencarian berdasarkan simpanan_kategori
    if ($KeywordBy === "simpanan_kategori") {
        echo '<select name="keyword" id="keyword" class="form-control">';
        echo '  <option value="">Pilih</option>';

        // Prepared Statement
        $stmt = $Conn->prepare("SELECT DISTINCT simpanan_kategori FROM simpanan_reference ORDER BY simpanan_kategori ASC");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($data = $result->fetch_assoc()) {
                $simpanan_kategori = htmlspecialchars($data['simpanan_kategori'], ENT_QUOTES, 'UTF-8');
                echo '<option value="' . $simpanan_kategori . '">' . $simpanan_kategori . '</option>';
            }
            $stmt->close();
        }
        echo '</select>';
    } else {

        // Jika pencarian berdasarkan status
        if ($KeywordBy === "status") {
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';

            // Prepared Statement
            $stmt = $Conn->prepare("SELECT DISTINCT status FROM simpanan_reference ORDER BY status ASC");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                while ($data = $result->fetch_assoc()) {
                    $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
                    if($status==1){
                        echo '<option value="' . $status . '">Active</option>';
                    }else{
                        echo '<option value="' . $status . '">Inactive</option>';
                    }
                    
                }
                $stmt->close();
            }
            echo '</select>';
        } else {
            if ($KeywordBy === "periode_pembayaran") {
                echo '<select name="keyword" id="keyword" class="form-control">';
                echo '  <option value="">Pilih</option>';

                // Prepared Statement
                $stmt = $Conn->prepare("SELECT DISTINCT periode_pembayaran FROM simpanan_reference ORDER BY periode_pembayaran ASC");
                if ($stmt) {
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($data = $result->fetch_assoc()) {
                        $periode_pembayaran = htmlspecialchars($data['periode_pembayaran'], ENT_QUOTES, 'UTF-8');
                        echo '<option value="' . $periode_pembayaran . '">' . $periode_pembayaran . '</option>';
                    }
                    $stmt->close();
                }
                echo '</select>';
            }else{
                // Default input text
                echo '<input type="text" name="keyword" id="keyword" class="form-control">';
            }
        }
    }
?>