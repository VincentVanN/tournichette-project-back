<?php

namespace App\Utils\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfLarge
{
    private $domPdf;

    public function __construct()
    {
        $this->domPdf = new DomPdf();

        $pdfOptions = new Options();

        $pdfOptions->set('defaultfont', 'Courier');

        
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html, $namePdf = 'detail.pdf')
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render($html);
        $this->domPdf->stream($namePdf, [
            'Attachement' => false
        ]);
        $this->domPdf->output();
    }

    public function generateBinaryPDF($html)
    {   
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}