<?php

namespace App\Repositories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ModuleRepository
{
    protected Module $model;

    public function __construct()
    {
        $this->model = new Module();
    }

    public function create(array $data): Module
    {
        return $this->model->create($data);
    }
    public function update(int $id, array $data): bool
    {
        $module = $this->model->find($id);
        if ($module) {
            return $module->update($data);
        }
        return false;
    }

    // 1. Basic CRUD
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Module
    {
        return $this->model->find($id);
    }
}