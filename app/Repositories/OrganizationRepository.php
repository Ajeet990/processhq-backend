<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\OrganizationRepositoryInterface;
use App\Constants\AppConstants;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    protected  $model;

    public function __construct()
    {
        $this->model = new Organization();
    }

    public function getAll($data): LengthAwarePaginator
    {
        $limit = AppConstants::DEFAULT_PER_PAGE;
        $query = $this->model->query();
        if (!empty($data['search'])) {
            $query->where('name', 'like', '%' . $data['search'] . '%');
        }
        if (array_key_exists('status', $data)) {
            $query->where('status', $data['status']);
        }

        return $query->orderBy('updated_at', 'desc')->paginate($limit);
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
