<?php
require "config.php";
require "fpdf/fpdf.php";
require "word.php";

$info = [
    "manid" => "",
    "repid" => "",
    "reptype" => "",
    "rep_date" => "",
    "report" => "",
    "user_id" => "",
    "sercost" => "",
    "partsale" => "",
    "words" => "",
];

// Check if Report_Id is set in $_GET
if (isset($_GET["Report_Id"])) {
    // Select Invoice Details From Database
    $reportId = $_GET["Report_Id"];
    $sql = "SELECT * FROM report WHERE Report_Id = '$reportId'";
    $res = $con->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();

        $obj = new IndianCurrency($row["Total_Cost"]);

        $info = [
            "manid" => $row["Manager_Id"],
            "repid" => $row["Report_Id"],
            "reptype" => $row["Report_Type"],
            "rep_date" => $row["Date_Generated"],
            "report" => $row["Content"],
            "user_id" => $row["User_Id"],
            "sercost" => $row["Service_Revenue"],
            "partsale" => $row["InStock"],
            "words" => $obj->get_words(),
        ];
    }
}

class PDF extends FPDF
{
    // setY = vertical space
    // setX = horizontal space
    function Header()
    {
        //Display Company Info
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(50, 10, "KMN (pvt) Ltd", 0, 1);
        $this->SetFont('Arial', '', 14);
        $this->Cell(50, 7, "Bolawalana Road,", 0, 1);
        $this->Cell(50, 7, "Negombo 11500.", 0, 1);
        $this->Cell(50, 7, "Tel : 0715551771", 0, 1);

        //Display INVOICE text
        $this->SetY(15);
        $this->SetX(-40);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(50, 10, "REPORT", 0, 1);

        //Display Horizontal line
        $this->Line(0, 48, 210, 48);
    }

    function body($info)
{
    // Manager Details
    $this->SetY(55);
    $this->SetX(10);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(50, 10, "Report To: ", 0, 1);
    $this->SetFont('Arial', '', 12);
    $this->Cell(50, 7, $info["manid"], 0, 1); 

    // Report Details
    $this->SetY(55);
    $this->SetX(-60);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(50, 10, "Report Details: ", 0, 1);
    $this->SetY(65);
    $this->SetX(-60);
    $this->SetFont('Arial', '', 12);
    $this->Cell(50, 7, $info["repid"], 0, 1);
    $this->SetY(79);
    $this->SetX(-60);
    $this->Cell(50, 7, $info["reptype"], 0, 1);

    // Display Table headings
    $this->SetY(95);
    $this->SetX(10);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(30, 9, "DATE", 1, 0, "C");
    $this->Cell(80, 9, "CONTENT", 1, 0, "C");
    $this->Cell(30, 9, "INSTOCK", 1, 0, "C");
    $this->Cell(50, 9, "SERVICE REVENUE", 1, 0, "C");
    $this->SetFont('Arial', '', 12);
    
    // Set initial Y position for the table content
    $y = $this->GetY();
    
    // Display table product rows
    $this->SetY($y + 10);
    $this->SetX(10);
    $this->SetFont('Arial', '', 12);
    $this->Cell(30, 9, $info["rep_date"], 1, 0, "C");
    $this->MultiCell(80, 9, $info["report"], 1, "C");
    $this->SetY($y + 10);
    $this->SetX(120);
    $this->Cell(30, 9, $info["partsale"], 1, 0, "C");
    $this->Cell(50, 9, $info["sercost"], 1, 0, "C");
    
    // Display table empty rows if needed
    $this->SetY($y + 19);
    for ($i = 0; $i < 11; $i++) {
        $this->SetX(10);
        $this->Cell(30, 9, "", 1, 0, "C");
        $this->Cell(80, 9, "", 1, 0, "C");
        $this->Cell(30, 9, "", 1, 0, "C");
        $this->Cell(50, 9, "", 1, 0, "C");
    }
}



    function Footer()
    {
        //set footer position
        $this->SetY(255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "for KMN (pvt) Ltd", 0, 1, "R");
        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Authorized Signature: " . $this->user_id,0,1, "R");
        // $this->Cell(0, 9, $info["user_id"], 1, 1, "R");
        $this->SetFont('Arial', '', 10);

        //Display Footer Text
        $this->Cell(0, 10, "This is a computer-generated report", 0, 1, "C");
    }
}

// Create A4 Page with Portrait
$pdf = new PDF("P", "mm", "A4");
$pdf->user_id = $info['user_id']; // Assigning user_id to the property
$pdf->AddPage();
$pdf->body($info);
$pdf->Output();
?>