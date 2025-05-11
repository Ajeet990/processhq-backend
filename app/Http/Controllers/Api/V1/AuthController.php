<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use App\Constants\AppConstants;
use App\Modules\User\Messages as UserMessages;
use App\Constants\StatusCodes;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Throwable;
use App\Http\Requests\LoginRequest;


class AuthController extends Controller
{
    public function __construct(private UserRepository $userRepo)
    {

    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
    
            // Registration logic here
    
            return response()->json(['message' => 'User registered successfully'], 200);
        } catch (Throwable $e) {
            $this->logError($e, $request);
            // return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
            return ApiResponse::sendError(false,StatusCodes::HTTP_INTERNAL_SERVER_ERROR,$e->getMessage(),null);
        }
        
    }

    // public function login(Request $request, LoginRequest $loginRequest)
    public function login(LoginRequest $request)
    {
        try {

            if (!Auth::attempt($request->only('email', 'password'))) {
                $message = UserMessages::$invalidCredentials;
                return ApiResponse::sendError(false, StatusCodes::HTTP_UNAUTHORIZED, $message, null);
            }
    
            // Generate the token
            $user = Auth::user();
            $token = $user->createToken('userToken')->accessToken;

            $success = true;
            $message = UserMessages::$loggedIn;
            $data['token'] = $token;
            $data['user'] = $user;
            return ApiResponse::sendResponse($success, StatusCodes::HTTP_OK, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e, $request);
            return ApiResponse::sendError(false, StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), null);
        } 
        
    }
    public function logout()
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete();
    
            // return response()->json(['message' => 'User logged out successfully'], 200);
            $success = true;
            $message = UserMessages::$loggedOut;
            $code = StatusCodes::HTTP_OK;
            $data['data'] = true;
            return ApiResponse::sendResponse($success, $code, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e, request());
            return ApiResponse::sendError(false,StatusCodes::HTTP_INTERNAL_SERVER_ERROR,$e->getMessage(),null);
        }

    }
    public function getAllUsers()
    {
        try {
            $users = $this->userRepo->getAll();
            $data['userList'] = $users;
            // $formatedData = UserService::formatedUserData($users);

            $success = true;
            $message = UserMessages::$userList;
            $code = StatusCodes::HTTP_OK;
            return ApiResponse::sendResponse($success, $code, $message, $data);
        } catch (Throwable $e) {
            $this->logError($e, request());
            return ApiResponse::sendError(false,StatusCodes::HTTP_INTERNAL_SERVER_ERROR,$e->getMessage(),null);
        }

    }
}
