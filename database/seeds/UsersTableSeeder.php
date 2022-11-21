<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() == 0) {
            $role = Role::where('name', 'admin')->firstOrFail();

            $defaultAdmins = config('voyager.default_admins');

            foreach($defaultAdmins as $admin) {
                $user = User::firstOrCreate([
                    'name'           => $admin['name']
                ],
                [
                    'email'          => $admin['email'],
                    'password'       => bcrypt($admin['password']),
                    'remember_token' => Str::random(60),
                    'role_id'        => $role->id,
                ]);
            }
        }
    }
}
