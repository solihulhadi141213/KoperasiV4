<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {

        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir. Silakan login ulang."
        ]);

        exit;
    }

    // =========================================================
    // FUNCTION RESPONSE
    // =========================================================
    function Response($status, $message){
        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
        exit;
    }

    // =========================================================
    // AMBIL DATA
    // =========================================================
    $id_akses            = trim($_POST['id_akses'] ?? '');
    $nama_akses          = trim($_POST['nama_akses'] ?? '');
    $kontak_akses        = trim($_POST['kontak_akses'] ?? '');
    $email               = trim($_POST['email'] ?? '');
    $uuid_akses_entitas  = trim($_POST['uuid_akses_entitas'] ?? '');

    // =========================================================
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($id_akses)) {
        Response("error", "ID akses tidak valid.");
    }

    if (empty($nama_akses)) {
        Response("error", "Nama pengguna tidak boleh kosong.");
    }

    if (empty($email)) {
        Response("error", "Alamat email tidak boleh kosong.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response("error", "Format email tidak valid.");
    }

    if (empty($uuid_akses_entitas)) {
        Response("error", "Level/entitas akses wajib dipilih.");
    }

    // =========================================================
    // VALIDASI DATA AKSES
    // =========================================================
    $stmt_old = $Conn->prepare("
        SELECT 
            id_akses,
            email,
            kontak_akses,
            uuid_akses_entitas
        FROM akses
        WHERE id_akses = ?
        LIMIT 1
    ");

    $stmt_old->bind_param("i", $id_akses);

    if (!$stmt_old->execute()) {
        Response("error", "Gagal membuka data akses.");
    }

    $result_old = $stmt_old->get_result();

    if ($result_old->num_rows == 0) {
        Response("error", "Data akses tidak ditemukan.");
    }

    $old = $result_old->fetch_assoc();

    $old_email              = $old['email'];
    $old_kontak             = $old['kontak_akses'];
    $old_uuid_entitas       = $old['uuid_akses_entitas'];

    $stmt_old->close();

    // =========================================================
    // VALIDASI ENTITAS
    // =========================================================
    $stmt_entitas = $Conn->prepare("
        SELECT akses
        FROM akses_entitas
        WHERE uuid_akses_entitas = ?
        LIMIT 1
    ");

    $stmt_entitas->bind_param("s", $uuid_akses_entitas);

    if (!$stmt_entitas->execute()) {
        Response("error", "Gagal membuka data entitas.");
    }

    $result_entitas = $stmt_entitas->get_result();

    if ($result_entitas->num_rows == 0) {
        Response("error", "Entitas akses tidak ditemukan.");
    }

    $data_entitas = $result_entitas->fetch_assoc();

    $akses = $data_entitas['akses'];

    $stmt_entitas->close();

    // =========================================================
    // VALIDASI EMAIL DUPLIKAT
    // HANYA JIKA EMAIL BERUBAH
    // =========================================================
    if ($email != $old_email) {

        $stmt_email = $Conn->prepare("
            SELECT id_akses
            FROM akses
            WHERE email = ?
            AND id_akses != ?
            LIMIT 1
        ");

        $stmt_email->bind_param("si", $email, $id_akses);

        $stmt_email->execute();

        $result_email = $stmt_email->get_result();

        if ($result_email->num_rows > 0) {
            Response("error", "Alamat email sudah digunakan.");
        }

        $stmt_email->close();
    }

    // =========================================================
    // VALIDASI KONTAK DUPLIKAT
    // HANYA JIKA KONTAK BERUBAH
    // =========================================================
    if (!empty($kontak_akses) && $kontak_akses != $old_kontak) {

        $stmt_kontak = $Conn->prepare("
            SELECT id_akses
            FROM akses
            WHERE kontak_akses = ?
            AND id_akses != ?
            LIMIT 1
        ");

        $stmt_kontak->bind_param("si", $kontak_akses, $id_akses);

        $stmt_kontak->execute();

        $result_kontak = $stmt_kontak->get_result();

        if ($result_kontak->num_rows > 0) {
            Response("error", "Nomor kontak sudah digunakan.");
        }

        $stmt_kontak->close();
    }

    // =========================================================
    // DATETIME UPDATE
    // =========================================================
    $datetime_update = date('Y-m-d H:i:s');

    // =========================================================
    // START TRANSACTION
    // =========================================================
    mysqli_begin_transaction($Conn);

    try {

        // =====================================================
        // UPDATE DATA AKSES
        // =====================================================
        $stmt_update = $Conn->prepare("
            UPDATE akses SET
                uuid_akses_entitas = ?,
                nama_akses         = ?,
                kontak_akses       = ?,
                email              = ?,
                akses              = ?,
                datetime_update    = ?
            WHERE id_akses = ?
        ");

        $stmt_update->bind_param(
            "ssssssi",
            $uuid_akses_entitas,
            $nama_akses,
            $kontak_akses,
            $email,
            $akses,
            $datetime_update,
            $id_akses
        );

        if (!$stmt_update->execute()) {
            throw new Exception("Gagal update data akses.");
        }

        $stmt_update->close();

        // =====================================================
        // JIKA ENTITAS BERUBAH
        // =====================================================
        if ($uuid_akses_entitas != $old_uuid_entitas) {

            // =================================================
            // HAPUS IJIN LAMA
            // =================================================
            $stmt_delete = $Conn->prepare("
                DELETE FROM akses_ijin
                WHERE id_akses = ?
            ");

            $stmt_delete->bind_param("i", $id_akses);

            if (!$stmt_delete->execute()) {
                throw new Exception("Gagal menghapus ijin akses lama.");
            }

            $stmt_delete->close();

            // =================================================
            // AMBIL REFERENSI FITUR BARU
            // =================================================
            $stmt_ref = $Conn->prepare("
                SELECT id_akses_fitur
                FROM akses_referensi
                WHERE uuid_akses_entitas = ?
            ");

            $stmt_ref->bind_param("s", $uuid_akses_entitas);

            if (!$stmt_ref->execute()) {
                throw new Exception("Gagal membuka referensi fitur.");
            }

            $result_ref = $stmt_ref->get_result();

            $fitur_list = [];

            while ($row = $result_ref->fetch_assoc()) {
                $fitur_list[] = (int)$row['id_akses_fitur'];
            }

            $stmt_ref->close();

            // =================================================
            // INSERT IJIN BARU
            // =================================================
            if (!empty($fitur_list)) {

                $stmt_insert_ijin = $Conn->prepare("
                    INSERT INTO akses_ijin (
                        id_akses,
                        id_akses_fitur
                    ) VALUES (?, ?)
                ");

                foreach ($fitur_list as $id_akses_fitur) {

                    $stmt_insert_ijin->bind_param(
                        "ii",
                        $id_akses,
                        $id_akses_fitur
                    );

                    if (!$stmt_insert_ijin->execute()) {
                        throw new Exception("Gagal menyimpan ijin akses baru.");
                    }
                }

                $stmt_insert_ijin->close();
            }
        }

        // =====================================================
        // COMMIT
        // =====================================================
        mysqli_commit($Conn);

        Response("success", "Data akses pengguna berhasil diperbarui.");

    } catch (Exception $e) {

        // =====================================================
        // ROLLBACK
        // =====================================================
        mysqli_rollback($Conn);

        Response("error", $e->getMessage());
    }
?>