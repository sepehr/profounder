<?php

namespace Profounder\Query\Storer;

use Illuminate\Support\Collection;
use Profounder\Persistence\Entity\PublisherContract;
use Profounder\Query\Http\Parser\CollectedArticleContract;
use Profounder\Persistence\Repository\ArticleRepositoryContract;
use Profounder\Persistence\Repository\PublisherRepositoryContract;

class Storer implements StorerContract
{
    /**
     * Article repository instance.
     *
     * @var ArticleRepositoryContract
     */
    private $articleRepository;

    /**
     * Publisher repository instance.
     *
     * @var PublisherRepositoryContract
     */
    private $publisherRepository;

    /**
     * Storer constructor.
     *
     * @param  ArticleRepositoryContract  $articleRepo
     * @param  PublisherRepositoryContract  $publisherRepo
     */
    public function __construct(ArticleRepositoryContract $articleRepo, PublisherRepositoryContract $publisherRepo)
    {
        $this->articleRepository   = $articleRepo;
        $this->publisherRepository = $publisherRepo;
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
        $articles->each(function (CollectedArticleContract $article) use (&$count) {
            $this->storeIfNotExists($article) and $count++;
        });

        return $count;
    }

    /**
     * Normalizes and stores the collected article into database, if not exists.
     *
     * @param  CollectedArticleContract $article
     *
     * @return bool
     */
    private function storeIfNotExists(CollectedArticleContract $article)
    {
        return $this->articleExists($article)
            ? false
            : $this->normalizeAndStore($article);
    }

    /**
     * Checks if the article exists in the database or not.
     *
     * @param  CollectedArticleContract $article
     *
     * @return bool
     */
    private function articleExists(CollectedArticleContract $article)
    {
        return $this->articleRepository->existsByContentId($article->getContentId());
    }

    /**
     * Normalizes the CollectedArticle object into Article/Publisher entities and stores them.
     *
     * @param  CollectedArticleContract $article
     *
     * @return bool
     */
    private function normalizeAndStore(CollectedArticleContract $article)
    {
        $publisher = $this->fetchOrCreatePublisher($article);

        return $publisher
            ? $this->createPublisherArticle($publisher, $article)
            : false;
    }

    /**
     * Fetches publisher from the database or creates a new one.
     *
     * @param  CollectedArticleContract $collectedArticle
     *
     * @return PublisherContract
     */
    private function fetchOrCreatePublisher(CollectedArticleContract $collectedArticle)
    {
        return $this->publisherRepository->existsOrCreate(
            $this->preparePublisher($collectedArticle)
        );
    }

    /**
     * Creates an article associated with the passed publisher object.
     *
     * @param  PublisherContract $publisher
     * @param  CollectedArticleContract $collectedArticle
     *
     * @return \Profounder\Persistence\Entity\Eloquent\Article
     */
    private function createPublisherArticle(PublisherContract $publisher, CollectedArticleContract $collectedArticle)
    {
        return $publisher->createArticle(
            $this->prepareArticle($collectedArticle)
        );
    }

    /**
     * Prepares an article array for insertion.
     *
     * @param  CollectedArticleContract $collectedArticle
     *
     * @return array
     */
    private function prepareArticle(CollectedArticleContract $collectedArticle)
    {
        return $collectedArticle->withoutPublisher()->toArray();
    }

    /**
     * Prepares a publisher array for insertion.
     *
     * @param  CollectedArticleContract $collectedArticle
     *
     * @return array
     */
    private function preparePublisher(CollectedArticleContract $collectedArticle)
    {
        return ['name' => $collectedArticle->getPublisher()];
    }
}
