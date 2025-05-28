<?php
namespace App\Interfaces;
use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ModuleRepositoryInterface
{
    public function create(array $data): Module;
    public function findById(int $id): ?Module;
    public function deleteModule(int $id): bool;
    public function getModules(array $filters): LengthAwarePaginator;
}

