<?php

namespace Profounder\Augment;

use Profounder\Entity\Toc;
use Profounder\Entity\Article;
use Profounder\Exception\InvalidArgument;

class Augmentor
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
     * Augments an article by ID from an ArticlePage instance.
     *
     * @param  string $articleId Article content ID.
     * @param  ArticlePage $articlePage
     *
     * @return bool
     *
     * @throws InvalidArgument
     * @throws \RuntimeException
     */
    public function augment($articleId, ArticlePage $articlePage)
    {
        if ($article = $this->articleRepo->whereContentId($articleId)->first()) {
            if ($this->updateArticle($article, $articlePage)) {
                return $this->syncTocItems($article, $articlePage);
            }

            throw new \RuntimeException("Could not augment the article: $articleId");
        }

        throw InvalidArgument::notFound("Article could not be found: $articleId");
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
        return $article->fill($articlePage->toArray())->save();
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
            $article->toc()->delete();

            // Then, associate new toc items under a parent wrapper node
            $articlePage->toc = $this->associateTocItemsWithArticle($articlePage->toc, $article->id);

            return (bool) $this->tocRepo->create([
                'article_id' => $article->id,
                'children'   => $articlePage->toc,
            ]);
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
