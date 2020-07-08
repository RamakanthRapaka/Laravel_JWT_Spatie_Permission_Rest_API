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

class PermissionController extends ApiController {

    protected $userTransformer;

    public function __construct(userTransformer $userTransformer) {

        $this->userTransformer = $userTransformer;
        //$this->middleware('auth:api', ['except' => ['authenticate', 'register']]);
    }

    public function GetPermissions(Request $request) {

        $rules = array(
            'name' => 'sometimes|nullable|max:255',
            'id' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $permission = Permission::get()->toArray();

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'data' => $permission,
                        'message' => 'Permission Details!',
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

    public function CreatePermission(Request $request) {

        $rules = array(
            'name' => 'required|max:255',
            'id' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $message = 'Permission Created!';
            $status_code = Res::HTTP_CREATED;
            if ($request->input('id') === NULL) {
                $permission = Permission::create(['name' => $request->input('name')]);
            }

            if ($request->input('id') != NULL) {
                $message = 'Permission Updated!';
                $status_code = Res::HTTP_OK;
                $permission = \App\Permission::when($request->input('id'), function($query) use ($request) {
                            return $query->where('permissions.id', $request->input('id'));
                        })->first();

                if ($permission === NULL) {
                    return $this->respond([
                                'status' => 'error',
                                'status_code' => Res::HTTP_NOT_FOUND,
                                'message' => 'Permission Not Found!',
                    ]);
                }
                $permission->name = $request->input('name');
                $permission->save();
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

    public function AssignPermission(Request $request) {

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

            $role->givePermissionTo($permission);

            return $this->respond([
                        'status' => 'success',
                        'status_code' => Res::HTTP_OK,
                        'message' => 'Permission Assigned Successful!'
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

    public function RemovePermissionToRole(Request $request) {

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
    
    public function GetPermissionsDataTables(Request $request) {

        $rules = array(
            'name' => 'sometimes|nullable|max:255',
            'id' => 'sometimes|nullable|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }

        try {
            $permissions = \App\Permission::when($request->input('name') != NULL, function($query) use ($request) {
                        return $query->where("permissions.name", $request->input('name'));
                    });

            return $this->respond([
                        "draw" => 1,
                        "recordsTotal" => $permissions->count(),
                        "recordsFiltered" => $permissions->count(),
                        'data' => $permissions->get()->toArray(),
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
