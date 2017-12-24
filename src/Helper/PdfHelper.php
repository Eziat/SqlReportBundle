<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\Helper;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class to check if the given user has a role in the role hierarchy
 *
 * @author Martin, Tomas
 */
class PdfHelper
{
    /**
     * @var Pdf
     */
    private $knpSnappy;

    /**
     * PdfHelper constructor.
     *
     * @param Pdf $knpSnappy
     */
    public function __construct(Pdf $knpSnappy)
    {
        $this->knpSnappy = $knpSnappy;
    }

    /**
     * Given HTML code that should be printed as PDF:
     * Return a response object that can be directly used as return
     * value in an action
     *
     * @param            $htmlContent
     * @param array      $parameters
     * @param string     $filename
     *
     * @return Response
     */
    public function getPdfResponse($htmlContent, $parameters = [], $filename = "export.pdf")
    {
        $pdfSettingsArray = [
            "print-media-type" => true,
        ];

        if (isset($parameters["pdfOrientation"])) {
            $pdfSettingsArray["orientation"] = $parameters["pdfOrientation"];
        }
        $this->knpSnappy->setTimeout(720);

        return new Response(
            $this->knpSnappy->getOutputFromHtml($htmlContent, $pdfSettingsArray),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename,
            ]
        );
    }

}