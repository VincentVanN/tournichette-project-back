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

        $pdfOptions->set('defaultFont', 'Courier-Bold');
        
        $this->domPdf->setPaper('A5', 'landscape');
        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("detail.pdf", [
            'Attachement' => true
        ]);
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