<?php

namespace App\Repositories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Constants\AppConstants;
use App\Interfaces\ModuleRepositoryInterface;

class ModuleRepository implements ModuleRepositoryInterface
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

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Module
    {
        return $this->model->find($id);
    }

    public function deleteModule(int $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }

    public function getModules($validated) : LengthAwarePaginator
    {
        $limit = AppConstants::DEFAULT_PER_PAGE;
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

    public function toggleStatus(int $id, string $status): bool
    {
        return $this->model->where('id', $id)->update(['status' => $status]);
    }

    public function updateModule(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }
}