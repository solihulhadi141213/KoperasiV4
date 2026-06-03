<?php
    // Hindari notice deprecation PhpSpreadsheet lama merusak output file Excel di PHP versi baru.
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

    // Connection dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if(empty($SessionIdAkses)){
        die("Sesi akses sudah berakhir.");
    }

    // Composer Autoload
    require '../../vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;

    // Parameter dari form export
    $status = $_GET['status'] ?? '';

    // Validasi filter status
    if($status !== '' && !in_array($status, ['0', '1'], true)){
        die("Parameter status tidak valid.");
    }

    // Buat Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Nama Sheet
    $sheet->setTitle('Data Supplier');

    // Header
    $header = [
        'A1' => 'No',
        'B1' => 'Nama Supplier',
        'C1' => 'Kategori',
        'D1' => 'Email',
        'E1' => 'Kontak',
        'F1' => 'Alamat'
    ];

    foreach($header as $cell => $value){
        $sheet->setCellValue($cell, $value);
    }

    // Header Bold
    $sheet->getStyle('A1:F1')->getFont()->setBold(true);

    // Align Vertical Middle
    $sheet->getStyle('A:F')->getAlignment()->setVertical(
        Alignment::VERTICAL_CENTER
    );

    // Query Data
    if($status === ''){
        $Qry = $Conn->prepare("
            SELECT
                nama_supplier,
                kategori_supplier,
                email_supplier,
                kontak_supplier,
                alamat_supplier
            FROM supplier
            ORDER BY nama_supplier ASC
        ");
    }else{
        $Qry = $Conn->prepare("
            SELECT
                nama_supplier,
                kategori_supplier,
                email_supplier,
                kontak_supplier,
                alamat_supplier
            FROM supplier
            WHERE status = ?
            ORDER BY nama_supplier ASC
        ");
    }

    if(!$Qry){
        die("Terjadi kesalahan query : ".$Conn->error);
    }

    if($status !== ''){
        $status_int = (int)$status;
        $Qry->bind_param("i", $status_int);
    }

    $Qry->execute();
    $Result = $Qry->get_result();

    $row = 2;
    $no  = 1;

    while($Data = $Result->fetch_assoc()){
        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row, $Data['nama_supplier']);
        $sheet->setCellValue('C'.$row, $Data['kategori_supplier']);
        $sheet->setCellValue('D'.$row, $Data['email_supplier']);
        $sheet->setCellValue('E'.$row, $Data['kontak_supplier']);
        $sheet->setCellValue('F'.$row, $Data['alamat_supplier']);

        $row++;
        $no++;
    }

    $Qry->close();

    // Auto Width Semua Kolom
    foreach(range('A','F') as $column){
        $sheet->getColumnDimension($column)
              ->setAutoSize(true);
    }

    // Freeze Header
    $sheet->freezePane('A2');

    // Auto Filter
    $sheet->setAutoFilter('A1:F1');

    // Output File
    $filename = 'supplier.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>
