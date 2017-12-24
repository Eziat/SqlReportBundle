<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\Event;

use Eziat\SqlReportBundle\Entity\SqlReport;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Tomas
 */
class ListSqlReportEvent extends Event
{
    /** @var SqlReport[] */
    private $sqlReports;

    public function __construct()
    {
        $this->sqlReports = [];
    }

    /**
     * @param SqlReport[]
     */
    public function setSqlReports(array $sqlReport)
    {
        $this->sqlReports = $sqlReport;
    }

    /**
     * @return SqlReport[]
     */
    public function getSqlReports() : array
    {
        return $this->sqlReports;
    }
}