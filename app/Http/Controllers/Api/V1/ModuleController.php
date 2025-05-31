<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;
use App\Repositories\ModuleRepository;
use App\Http\Requests\CreateModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Modules\Messages\Module as ModuleMessages;
use App\Constants\StatusCodes;
use App\Helper\CommonHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\FilterModuleRequest;
use Illuminate\Support\Facades\Validator;
use App\Services\ModuleService;
use App\Models\Module;

class ModuleController extends Controller
{
    public function __construct(private ModuleService $moduleService) {}

    public function createModule(CreateModuleRequest $request)
    {
        try {
            $validated = $request->validated();
            $module = $this->moduleService->create($validated);
            $success = false;
            $message = ModuleMessages::$moduleNotCreated;
            $statusCode = StatusCodes::HTTP_INTERNAL_SERVER_ERROR;
            $data['module'] = null;
            if ($module) {
                $success = true;
                $message = ModuleMessages::$moduleCreated;
                $statusCode = StatusCodes::HTTP_OK;
                $data['module'] = $module;
            }
            return ApiResponse::sendResponse($success, $statusCode, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e, $request);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

    public function getModules(FilterModuleRequest $request)
    {
        try {
            $validated = $request->validated();
            $modules = $this->moduleService->getModules($validated);
            $pagination = CommonHelper::getPaginationData($modules);
            $data['modules'] = $modules->items();
            $data['pagination'] = $pagination;
            $success = true;
            $message = ModuleMessages::$moduleList;
            $statusCode = StatusCodes::HTTP_OK;
            return ApiResponse::sendResponse($success, $statusCode, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

    public function deleteModule($id)
    {
        try {
            $validated = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:modules,id',
            ]);
            if ($validated->fails()) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_BAD_REQUEST, $validated->errors()->first(), null);
            }
            $validated = $validated->validated();
            $moduleRst = $this->moduleService->deleteModule($validated['id']);
            $success = false;
            $message = ModuleMessages::$moduleNotDeleted;
            $statusCode = StatusCodes::HTTP_NOT_FOUND;
            $data['module'] = null;
            if ($moduleRst['success']) {
                $success = true;
                $message = ModuleMessages::$moduleDeleted;
                $statusCode = StatusCodes::HTTP_OK;
                $data['module'] = $moduleRst['module_id'];
            }
            
            return ApiResponse::sendResponse($success, $statusCode, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

    public function toggleModuleStatus($id)
    {
        try {
            $validated = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:modules,id',
            ]);
            if ($validated->fails()) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_BAD_REQUEST, $validated->errors()->first(), null);
            }
            $validated = $validated->validated();
            $module = $this->moduleService->findById($validated['id']);
            if (!$module) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_NOT_FOUND, ModuleMessages::$moduleNotFound, null);
            }
            // $module->status = !$module->status;
            // $module->save();
            $toggleStatus = $this->moduleService->toggleStatus($module);
            if (!$toggleStatus) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, ModuleMessages::$moduleStatusNotUpdated, null);
            }
            $success = true;
            $message = ModuleMessages::$moduleStatusUpdated;
            $statusCode = StatusCodes::HTTP_OK;
            $data['module'] = $module;
            return ApiResponse::sendResponse($success, $statusCode, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

    public function getModuleById($id)
    {
        try {
            $validated = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:modules,id',
            ]);
            if ($validated->fails()) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_BAD_REQUEST, $validated->errors()->first(), null);
            }
            $validated = $validated->validated();
            $module = $this->moduleService->findById($validated['id']);
            if (!$module) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_NOT_FOUND, ModuleMessages::$moduleNotFound, null);
            }
            $success = true;
            $message = ModuleMessages::$moduleFound;
            $statusCode = StatusCodes::HTTP_OK;
            $data['module'] = $module;
            return ApiResponse::sendResponse($success, $statusCode, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

    public function updateModule(UpdateModuleRequest $request, $id)
    {
        try {
            // Validate ID exists in modules table
            if (!is_numeric($id) || !Module::where('id', $id)->exists()) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_NOT_FOUND, ModuleMessages::$invalidModuleId, null);
            }

            $validated = $request->validated();
            $module = $this->moduleService->findById($id);
            if (!$module) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_NOT_FOUND, ModuleMessages::$moduleNotFound, null);
            }
            $updateData = $request->only(['name', 'description', 'status']);
            $updatedModule = $this->moduleService->updateModule($id, $updateData);
            if (!$updatedModule) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, ModuleMessages::$moduleNotUpdated, null);
            }
            $success = true;
            $message = ModuleMessages::$moduleUpdated;
            $statusCode = StatusCodes::HTTP_OK;
            $data['module'] = $updatedModule;
            return ApiResponse::sendResponse($success, $statusCode, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

}
