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
use Illuminate\Database\QueryException as QueryException;
use Log;

class RoleController extends ApiController {

    //
    protected $userTransformer;

    public function __construct(userTransformer $userTransformer) {

        $this->userTransformer = $userTransformer;
        //$this->middleware('auth:api', ['except' => ['authenticate', 'register']]);
    }

    public function GetRoles(Request $request) {

        $rules = array(
            'name' => 'sometimes|nullable|max:255',
            'id' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $roles = Role::get()->toArray();

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'data' => $roles,
                        'message' => 'Roles Details!',
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

    public function CreateRole(Request $request) {

        $rules = array(
            'name' => 'required|max:255',
            'id' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $message = 'Role Created!';
            $status_code = Res::HTTP_CREATED;
            if ($request->input('id') === NULL) {
                $role = Role::create(['name' => $request->input('name')]);
            }

            if ($request->input('id') != NULL) {
                $message = 'Role Updated!';
                $status_code = Res::HTTP_OK;
                $role = \App\Role::when($request->input('id'), function($query) use ($request) {
                            return $query->where('roles.id', $request->input('id'));
                        })->first();

                if ($role === NULL) {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => Res::HTTP_NOT_FOUND,
                                'message' => 'Role Not Found!',
                    ]);
                }
                $role->name = $request->input('name');
                $role->save();
                \Artisan::call('cache:clear');
            }

            return $this->respond([
                        'status' => 'success',
                        'status_code' => $status_code,
                        'message' => $message,
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

    public function AssignRole(Request $request) {

        $rules = array(
            'role_id' => 'required|numeric',
            'user_id' => 'required|numeric'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $role = Role::findById($request->input('role_id'));

            if ($role === NULL) {
                return $this->respond([
                            'status' => 'error',
                            'status_code' => Res::HTTP_NOT_FOUND,
                            'message' => 'Role Not Found!'
                ]);
            }

            $user_role = User::where('id', $request->input('user_id'))->first();

            if ($user_role === NULL) {
                return $this->respond([
                            'status' => 'error',
                            'status_code' => Res::HTTP_NOT_FOUND,
                            'message' => 'User Not Found!'
                ]);
            }

            if ($user_role->getRoleNames() != NULL) {
                return $this->respond([
                            'status' => 'error',
                            'status_code' => Res::HTTP_UNPROCESSABLE_ENTITY,
                            'message' => 'User Already Had Another Role!'
                ]);
            }

            $user_role->assignRole($role->name);

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'message' => 'Role Assigned Successful!'
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

    public function RemoveRoleToUser(Request $request) {

        $rules = array(
            'permission_id' => 'required|numeric',
            'role_id' => 'required|numeric'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $role = Role::findById($request->input('role_id'));

            if ($role === NULL) {
                return $this->respond([
                            'status' => 'error',
                            'status_code' => Res::HTTP_NOT_FOUND,
                            'message' => 'Role Not Found!'
                ]);
            }

            $permission = Permission::findById($request->input('permission_id'));

            if ($permission === NULL) {
                return $this->respond([
                            'status' => 'error',
                            'status_code' => Res::HTTP_NOT_FOUND,
                            'message' => 'Permission Not Found!'
                ]);
            }

            $role->revokePermissionTo($permission);

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'message' => 'Permission Removed Successful!'
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
