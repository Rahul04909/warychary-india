<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once '../../vendor/autoload.php'; // Verify autoload path
include_once '../../database/db_config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'partner'; // 'partner' or 'senior_partner'
    
    $database = new Database();
    $db = $database->getConnection();

    // Data Gathering Logic (reuse query logic)
    if ($type == 'partner') {
        $filename = "Partner_Payouts_" . date('Y-m-d') . ".xlsx";
        $sql = "SELECT 
            p.id, p.name, p.mobile, p.email,
            COALESCE(e.total_earnings, 0) as total_earnings,
            COALESCE(ph.total_paid, 0) as total_paid,
            bd.account_number, bd.ifsc_code, bd.bank_name
        FROM partners p
        LEFT JOIN (SELECT partner_id, SUM(amount) as total_earnings FROM partner_earnings GROUP BY partner_id) e ON p.id = e.partner_id
        LEFT JOIN (SELECT user_id, SUM(amount) as total_paid FROM payout_history WHERE user_type = 'partner' AND status = 'processed' GROUP BY user_id) ph ON p.id = ph.user_id
        LEFT JOIN bank_details bd ON p.id = bd.user_id AND bd.user_type = 'partner'
        ORDER BY p.id DESC";
    } else {
        $filename = "Senior_Partner_Payouts_" . date('Y-m-d') . ".xlsx";
        $sql = "SELECT 
            sp.id, sp.name, sp.email,
            COALESCE(e.total_earnings, 0) as total_earnings,
            COALESCE(ph.total_paid, 0) as total_paid,
            bd.account_number, bd.ifsc_code, bd.bank_name
        FROM senior_partners sp
        LEFT JOIN (SELECT senior_partner_id, SUM(amount) as total_earnings FROM senior_partner_earnings GROUP BY senior_partner_id) e ON sp.id = e.senior_partner_id
        LEFT JOIN (SELECT user_id, SUM(amount) as total_paid FROM payout_history WHERE user_type = 'senior_partner' AND status = 'processed' GROUP BY user_id) ph ON sp.id = ph.user_id
        LEFT JOIN bank_details bd ON sp.id = bd.user_id AND bd.user_type = 'senior_partner'
        ORDER BY sp.id DESC";
    }

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Headlines
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Name');
    $sheet->setCellValue('C1', 'Email');
    $sheet->setCellValue('D1', 'Mobile'); // Senior Partner might pass NULL if column missing, handle gracefully
    $sheet->setCellValue('E1', 'Account Number');
    $sheet->setCellValue('F1', 'IFSC Code');
    $sheet->setCellValue('G1', 'Bank Name');
    $sheet->setCellValue('H1', 'Total Earnings');
    $sheet->setCellValue('I1', 'Total Paid');
    $sheet->setCellValue('J1', 'Pending Balance');

    $row = 2;
    foreach ($data as $d) {
        $pending = $d['total_earnings'] - $d['total_paid'];
        
        $sheet->setCellValue('A' . $row, $d['id']);
        $sheet->setCellValue('B' . $row, $d['name']);
        $sheet->setCellValue('C' . $row, $d['email']);
        $sheet->setCellValue('D' . $row, $d['mobile'] ?? 'N/A');
        
        // Force string type for account number to prevent scientific notation
        $sheet->setCellValueExplicit('E' . $row, $d['account_number'] ?? 'N/A', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        
        $sheet->setCellValue('F' . $row, $d['ifsc_code'] ?? 'N/A');
        $sheet->setCellValue('G' . $row, $d['bank_name'] ?? 'N/A');
        $sheet->setCellValue('H' . $row, $d['total_earnings']);
        $sheet->setCellValue('I' . $row, $d['total_paid']);
        $sheet->setCellValue('J' . $row, $pending);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
    $writer->save('php://output');
    exit;
}
?>
