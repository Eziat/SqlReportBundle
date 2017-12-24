<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Eziat\SqlReportBundle\Entity\SqlReport;
use Symfony\Component\DependencyInjection\ExpressionLanguage;

/**
 * @author Martin
 */
class EziatSqlReportHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SqlReportHelper constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Execute the query of this report.
     * Return result array, headers and errorMessage:
     *
     * @return array [$result, $headers, $errorMessage]
     * @throws \Exception
     */
    public function getQueryResult(SqlReport $sqlReport)
    {
        $queryString = $sqlReport->getQuery();
        $result      = null;
        $errMsg      = "";
        if (null != $sqlReport->getProjection()) {
            $language = new ExpressionLanguage();
        }
        $headers = $sqlReport->getHeadersArray();
        if ($sqlReport->getType() == SqlReport::SQL) {
            $connection = $this->em->getConnection();
            $statement  = $connection->prepare($queryString);
            try {
                $statement->execute();
                if (null != $sqlReport->getProjection()) {
                    $result = [];
                    $proj   = explode(";", $sqlReport->getProjection());
                    // evaluate all expressions in the projection field
                    // (separated by comma) for each row and store
                    // this row result in the result array
                    while ($row = $statement->fetch()) {
                        $rr = [];
                        foreach ($proj as $p) {
                            $rr[$p] = $language->evaluate($p, $row);

                        }
                        $result [] = $rr;
                    }
                } else {
                    $result = $statement->fetchAll();

                }
            } catch (\Exception $e) {
                $errMsg = $e->getMessage();
            }
        } else {
            $doctrineQuery = $this->em->createQuery($queryString);
            try {

                if (null != $sqlReport->getProjection()) {
                    $result = [];
                    $proj   = explode(";", $sqlReport->getProjection());
                    // evaluate all expressions in the projection field
                    // (separated by comma) for each row and store
                    // this row result in the result array
                    $l = $doctrineQuery->getResult($doctrineQuery::HYDRATE_OBJECT);
                    foreach ($l as $row) {
                        $rr = [];
                        foreach ($proj as $p) {
                            $rr[$p] = $language->evaluate('row.'.$p, [
                                'row' => $row,
                            ]);
                        }
                        $result [] = $rr;

                    }
                } else {
                    $result = $doctrineQuery->getScalarResult();
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        if (!$headers && $result) {
            // if no headers are set explicitly, use the information of the DB.
            $headers = array_keys($result[0]);
        }

        return [$result, $headers, $errMsg];
    }
}
