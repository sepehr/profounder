<?php

namespace Profounder\Query;

use Profounder\Entity\Article;
use Profounder\Entity\Publisher;
use Illuminate\Support\Collection;

class Storer
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
     * Static factory method.
     *
     * @param  array $args
     *
     * @return Storer
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * Stores a collection of CollectedArticle objects into the database.
     *
     * @param  Collection $articles
     *
     * @return int Number of successful inserts.
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
     * @return mixed
     */
    private function articleExists(CollectedArticle $article)
    {
        return $this->articleRepo->whereContentId($article->content_id)->exists();
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
     * @param  CollectedArticle $article
     *
     * @return mixed
     */
    private function fetchOrCreatePublisher(CollectedArticle $article)
    {
        return $this->publisherRepo->firstOrCreate($this->preparePublisher($article));
    }

    /**
     * Creates an article associated with the passed publisher object.
     *
     * @param  Publisher $publisher
     * @param  CollectedArticle $article
     *
     * @return mixed
     */
    private function createPublisherArticle(Publisher $publisher, CollectedArticle $article)
    {
        return $publisher->articles()->create($this->prepareArticle($article));
    }

    /**
     * Prepares an article array for insertion.
     *
     * @param  CollectedArticle $article
     *
     * @return array
     */
    private function prepareArticle(CollectedArticle $article)
    {
        unset($article->publisher);

        return $article->toArray();
    }

    /**
     * Prepares a publisher array for insertion.
     *
     * @param  CollectedArticle $article
     *
     * @return array
     */
    private function preparePublisher(CollectedArticle $article)
    {
        return ['name' => $article->publisher];
    }
}
