<?php
declare(strict_types=1);

namespace Eziat\SqlReportBundle\Event;

use Eziat\SqlReportBundle\Entity\SqlReport;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Tomas
 */
class SqlReportEvent extends Event
{
    /** @var SqlReport */
    private $sqlReport;

    public function __construct(SqlReport $sqlReport)
    {
        $this->sqlReport = $sqlReport;
    }

    public function getSqlReport() : SqlReport
    {
        return $this->sqlReport;
    }
}