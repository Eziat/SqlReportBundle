<?php
declare(strict_types=1);

namespace Eziat\SqlReportBundle\Event;

use Eziat\SqlReportBundle\Entity\SqlReportInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Tomas
 */
class FilterSqlReportResponseEvent extends Event
{
    /**
     * @var Response
     */
    private $response;
    /**
     * @var SqlReportInterface
     */
    private $sqlReport;
    /**
     * @var Request
     */
    private $request;

    public function __construct(SqlReportInterface $sqlReport, Request $request, ?Response $response)
    {
        $this->response  = $response;
        $this->sqlReport = $sqlReport;
        $this->request   = $request;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse() : ?Response
    {
        return $this->response;
    }

    public function getSqlReport() : SqlReportInterface
    {
        return $this->sqlReport;
    }

    public function getRequest() : Request
    {
        return $this->request;
    }
}