<?php
require_once('TCPDF-main/tcpdf.php');

class MYPDF extends TCPDF {
    public function LoadData($conn) {
        $select = "SELECT * FROM payment";
        $query = mysqli_query($conn, $select);
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        return $data;
    }

    // Colored table
    public function ColoredTable($header,$data) {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        // Can change column width
        $w = array(7, 25, 20, 30, 15, 20, 25, 25 );
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        // Can get each column
        // Can get each column
        $fill = 0;
        foreach($data as $row) {
            $this->Cell($w[0], 6, $row['Payment_Id'], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row['Date'], 'LR', 0, 'L', $fill);
            // $this->Cell($w[3], 6, $row['Date_Generated'], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row['Item_Cost'], 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, $row['Service_Charge'], 'LR', 0, 'L', $fill);
            $this->Cell($w[4], 6, $row['Total_Cost'], 'LR', 0, 'L', $fill);
            $this->Cell($w[5], 6, $row['Service_Id'], 'LR', 0, 'L', $fill);
            $this->Cell($w[6], 6, $row['Cust_Id'], 'LR', 0, 'L', $fill);
            $this->Cell($w[7], 6, $row['User_Id'], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill=!$fill;
        }

        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
// Initialize database connection
$conn = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Kavidu');
$pdf->SetTitle('KMN (pvt) Ltd Report');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('helvetica', '', 8);

$pdf->AddPage();

$header = array('ID', 'PAYMENT DATE', 'ITEM COST', 'SERVICE CHARGE', 'TOTAL', 'SERVICE ID', 'CUST ID', 'USER ID');
$data = $pdf->LoadData($conn);

$pdf->ColoredTable($header, $data);

$pdf->Output('payment_pdf.pdf', 'I');

// Close database connection
mysqli_close($conn);

?>