<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.users.role-permission', [
            'users' => User::with('roles')->get(),
            'roles' => Role::with(['permissions'])->withCount('users')->get(),
            'permissions' => Permission::all()
        ]);
    }

    // USERS        
        /**
         * show detail user
         *
         * @param  mixed $user
         * @return void
         */
        public function show(User $user)
        {
            $user->loadMissing('roles.permissions');
            
            if (request()->ajax()) {
                return json_encode($user);
            }

            return [];
        }

        /**
         * assign Role to User
         *
         * @param  mixed $request
         * @param  mixed $user
         * @return void
         */
        public function assignRole(Request $request)
        {
            $data = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['required', 'string', 'exists:roles,name'],
                'password' => ['required', 'current_password:web']
            ]);

            $user = User::findOrFail($data['user_id']);
            $user->syncRoles($data['roles']);
            return redirect(route('management.users'))->with('alert', [
                'type' => 'success',
                'msg' => 'Role Assigned to '. $user->name .'!'
            ]); 
        }
    // USERS

    // ROLES        
        /**
         * get Role by id
         *
         * @param  mixed $role
         * @return void
         */
        public function getRole(Role $role)
        {
            $role->loadMissing('permissions');

            if (request()->ajax()) {
                return json_encode($role);
            }

            return [];
        }
                
        /**
         * addRole
         *
         * @param  mixed $request
         * @return void
         */
        public function addRole(Request $request)
        { 
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
                'permissions' => ['nullable', 'array', 'min:1'],
                'permissions.*' => ['nullable', 'string', 'exists:permissions,name'],
            ]);

            $role = Role::create([
                'name' => $data['name']
            ]);

            if (array_key_exists('permissions', $data)) {
                $role->syncPermissions($data['permissions']);
            }

            return redirect(route('management.users'))->with('alert', [
                'type' => 'success',
                'msg' => 'Role Added!'
            ]);
        }
        
        /**
         * assign Permissions to Role
         *
         * @param  mixed $request
         * @param  mixed $role
         * @return void
         */
        public function assignPermissions(Request $request, Role $role)
        {
            $data = $request->validate([
                'permissions' => ['required', 'array', 'min:1'],
                'permissions.*' => ['required', 'string', 'exists:permissions,name'],
                'is_edit' => ['nullable', 'boolean'],
                'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id]
            ]);

            if (array_key_exists('is_edit', $data)) {
                $role->update(['name' => $data['name']]);
            }

            $role->syncPermissions($data['permissions']);

            return redirect(route('management.users'))->with('alert', [
                'type' => 'success',
                'msg' => 'Permissions has been Assigned!'
            ]);
        }
        
        /**
         * delete Role
         *
         * @param  mixed $role
         * @return void
         */
        public function deleteRole(Role $role)
        {
            $role->delete();

            return redirect(route('management.users'))->with('alert', [
                'type' => 'success',
                'msg' => 'Role Deleted!'
            ]);
        }
    // ROLES

    // PERMISSIONS        
        /**
         * get Permission by id
         *
         * @param  mixed $permission
         * @return void
         */
        public function getPermission(Permission $permission)
        {
            $permission->loadMissing('roles');

            if (request()->ajax()) {
                return json_encode($permission);
            }

            return [];
        }

        /**
         * add Permission
         *
         * @param  mixed $request
         * @return void
         */
        public function addPermission(Request $request)
        {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:permissions,name']
            ]);

            Permission::create($data);

            return redirect(route('management.users'))->with('alert', [
                'type' => 'success',
                'msg' => 'Permission Added!'
            ]);
        }
        
        /**
         * update Permission
         *
         * @param  mixed $request
         * @param  mixed $permission
         * @return void
         */
        public function updatePermission(Request $request, Permission $permission)
        {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            ]);

            $permission->update([
                'name' => $data['name']
            ]);

            return redirect(route('management.users'))->with('alert', [
                'type' => 'success',
                'msg' => 'Permission has been Updated!'
            ]);
        }
        
        /**
         * delete Permission
         *
         * @param  mixed $permission
         * @return void
         */
        public function deletePermission(Permission $permission)
        {
            $permission->delete();

            return redirect(route('management.users'))->with('alert', [
                'type' => 'success',
                'msg' => 'Permission Deleted!'
            ]);
        }
    // PERMISSIONS
}
