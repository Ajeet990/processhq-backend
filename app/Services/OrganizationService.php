<?php

namespace App\Services;

use App\Interfaces\OrganizationRepositoryInterface;
// use App\Models\Module;
use App\Models\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrganizationService
{
    // public function __construct(private OrganizationRepository $orgRepo) {}
    public function __construct(
        private OrganizationRepositoryInterface $orgRepo
    ) {}

    public function create(array $data): Organization
    {
        return $this->orgRepo->create($data);
    }
    public function findById(int $id): ?Organization
    {
        return $this->orgRepo->findById($id);
    }
    public function getAll($data): LengthAwarePaginator
    {
        return $this->orgRepo->getAll($data);
    }

}