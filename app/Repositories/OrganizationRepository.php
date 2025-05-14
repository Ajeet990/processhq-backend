<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationRepository
{
    protected  $model;

    public function __construct()
    {
        $this->model = new Organization();
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Organization
    {
        return $this->model->find($id);
    }

    public function create(array $data): Organization
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $this->model->create($data);
    }
}
