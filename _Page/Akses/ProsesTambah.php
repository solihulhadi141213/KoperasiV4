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
    $nama_akses         = trim($_POST['nama_akses'] ?? '');
    $kontak_akses       = trim($_POST['kontak_akses'] ?? '');
    $email              = trim($_POST['email'] ?? '');
    $password           = trim($_POST['password'] ?? '');
    $uuid_akses_entitas = trim($_POST['uuid_akses_entitas'] ?? '');

    // =========================================================
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($nama_akses)) {
        Response("error", "Nama pengguna tidak boleh kosong.");
    }

    if (empty($email)) {
        Response("error", "Email tidak boleh kosong.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response("error", "Format email tidak valid.");
    }

    if (empty($password)) {
        Response("error", "Password tidak boleh kosong.");
    }

    if (strlen($password) < 6) {
        Response("error", "Password minimal 6 karakter.");
    }

    if (empty($uuid_akses_entitas)) {
        Response("error", "Level/entitas akses wajib dipilih.");
    }

    // =========================================================
    // VALIDASI ENTITAS
    // =========================================================
    $stmt_entitas = $Conn->prepare("
        SELECT akses 
        FROM akses_entitas 
        WHERE uuid_akses_entitas = ?
    ");

    $stmt_entitas->bind_param("s", $uuid_akses_entitas);
    $stmt_entitas->execute();

    $result_entitas = $stmt_entitas->get_result();

    if ($result_entitas->num_rows == 0) {
        Response("error", "Entitas akses tidak valid.");
    }

    $data_entitas = $result_entitas->fetch_assoc();
    $akses = $data_entitas['akses'];

    $stmt_entitas->close();

    // =========================================================
    // VALIDASI EMAIL DUPLIKAT
    // =========================================================
    $stmt_email = $Conn->prepare("
        SELECT id_akses 
        FROM akses 
        WHERE email = ?
    ");

    $stmt_email->bind_param("s", $email);
    $stmt_email->execute();

    $result_email = $stmt_email->get_result();

    if ($result_email->num_rows > 0) {
        Response("error", "Alamat email sudah digunakan.");
    }

    $stmt_email->close();

    // =========================================================
    // HANDLE FILE UPLOAD
    // =========================================================
    $image_akses = "";

    if (
        isset($_FILES['image_akses']) &&
        $_FILES['image_akses']['error'] != 4
    ) {

        $file_tmp   = $_FILES['image_akses']['tmp_name'];
        $file_name  = $_FILES['image_akses']['name'];
        $file_size  = $_FILES['image_akses']['size'];
        $file_error = $_FILES['image_akses']['error'];

        // =====================================================
        // VALIDASI ERROR
        // =====================================================
        if ($file_error !== UPLOAD_ERR_OK) {
            Response("error", "Terjadi kesalahan saat upload file.");
        }

        // =====================================================
        // VALIDASI SIZE
        // =====================================================
        $max_size = 2 * 1024 * 1024;

        if ($file_size > $max_size) {
            Response("error", "Ukuran file maksimal 2 MB.");
        }

        // =====================================================
        // VALIDASI MIME TYPE
        // =====================================================
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowedMime = [
            'image/png'  => 'png',
            'image/jpeg' => 'jpg',
            'image/gif'  => 'gif',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $allowedMime)) {
            Response("error", "Tipe file tidak didukung.");
        }

        // =====================================================
        // GENERATE FILE NAME
        // =====================================================
        $extension = $allowedMime[$mime];

        $image_akses = strtolower(
            bin2hex(random_bytes(16)) . '.' . $extension
        );

        // =====================================================
        // PATH FILE
        // =====================================================
        $upload_dir  = "../../assets/img/User/";
        $upload_path = $upload_dir . $image_akses;

        // =====================================================
        // CEK DIRECTORY
        // =====================================================
        if (!is_dir($upload_dir)) {

            if (!mkdir($upload_dir, 0777, true)) {
                Response("error", "Gagal membuat folder upload.");
            }
        }

        // =====================================================
        // SIMPAN FILE
        // =====================================================
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            Response("error", "Gagal menyimpan file upload.");
        }
    }

    // =========================================================
    // HASH PASSWORD
    // =========================================================
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // =========================================================
    // DATETIME
    // =========================================================
    $datetime = date('Y-m-d H:i:s');

    // =========================================================
    // START TRANSACTION
    // =========================================================
    mysqli_begin_transaction($Conn);

    try {

        // =====================================================
        // INSERT TABEL AKSES
        // =====================================================
        $stmt_insert = $Conn->prepare("
            INSERT INTO akses (
                uuid_akses_entitas,
                nama_akses,
                kontak_akses,
                email,
                password,
                image_akses,
                akses,
                datetime_daftar,
                datetime_update
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $stmt_insert->bind_param(
            "sssssssss",
            $uuid_akses_entitas,
            $nama_akses,
            $kontak_akses,
            $email,
            $password_hash,
            $image_akses,
            $akses,
            $datetime,
            $datetime
        );

        if (!$stmt_insert->execute()) {
            throw new Exception("Gagal menyimpan data akses.");
        }

        $id_akses = $Conn->insert_id;

        $stmt_insert->close();

        // =====================================================
        // AMBIL REFERENSI FITUR
        // =====================================================
        $stmt_ref = $Conn->prepare("
            SELECT id_akses_fitur
            FROM akses_referensi
            WHERE uuid_akses_entitas = ?
        ");

        $stmt_ref->bind_param("s", $uuid_akses_entitas);

        $stmt_ref->execute();

        $result_ref = $stmt_ref->get_result();

        $fitur_list = [];

        while ($row = $result_ref->fetch_assoc()) {
            $fitur_list[] = (int)$row['id_akses_fitur'];
        }

        $stmt_ref->close();

        // =====================================================
        // INSERT AKSES IJIN
        // =====================================================
        if (!empty($fitur_list)) {

            $stmt_ijin = $Conn->prepare("
                INSERT INTO akses_ijin (
                    id_akses,
                    id_akses_fitur
                ) VALUES (?, ?)
            ");

            foreach ($fitur_list as $id_akses_fitur) {

                $stmt_ijin->bind_param(
                    "ii",
                    $id_akses,
                    $id_akses_fitur
                );

                if (!$stmt_ijin->execute()) {
                    throw new Exception("Gagal menyimpan hak akses fitur.");
                }
            }

            $stmt_ijin->close();
        }

        // =====================================================
        // COMMIT
        // =====================================================
        mysqli_commit($Conn);

        Response("success", "Data akses pengguna berhasil ditambahkan.");

    } catch (Exception $e) {

        // =====================================================
        // ROLLBACK
        // =====================================================
        mysqli_rollback($Conn);

        // =====================================================
        // HAPUS FILE JIKA ADA
        // =====================================================
        if (!empty($image_akses)) {

            $file_delete = "../../assets/img/User/" . $image_akses;

            if (file_exists($file_delete)) {
                unlink($file_delete);
            }
        }

        Response("error", $e->getMessage());
    }
?>