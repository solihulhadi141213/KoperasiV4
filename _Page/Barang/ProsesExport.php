<?php
    ob_start();
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {
        die("Sesi akses sudah berakhir.");
    }

    // =========================================================
    // LOAD PHPSPREADSHEET
    // =========================================================
    require '../../vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Style\Fill;

    // =========================================================
    // FILTER
    // =========================================================
    $status = $_GET['status'] ?? '';

    // =========================================================
    // QUERY
    // =========================================================
    $sql = "SELECT * FROM barang";

    if ($status !== '') {
        $sql .= " WHERE status=?";
    }

    $sql .= " ORDER BY nama ASC";

    $stmt = mysqli_prepare($Conn, $sql);

    if ($status !== '') {
        mysqli_stmt_bind_param($stmt, "i", $status);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // =========================================================
    // SPREADSHEET
    // =========================================================
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Barang');

    // =========================================================
    // HEADER
    // =========================================================
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Kode');
    $sheet->setCellValue('C1', 'Nama Barang');
    $sheet->setCellValue('D1', 'Kategori');
    $sheet->setCellValue('E1', 'Stok');
    $sheet->setCellValue('F1', 'Satuan');
    $sheet->setCellValue('G1', 'Harga Beli');
    $sheet->setCellValue('H1', 'Harga Jual (Standar)');
    $sheet->setCellValue('I1', 'Status');

    // =========================================================
    // STYLE HEADER
    // =========================================================
    $sheet->getStyle('A1:I1')->applyFromArray([
        'font' => [
            'bold' => true
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => 'D9EAD3'
            ]
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);

    // =========================================================
    // DATA
    // =========================================================
    $no = 1;
    $row = 2;

    while ($data = mysqli_fetch_assoc($result)) {

        $status_label = ($data['status'] == 1) ? 'Active' : 'Inactive';

        $sheet->setCellValue('A' . $row, $no);
        $sheet->setCellValue('B' . $row, $data['kode']);
        $sheet->setCellValue('C' . $row, $data['nama']);
        $sheet->setCellValue('D' . $row, $data['kategori']);
        $sheet->setCellValue('E' . $row, $data['stok']);
        $sheet->setCellValue('F' . $row, $data['satuan']);
        $sheet->setCellValue('G' . $row, $data['harga_beli']);
        $sheet->setCellValue('H' . $row, $data['harga_jual']);
        $sheet->setCellValue('I' . $row, $status_label);

        $row++;
        $no++;
    }

    // =========================================================
    // FORMAT ANGKA
    // =========================================================
    if ($row > 2) {

        $sheet->getStyle('G2:G' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->getStyle('H2:H' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->getStyle('E2:E' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle('A1:I' . ($row - 1))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);
    }

    // =========================================================
    // AUTO SIZE
    // =========================================================
    foreach (range('A', 'I') as $column) {
        $sheet->getColumnDimension($column)
            ->setAutoSize(true);
    }

    // =========================================================
    // FREEZE HEADER
    // =========================================================
    $sheet->freezePane('A2');

    // =========================================================
    // OUTPUT FILE
    // =========================================================
    $filename = 'barang_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

?>