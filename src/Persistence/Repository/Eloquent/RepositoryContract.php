<?php
namespace Profounder\Persistence\Repository\Eloquent;

use Profounder\Persistence\Entity\Eloquent\EntityContract;
use Profounder\Persistence\Repository\RepositoryContract as BaseRepositoryContract;

interface RepositoryContract extends BaseRepositoryContract
{
    /**
     * Returns underlying eloquent implementation of business entity's FQN.
     *
     * @return string
     */
    public function entity();

    /**
     * Persists entity data.
     *
     * @param  array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes = []);

    /**
     * Persists mutltiple entitys data.
     *
     * @param  array $records
     *
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function createMany(array $records = []);

    /**
     * Update the entity in the database.
     *
     * @param  array $attributes
     * @param  array $options
     *
     * @return bool
     */
    public function update(array $attributes = [], array $options = []);

    /**
     * Creates an entity only if not exists.
     *
     * @param  array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function existsOrCreate(array $attributes);

    /**
     * Deletes entity from database.
     *
     * @param  int $id
     *
     * @return bool|null
     */
    public function delete($id);

    /**
     * Finds entity by its primary ID.
     *
     * @param  int $id
     * @param  array $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function find($id, $columns = ['*']);

    /**
     * Finds entity by passed conditions.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  array $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function findBy($attribute, $value, $columns = ['*']);

    /**
     * Returns all entries from the underlying entity.
     *
     * @param  array $columns Array of column names to retrieve.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all($columns = ['*']);

    /**
     * Entity instance getter.
     *
     * @return EntityContract
     */
    public function getEntity();
}
