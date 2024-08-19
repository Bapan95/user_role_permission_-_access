<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role_Master;
use App\Models\UserPermission;
use App\Models\MenuMaster;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Session;

class NavController extends Controller
{
    public function showTopNavPage()
    {
        return view('user_permission.top_nav'); // This should match the path to your Blade file
    }

    public function sideNavCall(Request $request)
    {
        $userRoleMapping = DB::table('user_role_mapping_masters')
            ->where('user_id', Auth::user()->id) // Replace $roles[0]->id with your user role ID
            ->first();

        if ($userRoleMapping) {
            $roleId = $userRoleMapping->role_id; // Get the role_id from the user_role_mapping_masters table
        }
        $roles = DB::table('role_masters')
            ->where('is_active', true)
            ->where('id', $roleId)
            ->get(); // Retrieve active roles

        if (!$roles) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the permissions for the user's role
        $permissions = UserPermission::with('menu')->where('role_id', $roles[0]->id)
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->where('add_flag', 1)
                    ->orWhere('edit_flag', 1)
                    ->orWhere('delete_flag', 1);
            })
            ->get();
        if ($permissions->isEmpty()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // Store permissions in session
        $sessionPermissions = [];
        foreach ($permissions as $permission) {
            $sessionPermissions[$permission->menu->menu_link] = [
                'menu_link' => $permission->menu->menu_link,
                'can_add' => $permission->add_flag,
                'can_edit' => $permission->edit_flag,
                'can_delete' => $permission->delete_flag,
            ];
        }

        
        session(['user_permissions' => $sessionPermissions]);
        
        $superAdmin = DB::table('user_role_mapping_masters')
        ->where('user_id', Auth::user()->id)
        ->first();
        
        // print_r($super_admin);
        // die;
        // Store the super_admin in the session
        session(['super_admin' => $superAdmin ]);


        // Fetch the main menu items based on permissions
        $mainMenuItems = MenuMaster::whereIn('id', $permissions->pluck('menu_id'))
            ->where('is_active', 1)
            ->whereRaw('menu_rank = FLOOR(menu_rank)')
            ->orderBy('menu_rank')
            ->get();

        // Fetch the submenu items based on the parent_id
        $subMenuItems = MenuMaster::whereIn('parent_id', $mainMenuItems->pluck('menu_rank'))
            ->whereIn('id', $permissions->pluck('menu_id'))
            ->where('is_active', 1)
            ->orderBy('menu_rank')
            ->get();

        // Generate HTML for the navigation
        $html = $this->generateMenuHtml($mainMenuItems, $subMenuItems);

        return response()->json(['html' => $html]);
    }

    private function generateMenuHtml($mainMenuItems, $subMenuItems)
    {
        $html = '<aside class="main-sidebar"><section class="sidebar position-relative"><div class="user-profile px-40 py-15"><div class="d-flex align-items-center"><div class="image"><img src="' . asset('public/assets/images/avatar/1.jpg') . '" class="avatar avatar-lg" alt="User Image"></div><div class="info ms-10"><p class="mb-0">Welcome</p><h5 class="mb-0">' . auth()->user()->name . '</h5></div></div></div><div class="multinav"><div class="multinav-scroll" style="height: 100%;"><ul class="sidebar-menu" data-widget="tree" id="side_bar">';
        $html .= $this->generateMenuItemsHtml($mainMenuItems, $subMenuItems);
        $html .= '</ul></div></div></section></aside>';

        return $html;
    }

    private function generateMenuItemsHtml($mainMenuItems, $subMenuItems)
    {
        $html = '';
        $baseUrl = url('/');
        // print_r($baseUrl);
        // die;
        foreach ($mainMenuItems as $menuItem) {
            // Assuming $menuItem is available in your controller method
            $menuLink = htmlspecialchars($menuItem->menu_type);

            // Generate the image URL
            $imageUrl = asset('public/assets/images/svg-icon/' . $menuLink);
            $html .= '<li class="treeview-1">';
            $html .= '<a href="' . $baseUrl . '/' . htmlspecialchars($menuItem->menu_link) . '">';
            $html .= '<img src="' . $imageUrl . '" class="svg-icon"
                                alt="">';
            if ($menuItem->menu_icon) {
                $html .= '<span class="pull-right-container"><i class="' . htmlspecialchars($menuItem->menu_icon) . '"></i></span>';
            }
            $html .= htmlspecialchars($menuItem->menu_name);
            $html .= '</a>';
            $childItems = $subMenuItems->where('parent_id', $menuItem->menu_rank);
            if ($childItems->isNotEmpty()) {
                $html .= '<ul class="treeview-menu-1">';
                foreach ($childItems as $subItem) {
                    $html .= '<li>';
                    $html .= '<a href="' . $baseUrl . '/' . htmlspecialchars($subItem->menu_link) . '">';
                    if ($subItem->menu_icon) {
                        $html .= '<i class="' . htmlspecialchars($subItem->menu_icon) . '"></i>';
                    }
                    $html .= htmlspecialchars($subItem->menu_name);
                    $html .= '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</li>';
        }

        return $html;
    }

    public function authenticated()
    {
        $userRoleMapping = DB::table('user_role_mapping_masters')
            ->where('user_id', Auth::user()->id) // Replace $roles[0]->id with your user role ID
            ->first();

        if ($userRoleMapping) {
            $roleId = $userRoleMapping->role_id; // Get the role_id from the user_role_mapping_masters table
        }
        $roles = DB::table('role_masters')
            ->where('is_active', true)
            ->where('id', $roleId)
            ->get(); // Retrieve active roles

        if (!$roles) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $roleId = $roles[0]->id;
        // Fetch user permissions
        $permissions = $this->fetchUserPermissions($roleId);

        // Store permissions in session
        Session::put('user_permissions', $permissions);
        // Redirect to intended page
        return redirect()->intended($this->redirectPath());
    }
    public function fetchUserPermissions($roleId)
    {
        $permissions = UserPermission::with('menu')
            ->where('role_id', $roleId)
            ->get()
            ->map(function ($permission) {
                return [
                    'menu_name' => $permission->menu->menu_name,
                    'can_add' => $permission->add_flag,
                    'can_edit' => $permission->edit_flag,
                    'can_delete' => $permission->delete_flag,
                ];
            });

        return $permissions->toArray();
    }
}
