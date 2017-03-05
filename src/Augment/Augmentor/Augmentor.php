<?php

namespace Profounder\Augment\Augmentor;

use Profounder\Exception\InvalidArgument;
use Profounder\Augment\Http\Parser\ArticlePageContract;
use Profounder\Persistence\Entity\ArticleContract;
use Profounder\Persistence\Repository\TocRepositoryContract;
use Profounder\Persistence\Repository\ArticleRepositoryContract;

class Augmentor implements AugmentorContract
{
    /**
     * Toc repository instance.
     *
     * @var TocRepositoryContract
     */
    private $tocRepository;

    /**
     * Article repository instance.
     *
     * @var ArticleRepositoryContract
     */
    private $articleRepository;

    /**
     * Augmentor constructor.
     *
     * @param  TocRepositoryContract $tocRepository
     * @param  ArticleRepositoryContract $articleRepository
     */
    public function __construct(TocRepositoryContract $tocRepository, ArticleRepositoryContract $articleRepository)
    {
        $this->tocRepository     = $tocRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @inheritdoc
     */
    public function augment($articleContentId, ArticlePageContract $articlePage)
    {
        if ($article = $this->getArticle($articleContentId)) {
            if ($this->updateArticle($article, $articlePage)) {
                return $this->syncTocItems($article, $articlePage);
            }

            throw new \RuntimeException("Could not augment the article: $articleContentId");
        }

        throw InvalidArgument::notFound("Article could not be found: $articleContentId");
    }

    /**
     * Returns article from the database by its content ID.
     *
     * @param  string $articleId
     *
     * @return ArticleContract
     */
    private function getArticle($articleId)
    {
        return $this->articleRepository->findByContentId($articleId);
    }

    /**
     * Updates article entity with values from ArticlePage instance.
     *
     * @param  ArticleContract $article
     * @param  ArticlePageContract $articlePage
     *
     * @return bool
     */
    private function updateArticle(ArticleContract $article, ArticlePageContract $articlePage)
    {
        return $article->fillAndSave($articlePage->toArray());
    }

    /**
     * Creates article corresponding TOC entities.
     *
     * @param  ArticleContract $article
     * @param  ArticlePageContract $articlePage
     *
     * @return bool
     */
    private function syncTocItems(ArticleContract $article, ArticlePageContract $articlePage)
    {
        if ($toc = $articlePage->getToc()) {
            // First, delete any existing toc items
            $article->deleteToc();

            // Then, associate new toc items under a parent wrapper node
            $articlePage->toc(
                $this->associateTocItemsWithArticle($toc, $article->getId())
            );

            // We need to call the create() method on the Toc entity,
            // as it implements nested sets via traits. If we do call
            // the create() method on the relationship, nested sets
            // won't be considered.

            // This createArticleToc() method is just a wrapper around
            // the create().
            return $this->tocRepository->createArticleToc($articlePage->getToc(), $article->getId());
        }

        return true;
    }

    /**
     * Associates a TOC item with an article ID.
     *
     * @param  array $toc
     * @param  int $articleId
     *
     * @return array
     */
    private function associateTocItemsWithArticle(array $toc, $articleId)
    {
        foreach ($toc as &$item) {
            if (empty($item['article_id'])) {
                $item['article_id'] = $articleId;
            }

            if (! empty($item['children'])) {
                $item['children'] = $this->associateTocItemsWithArticle($item['children'], $articleId);
            }
        }

        return $toc;
    }
}
