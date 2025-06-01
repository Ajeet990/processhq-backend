<?php
namespace App\Interfaces;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrganizationRepositoryInterface
{
    public function create(array $data): Organization;
    public function findById(int $id): ?Organization;
    public function getAll($data): LengthAwarePaginator;

}