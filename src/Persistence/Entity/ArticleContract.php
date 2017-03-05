<?php

namespace Profounder\Persistence\Entity;

interface ArticleContract extends EntityContract
{
    /**
     * Getter for article ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Getter for article content ID.
     *
     * @return int
     */
    public function getContentId();

    /**
     * Getter for article SKU.
     *
     * @return string
     */
    public function getSku();

    /**
     * Deletes associated Toc entities.
     *
     * @return bool
     */
    public function deleteToc();
}
