<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Copy existing assigned_to values into task_user pivot
        $tasks = DB::table('tasks')->whereNotNull('assigned_to')->get(['id', 'assigned_to']);
        foreach ($tasks as $t) {
            // ignore duplicates
            DB::table('task_user')->updateOrInsert([
                'task_id' => $t->id,
                'user_id' => $t->assigned_to,
            ], ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down()
    {
        // remove entries copied from assigned_to (best-effort)
        $tasks = DB::table('tasks')->whereNotNull('assigned_to')->get(['id', 'assigned_to']);
        foreach ($tasks as $t) {
            DB::table('task_user')->where('task_id', $t->id)->where('user_id', $t->assigned_to)->delete();
        }
    }
};
