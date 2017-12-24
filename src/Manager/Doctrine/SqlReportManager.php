<?php
declare(strict_types=1);

namespace Eziat\SqlReportBundle\Manager\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Eziat\SqlReportBundle\Entity\SqlReportInterface;
use Eziat\SqlReportBundle\Manager\SqlReportManager as BaseSqlReportManager;

/**
 * @author Tomas
 */
class SqlReportManager extends BaseSqlReportManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    private $class;

    public function __construct(ObjectManager $om, string $class)
    {
        $this->objectManager = $om;
        $this->class         = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function findSqlReportBy(array $criteria) : ?SqlReportInterface
    {
        return $this->objectManager->getRepository($this->getClass())->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSqlReport(SqlReportInterface $sqlReport)
    {
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSqlReport(SqlReportInterface $sqlReport)
    {
        $this->objectManager->remove($sqlReport);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function insertSqlReport(SqlReportInterface $sqlReport)
    {
        $this->objectManager->persist($sqlReport);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findSqlReportsBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null) : array
    {
        return $this->objectManager->getRepository($this->getClass())->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getClass() : string
    {
        if (false !== strpos($this->class, ':')) {
            $metadata    = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }
}