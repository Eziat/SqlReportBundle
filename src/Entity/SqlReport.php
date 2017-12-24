<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Tomas
 */
abstract class SqlReport implements SqlReportInterface
{
    const SQL = 0;
    const DQL = 1;

    public static $EXPORT_TYPES = [
        'CSV'        => 'CSV',
        'Excel 5'    => 'Excel5',
        'Excel 2007' => 'Excel2007',
        'HTML'       => 'HTML',
        'PDF'        => 'PDF',
    ];
    public static $FILE_EXTENSIONS = [
        'OOCalc'    => "ods",
        'Excel5'    => "xls",
        'Excel2007' => "xlsx",
        'HTML'      => 'html',
        'CSV'       => 'csv',
        'PDF'       => 'pdf',
    ];
    public static $CONTENT_TYPES = [
        'OOCalc'    => "ods",
        'Excel5'    => "text/vnd.ms-excel",
        'Excel2007' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        'HTML'      => 'text/html',
        'CSV'       => 'text/csv',
        'PDF'       => 'application/pdf',
    ];
    /**
     * @var mixed
     */
    private $id;
    /**
     * @var string
     */
    private $title = '';
    /**
     * @var string
     */
    private $description = '';
    /**
     * @var string
     */
    private $query = '';
    /**
     * @var string
     */
    private $headers = '';
    /**
     * @var string
     */
    private $projection = '';
    /**
     * @var integer
     */
    private $type = 0;
    /**
     * @var string
     */
    private $reportGroup = '';
    /**
     * @var integer
     */
    private $sortBy = 0;
    /**
     * @var boolean
     */
    private $active;

    public function __construct()
    {
        $this->active = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(string $title) : SqlReportInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description) : SqlReportInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery() : string
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery(string $query) : SqlReportInterface
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get headers, if they were set for this SQL report. Otherwise,
     * return null
     *
     * @return array|null
     */
    public function getHeadersArray() : ?array
    {
        if (trim($this->getHeaders())) {
            return explode(",", $this->getHeaders());
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders() : string
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(string $headers) : SqlReportInterface
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName(string $exportType) : string
    {
        return $this->getTitle().".".self::$FILE_EXTENSIONS[$exportType];
    }

    /**
     * {@inheritdoc}
     */
    public function getProjection() : string
    {
        return $this->projection;
    }

    /**
     * {@inheritdoc}
     */
    public function setProjection(string $projection) : SqlReportInterface
    {
        $this->projection = $projection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(int $type) : SqlReportInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReportGroup() : string
    {
        return $this->reportGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function setReportGroup(string $reportGroup) : SqlReportInterface
    {
        $this->reportGroup = $reportGroup;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortBy() : int
    {
        return $this->sortBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortBy(int $sortBy) : SqlReportInterface
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActive() : bool
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive(bool $active) : SqlReportInterface
    {
        $this->active = $active;

        return $this;
    }
}