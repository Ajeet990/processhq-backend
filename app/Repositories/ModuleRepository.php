<?php

namespace App\Repositories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Constants\AppConstants;

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

    public function getModules($validated) : ?LengthAwarePaginator
    {
        $limit = AppConstants::DEFAULT_PER_PAGE;
        // $limit = 2;
        $query = $this->model->query();
        if (isset($validated['search'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['search'] . '%')
                  ->orWhere('description', 'like', '%' . $validated['search'] . '%')
                  ->orWhere('slug', 'like', '%' . $validated['search'] . '%');
            });
        }
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }
        return $query->orderBy('updated_at', 'DESC')->paginate($limit);
    }
}