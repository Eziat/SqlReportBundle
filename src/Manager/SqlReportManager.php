<?php
declare(strict_types=1);

namespace Eziat\SqlReportBundle\Manager;

use Eziat\SqlReportBundle\Entity\SqlReportInterface;

/**
 * @author Tomas
 */
abstract class SqlReportManager implements SqlReportManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSqlReport()
    {
        $class     = $this->getClass();
        $sqlReport = new $class();

        return $sqlReport;
    }

    /**
     * {@inheritdoc}
     */
    public function findSqlReportById(int $id) : ?SqlReportInterface
    {
        return $this->findSqlReportBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function findSqlReports() : array
    {
        return $this->findSqlReportsBy([]);
    }

    /**
     * Returns all SQL Reports inside a 3-dimensional array
     * which is grouped by the "reportGroup" attribute and uses
     * "_" as separator
     * [group][sub-group][reports] => sqlReportObject
     */
    public function findAllActive(?bool $onlyActive = false) : array
    {
        $filterArray = [];
        if ($onlyActive) {
            $filterArray['active'] = true;
        }

        return $this->findSqlReportsBy(
            $filterArray, [
                "sortBy"      => "ASC",
                "active"      => "DESC",
                "reportGroup" => "ASC",
                "title"       => "ASC",
            ]
        );
    }

    /**
     * @param SqlReportInterface[]
     *
     * @return SqlReportInterface[]
     */
    public function groupReportsByReportGroup(array $sqlReports) : array
    {
        $returnArray = [];
        foreach ($sqlReports as $sqlReport) {
            $splitReportsGroup               = explode("_", $sqlReport->getReportGroup());
            $group1                          = array_shift($splitReportsGroup);
            $group2                          = implode("_", $splitReportsGroup);
            $returnArray[$group1][$group2][] = $sqlReport;
            ksort($returnArray[$group1]);
        }
        ksort($returnArray);

        return $returnArray;
    }
}