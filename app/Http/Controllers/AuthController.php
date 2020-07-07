<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use JWTAuth;
use Response;
use \Illuminate\Http\Response as Res;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repository\Transformers\UserTransformer;
use App\Repository\Transformers\UserDataTransformer;
use Illuminate\Database\QueryException as QueryException;

class AuthController extends ApiController {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    protected $userTransformer;
    protected $userdataTransformer;

    public function __construct(userTransformer $userTransformer, userdataTransformer $userdataTransformer) {

        $this->userTransformer = $userTransformer;
        $this->userdataTransformer = $userdataTransformer;
        //$this->middleware('auth:api', ['except' => ['authenticate', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request) {

        $rules = array(
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        return $this->_login($request->input('email'), $request->input('password'));
    }

    /**
     * @description: Api user register method
     * @param: lastname, firstname, username, email, password
     * @return: Json String response
     */
    public function register(Request $request) {

        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:3'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {

            $user = User::create([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'password' => \Hash::make($request->input('password')),
            ]);

            return $this->_login($request->input('email'), $request->input('password'));
        }
    }

    private function _login($email, $password) {

        $credentials = ['email' => $email, 'password' => $password];

        if (!$token = auth()->attempt($credentials)) {

            return $this->respondInvalidCredentials();
        }

        $user = auth()->user();
        $user->api_token = $token;

        return $this->respond([
                    'status' => 'success',
                    'status_code' => Res::HTTP_OK,
                    'message' => 'Logged In Successfully!',
                    'data' => $this->userTransformer->transform($user),
                    'role' => auth()->user()->getRoleNames(),
                    'permissions' => auth()->user()->getAllPermissions()
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me() {
        $user = auth()->user();

        if ($user != NULL) {
            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'message' => 'Logged In Successfully!',
                        'data' => $this->userdataTransformer->transform($user),
            ]);
        }

        if ($user === NULL) {
            return $this->respond([
                        'status' => 'error',
                        'status_code' => Res::HTTP_UNAUTHORIZED,
                        'message' => 'Session Expired!',
            ]);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return $this->respond([
                    'status' => 'success',
                    'status_code' => Res::HTTP_OK,
                    'message' => 'Successfully logged out!',
        ]);

        //return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token) {
        return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function createuser(Request $request) {
        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|numeric'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        } else {

            $user = User::create([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'password' => \Hash::make($request->input('password')),
            ]);

            $role = Role::findById($request->input('role_id'));
            $user->assignRole($role->name);

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'message' => 'User Created Successfully!'
            ]);
        }
    }

    public function GetUsers(Request $request) {

        $rules = array(
            'name' => 'sometimes|nullable|max:255',
            'id' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $users = User::with('roles')->get()->toArray();

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'data' => $users,
                        'message' => 'Users Details!',
            ]);
        } catch (QueryException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\PDOException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\Exception $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        }
    }

    public function GetUsersDataTables(Request $request) {

        $rules = array(
            'name' => 'sometimes|nullable|max:255',
            'id' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $users = User::with('roles')
                    ->when($request->input('name') != NULL, function($query) use ($request) {
                        return $query->where("user.name", $request->input('name'));
                    });

            return $this->respond([
                        "draw" => 1,
                        "recordsTotal" => $users->count(),
                        "recordsFiltered" => $users->count(),
                        'data' => $users->get()->toArray(),
            ]);
        } catch (QueryException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\PDOException $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        } catch (\Exception $e) {
            Log::emergency($e);
            return $this->respondInternalErrors();
        }
    }

}
