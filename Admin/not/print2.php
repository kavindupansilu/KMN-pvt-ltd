<?php
require "config.php";
require "fpdf/fpdf.php";
require "word.php";

$info = [
    "custid" => "",
    "customer" => "",
    "address" => "",
    "invoiceid" => "",
    "invoice_date" => "",
    "total_amt" => "",
    "paid_amt" => "",
    "balance_amt" => "",
    "words" => "",
];

// Select Invoice Details From Database
$sql = "select * from invoice where SID='{$_GET["id"]}'";
$res = $con->query($sql);
if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();

    $obj = new IndianCurrency($row["Total_Cost"]);

    $info = [
        "custid" => $row["Cust_Id"],
        "customer" => $row["Name"],
        "address" => $row["address"],
        "invoiceid" =>$row["Payment_Id"],
        "invoice_date" => date("d-m-Y", strtotime($row["Payment_Date"])),
        "total_amt" => $row["Total_Cost"],
        "paid_amt" => $row["Paid"],
        "balance_amt" => $row["Balance"],
        "words" => $obj->get_words(),
    ];
}

$products_info = [];

// Select Invoice Product Details From Database
$sql = "select * from invoice_products where SID='{$_GET["id"]}'";
$res = $con->query($sql);
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $products_info[] = [
            "iname" => $row["Item_Name"],
            "price" => $row["PRICE"],
            "qty" => $row["QTY"],
            "itotal" => $row["Item_Cost"],
            "serviceid" => $row["Service_Id"],
            "typeservice" => $row["Type_of_Service"],
            "stotal" => $row["Service_Charge"],
        ];
    }
}

// Assuming $row contains the fetched invoice details
$payment_id = $row["Payment_Id"];
$formatted_payment_id = sprintf("%03d", $payment_id); // Formats the payment ID to have leading zeros if necessary


class PDF extends FPDF
{
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
        $this->Cell(50, 10, "INVOICE", 0, 1);

        //Display Horizontal line
        $this->Line(0, 48, 210, 48);
    }

    function body($info, $products_info)
    {
        //Billing Details
        $this->SetY(55);
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50, 10, "Bill To: ", 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(50, 7, $info["custid"], 0, 1);
        $this->Cell(50, 7, $info["customer"], 0, 1);
        $this->Cell(50, 7, $info["address"], 0, 1);

        //Display Invoice no
        $this->SetY(55);
        $this->SetX(-60);
        $this->Cell(50, 7, "Payment Id : " . $info["invoiceid"]);
        
        //Display Invoice date
        $this->SetY(63);
        $this->SetX(-60);
        $this->Cell(60, 7, "Payment Date : " . $info["invoice_date"]);

        //Display Table headings
        $this->SetY(95);
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20, 9, "ITEM", 1, 0);
        $this->Cell(20, 9, "PRICE", 1, 0, "C");
        $this->Cell(20, 9, "QTY", 1, 0, "C");
        $this->Cell(30, 9, "ITEM TOTAL", 1, 0, "C");
        $this->Cell(25, 9, "SERVICE ID", 1, 0, "C");
        $this->Cell(40, 9, "SERVICE TYPE", 1, 0, "C");
        $this->Cell(40, 9, "SERVICE CHARGE", 1, 1, "C");
        $this->SetFont('Arial', '', 12);

        //Display table product rows
        foreach ($products_info as $row) {
            $this->Cell(20, 9, $row["iname"], "LR", 0);
            $this->Cell(20, 9, $row["price"], "R", 0, "R");
            $this->Cell(20, 9, $row["qty"], "R", 0, "C");
            $this->Cell(30, 9, $row["itotal"], "R", 0, "R");
            $this->Cell(25, 9, $row["serviceid"], "R", 0, "R");
            $this->Cell(40, 9, $row["typeservice"], "R", 0, "C");
            $this->Cell(40, 9, $row["stotal"], "R", 1, "R");
        }

        //Display table empty rows
        for ($i = 0; $i < 12 - count($products_info); $i++) {
            $this->Cell(20, 9, "", "LR", 0);
            $this->Cell(20, 9, "", "R", 0, "R");
            $this->Cell(20, 9, "", "R", 0, "C");
            $this->Cell(30, 9, "", "R", 0, "R");
            $this->Cell(25, 9, "", "R", 0, "R");
            $this->Cell(40, 9, "", "R", 0, "C");
            $this->Cell(40, 9, "", "R", 1, "R");
        }

        //Display table total row
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(155, 9, "TOTAL", 1, 0, "R");
        $this->Cell(40, 9, $info["total_amt"], 1, 1, "R");
        $this->Cell(155, 9, "PAID", 1, 0, "R");
        $this->Cell(40, 9, $info["paid_amt"], 1, 1, "R");
        $this->Cell(155, 9, "BALANCE", 1, 0, "R");
        $this->Cell(40, 9, $info["balance_amt"], 1, 1, "R");

        //Display amount in words
        $this->SetY(250);
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 9, "Amount in Words ", 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 9, $info["words"], 0, 1);
    }

    function Footer()
    {
        //set footer position
        $this->SetY(255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "for KMN (pvt) Ltd", 0, 1, "R");
        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Authorized Signature", 0, 1, "R");
        $this->SetFont('Arial', '', 10);

        //Display Footer Text
        $this->Cell(0, 10, "This is a computer generated invoice", 0, 1, "C");
    }
}

// Create A4 Page with Portrait
$pdf = new PDF("P", "mm", "A4");
$pdf->AddPage();
$pdf->body($info, $products_info);
ob_clean(); // Clear the output buffer
$pdf->Output();
?>
