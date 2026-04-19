<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $permissions = [
            ['name' => 'intersite.additional_cost.index', 'description' => 'View Intersite Additional Costs', 'guard_name' => 'web', 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'intersite.additional_cost.create', 'description' => 'Create Intersite Additional Cost', 'guard_name' => 'web', 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'intersite.additional_cost.show', 'description' => 'View Intersite Additional Cost Details', 'guard_name' => 'web', 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'intersite.additional_cost.edit', 'description' => 'Edit Intersite Additional Cost', 'guard_name' => 'web', 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'intersite.additional_cost.post', 'description' => 'Post/Reverse Intersite Additional Cost', 'guard_name' => 'web', 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'intersite.additional_cost.delete', 'description' => 'Delete Intersite Additional Cost', 'guard_name' => 'web', 'active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    public function down()
    {
        DB::table('permissions')->whereIn('name', [
            'intersite.additional_cost.index',
            'intersite.additional_cost.create',
            'intersite.additional_cost.show',
            'intersite.additional_cost.edit',
            'intersite.additional_cost.post',
            'intersite.additional_cost.delete',
        ])->delete();
    }
};
