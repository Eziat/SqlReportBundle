<?php
declare(strict_types = 1);

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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $this->projectDir       = $projectDir;
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

    /**
     * @param int    $id
     * @param string $exportType
     *
     * @return StreamedResponse
     * @throws \Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportAction(int $id, string $exportType, SqlReportHelper $sqlReportHelper)
    {
        /** @var SqlReport $sqlReport */
        $sqlReport = $this->sqlReportManager->findSqlReportById($id);

        $event = new SqlReportEvent($sqlReport);
        $this->eventDispatcher->dispatch(EziatSqlReportEvents::SQL_REPORT_EXPORT_INITIALIZE, $event);

        list($resultArray, $headers, $errMsg) = $sqlReportHelper->getQueryResult($sqlReport);

        $phpExcelObject = new Spreadsheet();

        array_unshift($resultArray, $headers);
        $phpExcelObject->setActiveSheetIndex(0)
                       ->fromArray($resultArray);

        $contentType = SqlReport::$CONTENT_TYPES[$exportType];

        if ($exportType == "PDF") {
            $objWriter = IOFactory::createWriter($phpExcelObject, 'Tcpdf');
        } else {
            $objWriter = IOFactory::createWriter($phpExcelObject, $exportType);
        }
        $response = $this->createStreamedResponse($objWriter);
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

     /**
     * Stream the file as Response.
     */
    public function createStreamedResponse(IWriter $writer, int $status = 200, array $headers = array()) : StreamedResponse
    {
        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $status,
            $headers
        );
    }
}