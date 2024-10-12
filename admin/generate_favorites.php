<?php
//============================================================+
// File name   : example_011.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 011 for TCPDF class
//               Colored Table (very simple table)
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Colored Table
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf.php');

// extend TCPF with custom functions
class MYPDF extends TCPDF {

    // Load table data from file
    public function LoadData($conn) {
        // Read file lines
        require_once("../config.php");
        $sql = "SELECT * FROM favorites";
        $query = mysqli_query($conn, $sql);
        return $query;
    }

    // Colored table
    public function ColoredTable($header, $data, $conn) {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        
        // Set column widths
        $w = array(155, 70, 42); // Adjusted widths for ID, Name, and Role
        
        // Header
        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        
        // Data
        $fill = 0;
        foreach ($data as $row) {
            // Fetch title based on item_id
            $title_query = "SELECT title FROM tbl_news WHERE id = '{$row['news_id']}'";
            $title_result = mysqli_query($conn, $title_query);
            $title_row = mysqli_fetch_assoc($title_result);
            $title = $title_row['title'];
        
            // Fetch username based on user_id
            $username_query = "SELECT username FROM users WHERE id = '{$row['user_id']}'";
            $username_result = mysqli_query($conn, $username_query);
            $username_row = mysqli_fetch_assoc($username_result);
            $username = $username_row['username'];
        
            // Output the data with titles and usernames
            $this->Cell($w[0], 6, $title, 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $username, 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, $row['created_at'], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('GAME REVIEW');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Game Review', 'XML-Based Web Applications');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

// column titles
$header = array('GAME TITLE', 'NAME', 'CREATED_AT');

// Establish database connection
require_once("../config.php");

// data loading
$data = $pdf->LoadData($conn);

// print colored table
$pdf->ColoredTable($header, $data, $conn);

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('pdf.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+