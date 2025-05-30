<?php

namespace App\Services;

use App\Repositories\ModuleRepository;
use App\Interfaces\ModuleRepositoryInterface;
use App\Models\Module;

class ModuleService
{
    // public function __construct(private ModuleRepository $moduleRepo) {}
    public function __construct(
        private ModuleRepositoryInterface $moduleRepo
    ) {}

    public function create($data)
    {
        return $this->moduleRepo->create($data);
    }

    public function getModules($data)
    {
        return $this->moduleRepo->getModules($data);
    }

    public function findById(int $id): ?Module
    {
        return $this->moduleRepo->findById($id);
    }

    public function deleteModule(int $id): array
    {
        $module = $this->findById($id);
        if (!$module) {
            return ['success' => false, 'message' => 'Module not found', 'module_id' => $id];
        }

        $this->moduleRepo->deleteModule($id);
        return ['success' => true, 'message' => 'Module deleted', 'module_id' => $id];
    }

    public function toggleStatus(Module $module): bool
    {
        $status = $module->status === '1' ? '0' : '1';
        return $this->moduleRepo->toggleStatus($module->id, $status);
    }

    public function updateModule(int $id, array $data): bool
    {
        return $this->moduleRepo->updateModule($id, $data);
    }

}
