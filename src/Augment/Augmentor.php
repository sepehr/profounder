<?php

namespace Profounder\Augment;

use Profounder\Entity\Toc;
use Profounder\Entity\Article;
use Profounder\Exception\InvalidArgument;

class Augmentor implements AugmentorContract
{
    /**
     * Toc repository instance.
     *
     * @var Article
     */
    private $tocRepo;

    /**
     * Article repository instance.
     *
     * @var Article
     */
    private $articleRepo;

    /**
     * Augmentor constructor.
     *
     * @param  Toc $tocRepo
     * @param  Article $articleRepo
     */
    public function __construct(Toc $tocRepo, Article $articleRepo)
    {
        $this->tocRepo     = $tocRepo;
        $this->articleRepo = $articleRepo;
    }

    /**
     * @inheritdoc
     */
    public function augment($articleId, ArticlePage $articlePage)
    {
        if ($article = $this->getArticle($articleId)) {
            if ($this->updateArticle($article, $articlePage)) {
                return $this->syncTocItems($article, $articlePage);
            }

            throw new \RuntimeException("Could not augment the article: $articleId");
        }

        throw InvalidArgument::notFound("Article could not be found: $articleId");
    }

    /**
     * Returns article from the database by its content ID.
     *
     * @param  string $articleId
     *
     * @return Article
     */
    private function getArticle($articleId)
    {
        return $this->articleRepo->findByContentId($articleId);
    }

    /**
     * Updates article entity with values from ArticlePage instance.
     *
     * @param  Article $article
     * @param  ArticlePage $articlePage
     *
     * @return bool
     */
    private function updateArticle(Article $article, ArticlePage $articlePage)
    {
        return $article->fillAndSave($articlePage->toArray());
    }

    /**
     * Creates article corresponding TOC entities.
     *
     * @param  Article $article
     * @param  ArticlePage $articlePage
     *
     * @return bool
     */
    private function syncTocItems(Article $article, ArticlePage $articlePage)
    {
        if ($articlePage->toc) {
            // First, delete any existing toc items
            $article->deleteToc();

            // Then, associate new toc items under a parent wrapper node
            $articlePage->toc = $this->associateTocItemsWithArticle($articlePage->toc, $article->id);

            // We need to call the create() method on the Toc entity,
            // as it implements nested sets via traits. If we do call
            // the create() method on the relationship, nested sets
            // won't be considered.
            // This createArticleToc() method is just a wrapper around
            // the create().
            return $this->tocRepo->createArticleToc($articlePage->toc, $article->id);
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
