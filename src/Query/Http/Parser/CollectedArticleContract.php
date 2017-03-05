<?php

namespace Profounder\Query\Http\Parser;

use Profounder\Foundation\Http\Parser\ParsedObjectContract;

interface CollectedArticleContract extends ParsedObjectContract
{
    /**
     * Unsets the publisher property.
     *
     * @return $this
     */
    public function withoutPublisher();

    /**
     * Getter for the price property.
     *
     * @return int
     */
    public function getPrice();

    /**
     * Getter for the sku property.
     *
     * @return string
     */
    public function getSku();

    /**
     * Getter for the date property.
     *
     * @return string
     */
    public function getDate();

    /**
     * Getter for the title property.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Getter for the publisher property.
     *
     * @return string
     */
    public function getPublisher();

    /**
     * Getter for the content_id property.
     *
     * @return string
     */
    public function getContentId();

    /**
     * Getter for the internal_id property.
     *
     * @return string
     */
    public function getInternalId();
}
