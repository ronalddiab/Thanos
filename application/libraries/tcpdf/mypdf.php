<?php

// Include the main TCPDF library (search for installation path).
require_once('tcpdf.php');

class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-10); // $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 11, '', true);
        // set color for background
        $this->SetFillColor(255, 255, 200);
        // set color for text
        $this->SetTextColor(0, 63, 127);
        $this->writeHTMLCell('', '', '5', '', 'Copyright &copy; 2000 - '.date("Y").' Peter Kirkland - <a style="display:inline-block; text-decoration:none; color:#003F7F;" href="www.plantfile.com">www.plantfile.com</a>', 1, 1, true, true, 'C', true);
    }
}
