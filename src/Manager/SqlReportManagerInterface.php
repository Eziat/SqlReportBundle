<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\Manager;

use Eziat\SqlReportBundle\Entity\SqlReportInterface;

/**
 * @author Tomas
 */
interface SqlReportManagerInterface
{
    /**
     * Creates an empty sql report.
     */
    public function createSqlReport();

    /**
     * Finds one user by the given criteria.
     */
    public function findSqlReportBy(array $criteria);

    /**
     * Gets an sql report by the given id.
     */
    public function findSqlReportById(int $id) : ?SqlReportInterface;

    /**
     * Returns the sql report's fully qualified class name.
     */
    public function findSqlReports() : array;

    /**
     * Returns the sql report's fully qualified class name.
     */
    public function findSqlReportsBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null) : array;

    /**
     * Updates an sql report.
     */
    public function updateSqlReport(SqlReportInterface $sqlReport);

    /**
     * Deletes an sql report.
     */
    public function deleteSqlReport(SqlReportInterface $sqlReport);

    /**
     * Inserts an sql report.
     */
    public function insertSqlReport(SqlReportInterface $sqlReport);

    /**
     * Returns the sql report's fully qualified class name.
     */
    public function getClass() : string;
}