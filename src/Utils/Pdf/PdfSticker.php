<?php

namespace App\Utils\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfSticker
{
    private $domPdf;

    public function __construct()
    {
        $this->domPdf = new DomPdf();

        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'Courier');
        
        $this->domPdf->setPaper('A5', 'landscape');
        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html)
    {   
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("detail.pdf", [
            'Attachement' => false
        ]);
    }

    public function generateBinaryPDF($html)
    {   
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}