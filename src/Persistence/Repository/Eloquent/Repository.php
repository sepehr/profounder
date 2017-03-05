<?php

namespace Profounder\Persistence\Repository\Eloquent;

use Illuminate\Database\Query\Builder;
use Profounder\Core\Console\Container;
use Profounder\Persistence\Entity\Eloquent\EntityContract;
use Profounder\Persistence\Repository\Repository as BaseRepository;

abstract class Repository extends BaseRepository implements RepositoryContract
{
    /**
     * Holds entity instance.
     *
     * @var EntityContract
     */
    protected $entity;

    /**
     * Holds instance of Container.
     *
     * @var Container
     */
    private $container;

    /**
     * @inheritdoc
     */
    abstract public function entity();

    /**
     * Repository constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->createEntity();
    }

    /**
     * Reroute calls to the underlying entity's query builder.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (method_exists(Builder::class, $method)) {
            return $this->entity->$method(...$args);
        }
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes = [])
    {
        return $this->entity->create($attributes);
    }

    /**
     * @inheritdoc
     */
    public function createMany(array $records = [])
    {
        $created = [];
        foreach ($records as $record) {
            $created[] = $this->create($record);
        }

        return $created;
    }

    /**
     * @inheritdoc
     */
    public function update(array $attributes = [], array $options = [])
    {
        return $this->entity->update($attributes, $options);
    }

    /**
     * @inheritdoc
     */
    public function existsOrCreate(array $attributes)
    {
        return $this->entity->existsOrCreate($attributes);
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        return $this->entity->destroy($id);
    }

    /**
     * @inheritdoc
     */
    public function find($id, $columns = ['*']) {
        return $this->entity->find($id, $columns);
    }

    /**
     * @inheritdoc
     */
    public function findBy($attribute, $value, $columns = ['*']) {
        return $this->entity->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @inheritdoc
     */
    public function all($columns = ['*'])
    {
        return $this->entity->all($columns);
    }

    /**
     * @inheritdoc
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Creates and sets the instance of underlying entity.
     */
    private function createEntity()
    {
        $this->entity = $this->container->make($this->entity());
    }
}
