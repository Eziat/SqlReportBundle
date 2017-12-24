<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\Entity;

/**
 * @author Tomas
 */
interface SqlReportInterface
{
    /**
     * Get id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() : string;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SqlReport
     */
    public function setTitle(string $title) : self;

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() : string;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SqlReport
     */
    public function setDescription(string $description) : self;

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery() : string;

    /**
     * Set query
     *
     * @param string $query
     *
     * @return SqlReport
     */
    public function setQuery(string $query) : self;

    /**
     * Get headers
     *
     * @return string
     */
    public function getHeaders() : string;

    /**
     * Set headers
     *
     * @param string $headers
     *
     * @return SqlReport
     */
    public function setHeaders(string $headers) : self;

    /**
     * Get projection
     *
     * @return string
     */
    public function getProjection() : string;

    /**
     * Set projection
     *
     * @param string $projection
     *
     * @return SqlReport
     */
    public function setProjection(string $projection) : self;

    /**
     * Get type
     *
     * @return integer
     */
    public function getType() : int;

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return SqlReport
     */
    public function setType(int $type) : self;

    /**
     * Get reportGroup
     *
     * @return string
     */
    public function getReportGroup() : string;

    /**
     * Set reportGroup
     *
     * @param string $reportGroup
     *
     * @return SqlReport
     */
    public function setReportGroup(string $reportGroup) : self;

    /**
     * Get sortBy
     *
     * @return integer
     */
    public function getSortBy() : int;

    /**
     * Set sortBy
     *
     * @param integer $sortBy
     *
     * @return SqlReport
     */
    public function setSortBy(int $sortBy) : self;

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive() : bool;

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return SqlReport
     */
    public function setActive(bool $active) : self;

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime;

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SqlReport
     */
    public function setCreatedAt(\DateTime $createdAt) : self;
}