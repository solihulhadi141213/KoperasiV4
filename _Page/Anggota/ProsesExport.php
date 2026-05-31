<?php
    // Connection, Function dan Session
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

    // Buat Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Nama Sheet
    $sheet->setTitle('Data Anggota');

    // Header
    $header = [
        'A1' => 'No',
        'B1' => 'Nama',
        'C1' => 'NIA',
        'D1' => 'Email',
        'E1' => 'Kontak',
        'F1' => 'Organization',
        'G1' => 'Rank',
        'H1' => 'Status',
        'I1' => 'Tanggal Daftar',
        'J1' => 'Tanggal Keluar'
    ];

    foreach($header as $cell => $value){
        $sheet->setCellValue($cell, $value);
    }

    // Header Bold
    $sheet->getStyle('A1:J1')->getFont()->setBold(true);

    // Align Vertical Middle
    $sheet->getStyle('A:J')->getAlignment()->setVertical(
        Alignment::VERTICAL_CENTER
    );

    // Query Data
    $Qry = $Conn->prepare("
        SELECT
            id_anggota,
            nia,
            nama,
            email,
            kontak,
            organization_tag,
            rank_tag,
            status,
            datetime_registered,
            datetime_leave
        FROM anggota
        ORDER BY rank_tag ASC, nama ASC
    ");

    if(!$Qry){
        die("Terjadi kesalahan query : ".$Conn->error);
    }

    $Qry->execute();
    $Result = $Qry->get_result();

    $row = 2;
    $no  = 1;

    while($Data = $Result->fetch_assoc()){

        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row, $Data['nama']);
        $sheet->setCellValue('C'.$row, $Data['nia']);
        $sheet->setCellValue('D'.$row, $Data['email']);
        $sheet->setCellValue('E'.$row, $Data['kontak']);
        $sheet->setCellValue('F'.$row, $Data['organization_tag']);
        $sheet->setCellValue('G'.$row, $Data['rank_tag']);
        $sheet->setCellValue('H'.$row, $Data['status']);
        $sheet->setCellValue('I'.$row, $Data['datetime_registered']);
        $sheet->setCellValue('J'.$row, $Data['datetime_leave']);

        $row++;
        $no++;
    }

    $Qry->close();

    // Auto Width Semua Kolom
    foreach(range('A','J') as $column){
        $sheet->getColumnDimension($column)
              ->setAutoSize(true);
    }

    // Freeze Header
    $sheet->freezePane('A2');

    // Auto Filter
    $sheet->setAutoFilter('A1:J1');

    // Output File
    $filename = 'data_anggota.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>