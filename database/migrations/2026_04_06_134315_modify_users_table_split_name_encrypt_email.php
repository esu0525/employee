<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('email_hash')->nullable()->after('email');
        });

        // Migrate existing users
        $users = \Illuminate\Support\Facades\DB::table('users')->get();
        foreach ($users as $user) {
            $nameParts = explode(' ', $user->name);
            $lastName = array_pop($nameParts);
            $firstName = count($nameParts) > 0 ? implode(' ', $nameParts) : $lastName;
            if (empty($firstName) && !empty($lastName)) {
                $firstName = $lastName;
                $lastName = '';
            }

            $email = $user->email;
            $hashedEmail = hash('sha256', strtolower(trim($email)));
            $encryptedEmail = \Illuminate\Support\Facades\Crypt::encryptString($email);

            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $encryptedEmail,
                    'email_hash' => $hashedEmail,
                ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique('email_hash');
        });
        
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE users MODIFY email TEXT');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->unique('email');
        });
        
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE users MODIFY email VARCHAR(255)');

        $users = \Illuminate\Support\Facades\DB::table('users')->get();
        foreach ($users as $user) {
            $decryptedEmail = '';
            try {
                $decryptedEmail = \Illuminate\Support\Facades\Crypt::decryptString($user->email);
            } catch (\Exception $e) {
                // Ignore
            }

            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $decryptedEmail ?: 'unknown_' . $user->id . '@example.com',
                ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email_hash']);
            $table->dropColumn(['first_name', 'last_name', 'email_hash']);
        });
    }
};
