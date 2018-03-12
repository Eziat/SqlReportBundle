<?php
declare(strict_types=1);

namespace Eziat\SqlReportBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Tomas
 */
abstract class SqlReport implements SqlReportInterface
{
    const SQL = 0;
    const DQL = 1;

    public static $EXPORT_TYPES    = [
        'CSV'  => 'Csv',
        'Excel5'  => 'Xls',
        'Excel2007' => 'Xlsx',
        'ODS'  => 'Ods',
        'HTML' => 'Html',
        'PDF'  => 'Tcpdf',
    ];
    public static $FILE_EXTENSIONS = [
        'CSV'  => 'csv',
        'Excel5'  => 'xls',
        'Excel2007' => 'xlsx',
        'ODS'  => 'ods',
        'HTML' => 'html',
        'PDF'  => 'pdf',
    ];
    public static $CONTENT_TYPES   = [
        'CSV'  => 'text/csv',
        'ODS'  => "ods",
        'Excel5'  => "text/vnd.ms-excel",
        'Excel2007' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        'HTML' => 'text/html',
        'PDF'  => 'application/pdf',
    ];
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var string
     */
    protected $description = '';
    /**
     * @var string
     */
    protected $query = '';
    /**
     * @var string
     */
    protected $headers = '';
    /**
     * @var string
     */
    protected $projection = '';
    /**
     * @var integer
     */
    protected $type = 0;
    /**
     * @var string
     */
    protected $reportGroup = '';
    /**
     * @var integer
     */
    protected $sortBy = 0;
    /**
     * @var boolean
     */
    protected $active;

    public function __construct()
    {
        $this->active = true;
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