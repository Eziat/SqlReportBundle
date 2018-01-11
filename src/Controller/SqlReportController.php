<?php
declare(strict_types=1);

namespace Eziat\SqlReportBundle\Controller;

use Eziat\SqlReportBundle\Entity\SqlReport;
use Eziat\SqlReportBundle\Event\EziatSqlReportEvents;
use Eziat\SqlReportBundle\Event\FilterSqlReportResponseEvent;
use Eziat\SqlReportBundle\Event\FormEvent;
use Eziat\SqlReportBundle\Event\ListSqlReportEvent;
use Eziat\SqlReportBundle\Event\SqlReportEvent;
use Eziat\SqlReportBundle\Form\SqlReportType;
use Eziat\SqlReportBundle\Helper\PdfHelper;
use Eziat\SqlReportBundle\Helper\SqlReportHelper;
use Eziat\SqlReportBundle\Manager\SqlReportManager;
use PHPExcel;
use PHPExcel_Settings;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Writer_PDF_tcPDF;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Tomas
 */
class SqlReportController extends AbstractController
{
    /** @var SqlReportManager */
    private $sqlReportManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $projectDir;

    public function __construct(EventDispatcherInterface $eventDispatcher, SqlReportManager $sqlReportManager, string $projectDir)
    {
        $this->sqlReportManager = $sqlReportManager;
        $this->eventDispatcher  = $eventDispatcher;
        $this->projectDir = $projectDir;
    }

    public function listAction()
    {
        $event = new ListSqlReportEvent();
        $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_LIST_INITIALIZE, $event);

        $sqlReports = $event->getSqlReports();
        if ($sqlReports === []) {
            $sqlReports = $this->sqlReportManager->findSqlReports();
        }

        $groupedReportsArray = $this->sqlReportManager->groupReportsByReportGroup($sqlReports);

        return $this->render('@EziatSqlReport/SqlReport/list.html.twig', [
            'groupedReportsArray' => $groupedReportsArray,
        ]);
    }

    public function editAction(Request $request, int $id)
    {
        if ($id) {
            $sqlReport = $this->sqlReportManager->findSqlReportById($id);
        } else {
            $sqlReport = $this->sqlReportManager->createSqlReport();
        }
        $form = $this->getForm($sqlReport);

        $event = new FormEvent($form, $request);
        $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_EDIT_INITIALIZE, $event);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_EDIT_SUCCESS, $event);
                $this->sqlReportManager->insertSqlReport($sqlReport);

                $response = $event->getResponse();
                $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_EDIT_COMPLETE, new FilterSqlReportResponseEvent($sqlReport, $request, $response));
                if ($event->getResponse() !== null) {
                    return $event->getResponse();
                }
            }
        }

        return $this->render('@EziatSqlReport/SqlReport/edit.html.twig', [
            'entity' => $sqlReport,
            'form'   => $form->createView(),
        ]);
    }

    public function showAction(Request $request, int $id, SqlReportHelper $sqlReportHelper)
    {
        /** @var SqlReport $sqlReport */
        $sqlReport = $this->sqlReportManager->findSqlReportById($id);

        $event = new SqlReportEvent($sqlReport);
        $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_SHOW_INITIALIZE, $event);

        list($resultArray, $headers, $errMsg) = $sqlReportHelper->getQueryResult($sqlReport);

        return $this->render('@EziatSqlReport/SqlReport/show.html.twig', [
            'sqlReport'   => $sqlReport,
            'result'      => $resultArray,
            'headers'     => $headers,
            'resultSize'  => $resultArray === null ? 0 : sizeof($resultArray),
            'exportTypes' => SqlReport::$EXPORT_TYPES,
            'errMsg'      => $errMsg,
            'printView'   => $request->get("printView"),
        ]);
    }

    public function pdfAction(Request $request, int $id, SqlReportHelper $helper, PdfHelper $pdfHelper)
    {
        /** @var SqlReport $sqlReport */
        $sqlReport = $this->sqlReportManager->findSqlReportById($id);

        $event = new SqlReportEvent($sqlReport);
        $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_PDF_INITIALIZE, $event);

        $filename = $sqlReport->getFileName("PDF");

        // get HTML of ShowAction
        $htmlContent = $this->showAction($request, $id, $helper)->getContent();

        return $pdfHelper->getPdfResponse($htmlContent, $request->query->all(), $filename);
    }

    public function exportAction(int $id, string $exportType )
    {
        /** @var SqlReport $sqlReport */
        $sqlReport = $this->sqlReportManager->findSqlReportById($id);

        $event = new SqlReportEvent($sqlReport);
        $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_EXPORT_INITIALIZE, $event);

        list($resultArray, $headers, $errMsg) = $this->get(SqlReportHelper::class)->getQueryResult($sqlReport);

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        array_unshift($resultArray, $headers);
        /* @var $phpExcelObject PHPExcel */
        $phpExcelObject->setActiveSheetIndex(0)
                       ->fromArray($resultArray);

        $contentType = SqlReport::$CONTENT_TYPES[$exportType];

        if ($exportType == "PDF") {
            $this->_initOfficePdfExorter();
            $objWriter = new PHPExcel_Writer_PDF_tcPDF($phpExcelObject);
            $objWriter->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3);
        } else {
            $objWriter = $this->get('phpexcel')->createWriter($phpExcelObject, $exportType);
        }
        $response = $this->get('phpexcel')->createStreamedResponse($objWriter);
        $response->headers->set('Content-Type', "$contentType; charset=utf-8");
        $response->headers->set(
            'Content-Disposition', 'attachment; filename="'
                                   .$sqlReport->getFileName($exportType).'"');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    private function getForm(SqlReport $entity) : FormInterface
    {
        return $this->createForm(
            SqlReportType::class, $entity, [
            'method' => 'POST',
        ]);
    }

    private function _initOfficePdfExorter()
    {
        $rendererName        = PHPExcel_Settings::PDF_RENDERER_TCPDF;
        $rendererLibraryPath = $this->projectDir.'/vendor/tecnickcom/tcpdf';

        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
            die("Renderer not found at".$rendererLibraryPath);
        }
    }
}