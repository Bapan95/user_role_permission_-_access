<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role_Master;
use App\Models\UserPermission;
use App\Models\MenuMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends Controller
{
    public function showPermissionsPage()
    {
        return view('user_permission.index'); // This should match the path to your Blade file
    }
    // Fetch roles for the dropdown
    public function getRoleList()
    {
        $roles = Role_Master::all();
        return response()->json(['list' => $roles]);
    }

    // Fetch permissions based on selected role
    // public function getPermissions(Request $request)
    // {
    //     $roleId = $request->input('role_id');

    //     $permissions = DB::table('menu_masters as mm')
    //         ->leftJoin('user_permissions as up', function ($join) use ($roleId) {
    //             $join->on('mm.id', '=', 'up.menu_id')
    //                 ->where('up.role_id', $roleId)
    //                 ->where('up.is_active', '1');
    //         })
    //         ->select(
    //             'mm.id as menu_id',
    //             'mm.menu_name',
    //             'mm.menu_rank',
    //             DB::raw('IFNULL(up.id, "") as permission_id'),
    //             'up.add_flag',
    //             'up.edit_flag',
    //             'up.delete_flag',
    //             'up.download_flag',
    //             'mm.menu_type'
    //         )
    //         ->whereNotNull('mm.menu_link')
    //         ->orderByRaw("FIELD(mm.menu_type, 'Common', 'OOH', 'activation', 'Media')")
    //         ->orderBy('mm.menu_name')
    //         ->get();

    //     return response()->json(['list' => $permissions]);
    // }

    public function updateSinglePermission(Request $request)
    {
        // print_r($request->all());
        // die;
        try {
            // Validate the request
            $validated = $request->validate([
                'add_value' => 'nullable|boolean',
                'edit_value' => 'nullable|boolean',
                'delete_value' => 'nullable|boolean',
            ]);
    
            // Check if the record exists
            $permission = UserPermission::find($request->permission_id);
    
            if ($permission) {
                // Update existing record
                $permission->update([
                    'role_id' => $request->role_id,
                    'menu_id' => $request->menu_id,
                    'add_flag' => $request->add_flag ? 1 : 0,
                    'edit_flag' => $request->edit_flag ? 1 : 0,
                    'delete_flag' => $request->delete_flag ? 1 : 0,
                    // 'download_flag' => $request->print_value ? 1 : 0,
                    'updated_by' => auth()->user()->id, // Update only if authenticated
                    'updated_at' => now() // Update timestamp
                ]);
            } else {
                // Create new record
                UserPermission::create([
                    'id' => $request->permission_id,
                    'role_id' => $request->role_id,
                    'menu_id' => $request->menu_id,
                    'add_flag' => $request->add_flag ? 1 : 0,
                    'edit_flag' => $request->edit_flag ? 1 : 0,
                    'delete_flag' => $request->delete_flag ? 1 : 0,
                    // 'download_flag' => $request->print_value ? 1 : 0,
                    'created_by' => auth()->user()->id, // Set if creating
                    'created_at' => now(), // Set timestamp for creation
                ]);
            }
    
            return response()->json(['status' => 'Success', 'message' => $permission ? 'Permission Updated Successfully' : 'Permission Created Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Error', 'message' => 'Unable to Update or Create Permission', 'error' => $e->getMessage()]);
        }
    }
    

    public function getPermissions(Request $request)
    {
        try {
            $permissions = UserPermission::where('role_id', $request->role_id)->get();
            $menuMasters = MenuMaster::where('is_active', 1)->get();

            $list = $menuMasters->map(function ($menu) use ($permissions) {
                $permission = $permissions->firstWhere('menu_id', $menu->id);
                return [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->menu_name,
                    'menu_type' => $menu->menu_type,
                    'permission_id' => $permission ? $permission->id : null,
                    'add_flag' => $permission ? $permission->add_flag : 0,
                    'edit_flag' => $permission ? $permission->edit_flag : 0,
                    'delete_flag' => $permission ? $permission->delete_flag : 0,
                    'download_flag' => $permission ? $permission->download_flag : 0,
                ];
            });

            return response()->json(['status' => 'Success', 'list' => $list]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Error', 'message' => 'Unable to Fetch Permissions', 'error' => $e->getMessage()]);
        }
    }

    // Update permissions for a specific role
    public function updatePermissions(Request $request)
    {

        // Validate the request
        $validated = $request->validate([
            'add_*' => 'nullable|boolean',
            'edit_*' => 'nullable|boolean',
            'delete_*' => 'nullable|boolean',
        ]);

        $data = $request->except(['_token']);
        // print_r($data);
        // die;
        // Loop through each permission ID and update or insert the corresponding record
        foreach ($data['permission_id'] as $index => $permissionId) {
            $permission = UserPermission::find($permissionId);

            if ($permission) {
                // Update existing permission
                $permission->add_flag = isset($data['add_' . $index]) ? 1 : 0;
                $permission->edit_flag = isset($data['edit_' . $index]) ? 1 : 0;
                $permission->delete_flag = isset($data['delete_' . $index]) ? 1 : 0;
                // $permission->download_flag = isset($data['print_' . $index]) ? 1 : 0;

                // Optionally update 'is_active', 'updated_by', and 'updated_at' columns
                $permission->is_active = 1; // Or set based on your needs
                $permission->updated_at = now(); // Set to current timestamp
                $permission->updated_by = auth()->id(); // Set to current timestamp

                $permission->save();
            } else {
                // Insert new permission if it doesn't exist
                UserPermission::create([
                    'role_id' => $request->role_id, // Assuming you have role_id in request
                    'menu_id' => $data['menu_id'][$index],
                    'add_flag' => isset($data['add_' . $index]) ? 1 : 0,
                    'edit_flag' => isset($data['edit_' . $index]) ? 1 : 0,
                    'delete_flag' => isset($data['delete_' . $index]) ? 1 : 0,
                    // 'download_flag' => isset($data['print_' . $index]) ? 1 : 0,
                    'is_active' => 1, // Set as active
                    'created_by' => auth()->id(), // Assuming user is authenticated
                    'created_at' => now(), // Set to current timestamp
                ]);
            }
        }

        return response()->json(['status' => 'Success']);
    }

    // Update a single permission
    // public function updateSinglePermission(Request $request)
    // {
    //     $permission = UserPermission::find($request->input('permission_id'));
    //     if ($permission) {
    //         $permission->add_flag = $request->input('add_value') ? 1 : 0;
    //         $permission->edit_flag = $request->input('edit_value') ? 1 : 0;
    //         $permission->delete_flag = $request->input('delete_value') ? 1 : 0;
    //         $permission->download_flag = $request->input('print_value') ? 1 : 0;
    //         $permission->save();
    //         return response()->json(['status' => 'Success']);
    //     }
    //     return response()->json(['status' => 'Error']);
    // }
}
