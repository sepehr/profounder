<?php

namespace Profounder\Persistence\Repository\Eloquent;

use Profounder\Persistence\Entity\Eloquent\Publisher;
use Profounder\Persistence\Repository\PublisherRepositoryContract;

class PublisherRepository extends Repository implements PublisherRepositoryContract
{
    /**
     * @inheritdoc
     */
    public function entity()
    {
        return Publisher::class;
    }
}
