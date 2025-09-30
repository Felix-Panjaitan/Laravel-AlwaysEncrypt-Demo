<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the columns normally first
        Schema::table('products', function (Blueprint $table) {
            $table->string('credit_card_number')->nullable();
            $table->string('secret_notes')->nullable();
        });

        // For SQL Server Always Encrypted, we need to use raw SQL
        // These commands would normally be executed in SQL Server Management Studio
        // Here we're showing what the SQL would look like, but it's commented out
        // because it requires special encryption setup in your SQL Server

        /*
        DB::unprepared("
            ALTER TABLE products
            ALTER COLUMN credit_card_number varchar(255)
            ENCRYPTED WITH (
                ENCRYPTION_TYPE = DETERMINISTIC,
                ALGORITHM = 'AEAD_AES_256_CBC_HMAC_SHA_256',
                COLUMN_ENCRYPTION_KEY = 'CEK_Auto1'
            )
        ");

        DB::unprepared("
            ALTER TABLE products
            ALTER COLUMN secret_notes varchar(255)
            ENCRYPTED WITH (
                ENCRYPTION_TYPE = RANDOMIZED,
                ALGORITHM = 'AEAD_AES_256_CBC_HMAC_SHA_256',
                COLUMN_ENCRYPTION_KEY = 'CEK_Auto1'
            )
        ");
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['credit_card_number', 'secret_notes']);
        });
    }
};
