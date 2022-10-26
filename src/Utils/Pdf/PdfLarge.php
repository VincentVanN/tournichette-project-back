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

    public function showPdfFile($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render($html);
        $this->domPdf->stream("detail.pdf", [
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

    /*
    global
    lot
    point dépôt
    total cumul par produits
    total produit par dépôt
    qté global produit par point dépôt
    nombre de lot par qté vendu
    client global
    client par point dépôt

array 
nom produit
unity
prix

commande client
tel
nom
detail
    */
}