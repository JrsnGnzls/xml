<?php
//============================================================+
// File name   : generate_pdf_rated.php
// Description : Example of generating a PDF report using TCPDF
// Author: Nicola Asuni
// Last Update : 2013-05-14
//============================================================+

// Include the main TCPDF library
require_once('../tcpdf/tcpdf.php');

// Extend TCPDF with custom functions
class MYPDF extends TCPDF {

    // Method to load data from database
    public function LoadData($conn, $sort_column, $sort_order) {
        // Base SQL query to fetch data
        $sql = "SELECT r.news_id AS ID, n.title AS `Game Title`, ROUND(AVG(r.rating), 1) AS `Average Ratings`, COUNT(DISTINCT r.user_id) AS `Number of Users Rated`
                FROM ratings r
                INNER JOIN tbl_news n ON r.news_id = n.id
                GROUP BY r.news_id
                ORDER BY $sort_column $sort_order";

        // Execute query to fetch data
        $result = mysqli_query($conn, $sql);
        
        // Fetch all rows from the result set
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        // Free result set
        mysqli_free_result($result);

        return $data;
    }

    // Method to generate colored table in PDF
    public function ColoredTable($header, $data) {
        // Set colors, line width, and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        
        // Set column widths to match your table structure
        $w = array(20, 150, 50, 50); // Adjust widths for each column
        
        // Print header
        $this->Cell($w[0], 7, $header[0], 1, 0, 'C', 1);
        $this->Cell($w[1], 7, $header[1], 1, 0, 'C', 1);
        $this->Cell($w[2], 7, $header[2], 1, 0, 'C', 1);
        $this->Cell($w[3], 7, $header[3], 1, 0, 'C', 1);
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        
        // Data
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row['ID'], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row['Game Title'], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row['Average Ratings'], 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, $row['Number of Users Rated'], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// Create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('GAME REVIEW');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Game Review', 'XML-Based Web Applications');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Define column titles
$header = array('ID', 'Game Title', 'Average Ratings', 'Number of Users Rated');

// Establish database connection
require_once("../config.php");

// Fetch data from database based on sorting parameters
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'ID';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$data = $pdf->LoadData($conn, $sort_column, $sort_order);

// Print colored table
$pdf->ColoredTable($header, $data);

// ---------------------------------------------------------

// Close and output PDF document
$pdf->Output('pdf.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
