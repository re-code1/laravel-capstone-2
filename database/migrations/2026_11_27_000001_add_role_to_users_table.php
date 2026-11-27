<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // MariaDB ENUM: the database itself rejects any other value. No default, so every user
            // must be given a role on purpose. 'student' is listed first on purpose: if MariaDB ever has
            // to fill a value for old rows, it uses the first item, which is the role with the least access.
            $table->enum('role', ['student', 'teacher', 'principal', 'admin'])->index()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
