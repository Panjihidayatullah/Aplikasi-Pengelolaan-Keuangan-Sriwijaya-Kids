<?php

namespace App\Repositories;

class IncomeRepository
{
    protected $model;

    public function __construct()
    {
        // Model will be injected when Income model is created
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }
}
