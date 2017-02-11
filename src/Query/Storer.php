<?php

namespace Profounder\Query;

use Profounder\Entity\Article;
use Profounder\Entity\Publisher;
use Illuminate\Support\Collection;

class Storer implements StorerContract
{
    /**
     * Article repository instance.
     *
     * @var Article
     */
    private $articleRepo;

    /**
     * Publisher repository instance.
     *
     * @var Publisher
     */
    private $publisherRepo;

    /**
     * Storer constructor.
     *
     * @param  Article $articleRepo
     * @param  Publisher $publisherRepo
     */
    public function __construct(Article $articleRepo, Publisher $publisherRepo)
    {
        $this->articleRepo   = $articleRepo;
        $this->publisherRepo = $publisherRepo;
    }

    /**
     * @inheritdoc
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * @inheritdoc
     */
    public function store(Collection $articles)
    {
        $count = 0;
        $articles->each(function (CollectedArticle $article) use (&$count) {
            $this->storeIfNotExists($article) and $count++;
        });

        return $count;
    }

    /**
     * Normalizes and stores the collected article into database, if not exists.
     *
     * @param  CollectedArticle $article
     *
     * @return bool
     */
    private function storeIfNotExists(CollectedArticle $article)
    {
        return $this->articleExists($article)
            ? false
            : $this->normalizeAndStore($article);
    }

    /**
     * Checks if the article exists in the database or not.
     *
     * @param  CollectedArticle $article
     *
     * @return bool
     */
    private function articleExists(CollectedArticle $article)
    {
        return $this->articleRepo->existsByContentId($article->content_id);
    }

    /**
     * Normalizes the CollectedArticle object into Article/Publisher entities and stores them.
     *
     * @param  CollectedArticle $article
     *
     * @return bool
     */
    private function normalizeAndStore(CollectedArticle $article)
    {
        $publisher = $this->fetchOrCreatePublisher($article);

        return $publisher
            ? $this->createPublisherArticle($publisher, $article)
            : false;
    }

    /**
     * Fetches publisher from the database or creates a new one.
     *
     * @param  CollectedArticle $collectedArticle
     *
     * @return Publisher
     */
    private function fetchOrCreatePublisher(CollectedArticle $collectedArticle)
    {
        return $this->publisherRepo->existsOrCreate(
            $this->preparePublisher($collectedArticle)
        );
    }

    /**
     * Creates an article associated with the passed publisher object.
     *
     * @param  Publisher $publisher
     * @param  CollectedArticle $collectedArticle
     *
     * @return Article
     */
    private function createPublisherArticle(Publisher $publisher, CollectedArticle $collectedArticle)
    {
        return $publisher->createArticle(
            $this->prepareArticle($collectedArticle)
        );
    }

    /**
     * Prepares an article array for insertion.
     *
     * @param  CollectedArticle $collectedArticle
     *
     * @return array
     */
    private function prepareArticle(CollectedArticle $collectedArticle)
    {
        unset($collectedArticle->publisher);

        return $collectedArticle->toArray();
    }

    /**
     * Prepares a publisher array for insertion.
     *
     * @param  CollectedArticle $collectedArticle
     *
     * @return array
     */
    private function preparePublisher(CollectedArticle $collectedArticle)
    {
        return ['name' => $collectedArticle->publisher];
    }
}
