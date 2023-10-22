<?php

namespace Marifuli\SchoolService\Repositories;

use App\SmGeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\RolePermission\Entities\Role;
use Modules\RolePermission\Entities\AssignPermission;
use Modules\RolePermission\Entities\Permission;

class InitRepository {

    public function init() {
		config([
            'app.item' => '23876323',
            'marifuli.module_manager_model' => \App\SchoolynxModuleManager::class,
            'marifuli.module_manager_table' => 'schoolynx_module_managers',
            'marifuli.saas_module_name' => 'Saas',
            'marifuli.module_status_check_function' => 'moduleStatusCheck',

            'marifuli.settings_model' => SmGeneralSettings::class,
            'marifuli.module_model' => \Nwidart\Modules\Facades\Module::class,

            'marifuli.user_model' => \App\User::class,
            'marifuli.settings_table' => 'sm_general_settings',
            'marifuli.database_file' => 'schoolynx_edu.sql',
            'marifuli.support_multi_connection' => true
        ]);
    }

    public function config()
	{

        app()->singleton('dashboard_bg', function () {
            $dashboard_background = DB::table('sm_background_settings')->where([['is_default', 1], ['title', 'Dashboard Background']])->first();
            return $dashboard_background;
        });

         app()->singleton('school_info', function () {
            return DB::table('sm_general_settings')->where('school_id', app('school')->id)->first();
        });

       
        app()->singleton('permission', function () {
            if(!Auth::check()){
                return [];
            }
            $Role = Role::find(Auth::user()->role_id);
            $permissionIds = AssignPermission::where('role_id', Auth::user()->role_id)
            ->when($Role->is_saas == 0, function($q) {
                $q->where('school_id', Auth::user()->school_id);
            })->pluck('permission_id')->toArray();

           return Permission::whereIn('id', $permissionIds)
                                ->pluck('route')->toArray();  
        });

	}

}
