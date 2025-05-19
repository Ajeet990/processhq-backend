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

class OrganizationController extends Controller
{
    public function __construct(private OrganizationRepository $orgRepo) {}
    public function createOrganization(CreateOrganizationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            // dd($validatedData);
            $success = false;
            $message = OrganizationMessages::$organizationNotCreated;
            $statusCode = StatusCodes::HTTP_INTERNAL_SERVER_ERROR;
            $data = null;
            $createdOrganization = $this->orgRepo->create($validatedData);
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

    public function getOrganization(Request $request)
    {
        try {
            // $validatedData = $request->validated();
            $success = false;
            $message = OrganizationMessages::$noOrgDataFound;
            $statusCode = StatusCodes::HTTP_INTERNAL_SERVER_ERROR;
            $orgData = $this->orgRepo->getAll();
            $data = null;
            if (!empty($orgData)) {
                $success = true;
                $message = OrganizationMessages::$orgDataFound;
                $statusCode = StatusCodes::HTTP_OK;
                $data = $orgData;

                return ApiResponse::sendResponse($success, $statusCode, $message, $data);
            } else {

                return ApiResponse::sendError($success, $statusCode, $message, $data);
            }
        } catch (Throwable $e) {
            $this->logError($e, $request);

            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        }
    }

    public function deleteOrganization(Request $request)
    {
        try {
            $data = null;

            if (!$request->has('id')) {

                $message = OrganizationMessages::$orgIdRequired;

                return ApiResponse::sendError(false, $statusCode, StatusCodes::HTTP_BAD_REQUEST, $data);
            }

            $id = $request->input('id');
            $organization = $this->orgRepo->findById($id);

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
