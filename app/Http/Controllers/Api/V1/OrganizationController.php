<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrganizationRequest;
use App\Repositories\UserRepository;
use App\Repositories\OrganizationRepository;
use App\Modules\Messages\Organization as OrganizationMessages;
use App\Constants\StatusCodes;
use App\Constants\AppConstants;
use App\Http\Responses\ApiResponse;
use Throwable;
use App\Services\OrganizationService;
use App\Helper\CommonHelper;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    // public function __construct(private OrganizationRepository $orgRepo) {}
    public function __construct(private OrganizationService $orgService) {}

    public function createOrganization(CreateOrganizationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $success = false;
            $message = OrganizationMessages::$organizationNotCreated;
            $statusCode = StatusCodes::HTTP_INTERNAL_SERVER_ERROR;
            $data = null;
            $createdOrganization = $this->orgService->create($validatedData);
            if ($createdOrganization) {
                $success = true;
                $message = OrganizationMessages::$organizationCreated;
                $statusCode = StatusCodes::HTTP_OK;
                $data = $createdOrganization;
                return ApiResponse::sendResponse($success, $statusCode, $message, $data);
            } else {
                return ApiResponse::sendError($success, $statusCode, $message, $data);
            }
        } catch (Throwable $e) {
            $this->logError($e, $request);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

    public function getOrganization(Request $request, $id = null)
    {
        try {
            $success = false;
            $message = OrganizationMessages::$noOrgDataFound;
            $statusCode = StatusCodes::HTTP_NOT_FOUND;
            $data = null;
            $validator = Validator::make($request->all(), [
                'id' => 'nullable|integer|exists:organizations,id',
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], StatusCodes::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Get validated data
            $validatedData = $validator->validated();
            // dd("sss", $validatedData);
            if ($id) {
                $orgData = $this->orgService->findById($id);

                if ($orgData) {
                    $success = true;
                    $message = OrganizationMessages::$orgDataFound;
                    $statusCode = StatusCodes::HTTP_OK;
                    $data = $orgData;
                }
            } else {
                $orgData = $this->orgService->getAll($validatedData);
                $pagination = CommonHelper::getPaginationData($orgData);
                $data['organizations'] = $orgData->items();
                $data['pagination'] = $pagination;

                if (!empty($orgData)) {
                    $success = true;
                    $message = OrganizationMessages::$orgDataFound;
                    $statusCode = StatusCodes::HTTP_OK;
                    // $data = $data;
                }
            }

            if ($success) {
                return ApiResponse::sendResponse($success, $statusCode, $message, $data);
            } else {
                return ApiResponse::sendError($success, $statusCode, $message, $data);
            }
        } catch (Throwable $e) {
            $this->logError($e, $request);
            return ApiResponse::sendError(
                false,
                StatusCodes::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage(),
                null
            );
        }
    }

    public function deleteOrganization(Request $request, $id = null)
    {
        try {
            $data = null;

            if (null === $id) {

                $message = OrganizationMessages::$orgIdRequired;

                return ApiResponse::sendError(false, StatusCodes::HTTP_BAD_REQUEST, $message, $data);
            }

            $organization = $this->orgService->findById($id);

            if (!$organization) {

                return ApiResponse::sendError(false, StatusCodes::HTTP_NOT_FOUND, OrganizationMessages::$orgDataFound, $data);
            }

            $deletion = $organization->delete();

            if (!$deletion) {
                return ApiResponse::sendError(false, StatusCodes::HTTP_CONFLICT, OrganizationMessages::$organizationNotDeleted, $data);
            }

            return ApiResponse::sendResponse(true, StatusCodes::HTTP_OK, OrganizationMessages::$orgDeleteSuccess, $data);
        } catch (Throwable $e) {
            $this->logError($e, $request);
        }
    }
}
