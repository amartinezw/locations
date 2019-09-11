<?php

namespace App\Repositories;

class BaseRepository
{
    public function select(array $columns = ['*'])
    {
        return (new $this->model)->select($columns);
    }

    public function get($columns = ['*'])
    {
        return (new $this->model)->get($columns);
    }

    public function getOrdered($field, $order = 'ASC', $columns = ['*'])
    {
        return (new $this->model)->orderBy($field, $order)->get($columns);
    }

    public function all($columns = ['*'])
    {
        return (new $this->model)->all($columns);
    }

    public function count()
    {
        return (new $this->model)->count();
    }

    public function find($id)
    {
        return (new $this->model)->find($id);
    }

    public function findOrFail($id)
    {
        return (new $this->model)->findOrFail($id);
    }

    public function create(array $data)
    {
        return (new $this->model)->create($data);
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function update($model, array $data = [])
    {
        return $model->update($data);
    }

    public function save($model)
    {
        return $model->save();
    }

    public function getRules()
    {
        $model = new $this->model;

        return $model::$rules;
    }

    public function getUpdateRules()
    {
        $model = new $this->model;

        return $model::$updateRules;
    }
}
