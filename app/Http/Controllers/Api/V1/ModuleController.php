<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;
use App\Repositories\ModuleRepository;
use App\Http\Requests\CreateModuleRequest;
use App\Modules\Messages\Module as ModuleMessages;
use App\Constants\StatusCodes;
use App\Helper\CommonHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\FilterModuleRequest;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    public function __construct(private ModuleRepository $moduleRepo) {}

    public function createModule(CreateModuleRequest $request)
    {
        try {
            $validated = $request->validated();
            $module = $this->moduleRepo->create($validated);
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
            $modules = $this->moduleRepo->getModules($validated);
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
            $module = $this->moduleRepo->findById($validated['id']);
            $success = false;
            $message = ModuleMessages::$moduleNotDeleted;
            $statusCode = StatusCodes::HTTP_NOT_FOUND;
            $data['module'] = null;
            if ($module) {
                $module->delete();
                $success = true;
                $message = ModuleMessages::$moduleDeleted;
                $statusCode = StatusCodes::HTTP_OK;
                $data['module'] = $module;
            }
            
            return ApiResponse::sendResponse($success, $statusCode, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

}
