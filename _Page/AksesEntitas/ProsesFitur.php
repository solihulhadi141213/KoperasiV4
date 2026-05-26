<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION, FUNCTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // DEFAULT RESPONSE
    // =========================================================
    $response = [
        'status'  => 'error',
        'message' => 'Tidak ada proses yang berjalan.'
    ];

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {

        $response = [
            'status'  => 'error',
            'message' => 'Sesi akses sudah berakhir! Silahkan login ulang.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // VALIDASI UUID
    // =========================================================
    if (empty($_POST['uuid_akses_entitas'])) {

        $response = [
            'status'  => 'error',
            'message' => 'ID Level/Entitas akses tidak boleh kosong.'
        ];

        echo json_encode($response);
        exit;
    }

    // =========================================================
    // SANITASI INPUT
    // =========================================================
    $uuid_akses_entitas = validateAndSanitizeInput(
        $_POST['uuid_akses_entitas']
    );

    // =========================================================
    // VALIDASI DATA ENTITAS
    // =========================================================
    $stmt = $Conn->prepare("
        SELECT 
            uuid_akses_entitas,
            akses
        FROM akses_entitas
        WHERE uuid_akses_entitas = ?
        LIMIT 1
    ");

    if (!$stmt) {

        $response = [
            'status'  => 'error',
            'message' => 'Gagal mempersiapkan validasi data.'
        ];

        echo json_encode($response);
        exit;
    }

    $stmt->bind_param(
        "s",
        $uuid_akses_entitas
    );

    $stmt->execute();

    $result = $stmt->get_result();

    // Jika tidak ditemukan
    if ($result->num_rows == 0) {

        $stmt->close();

        $response = [
            'status'  => 'error',
            'message' => 'Data entitas akses tidak ditemukan.'
        ];

        echo json_encode($response);
        exit;
    }

    $data_entitas = $result->fetch_assoc();

    $akses = $data_entitas['akses'];

    $stmt->close();

    // =========================================================
    // AMBIL DATA FITUR
    // =========================================================
    $id_akses_fitur = $_POST['id_akses_fitur'] ?? [];

    // Pastikan array
    if (!is_array($id_akses_fitur)) {
        $id_akses_fitur = [];
    }

    // =========================================================
    // START TRANSACTION
    // =========================================================
    mysqli_begin_transaction($Conn);

    try {

        // =====================================================
        // HAPUS REFERENSI LAMA
        // =====================================================
        $stmt_delete = $Conn->prepare("
            DELETE FROM akses_referensi
            WHERE uuid_akses_entitas = ?
        ");

        if (!$stmt_delete) {
            throw new Exception(
                'Gagal mempersiapkan penghapusan referensi lama.'
            );
        }

        $stmt_delete->bind_param(
            "s",
            $uuid_akses_entitas
        );

        if (!$stmt_delete->execute()) {
            throw new Exception(
                'Gagal menghapus referensi fitur lama.'
            );
        }

        $stmt_delete->close();

        // =====================================================
        // INSERT REFERENSI BARU
        // =====================================================
        if (!empty($id_akses_fitur)) {

            $stmt_insert = $Conn->prepare("
                INSERT INTO akses_referensi (
                    uuid_akses_entitas,
                    id_akses_fitur
                ) VALUES (
                    ?, ?
                )
            ");

            if (!$stmt_insert) {
                throw new Exception(
                    'Gagal mempersiapkan insert fitur.'
                );
            }

            foreach ($id_akses_fitur as $id_fitur) {

                $id_fitur = (int)$id_fitur;

                // Skip jika kosong
                if (empty($id_fitur)) {
                    continue;
                }

                $stmt_insert->bind_param(
                    "si",
                    $uuid_akses_entitas,
                    $id_fitur
                );

                if (!$stmt_insert->execute()) {
                    throw new Exception(
                        'Gagal menyimpan referensi fitur.'
                    );
                }
            }

            $stmt_insert->close();
        }

        // =====================================================
        // COMMIT
        // =====================================================
        mysqli_commit($Conn);

        $response = [
            'status'  => 'success',
            'message' => 'Fitur standar untuk entitas akses ' . $akses . ' berhasil diperbarui.'
        ];

        echo json_encode($response);
        exit;

    } catch (Exception $e) {

        // =====================================================
        // ROLLBACK
        // =====================================================
        mysqli_rollback($Conn);

        $response = [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];

        echo json_encode($response);
        exit;
    }
?>