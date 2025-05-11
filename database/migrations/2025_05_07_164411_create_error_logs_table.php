<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(); // HTTP status code or error code
            $table->text('message'); // Error message
            $table->text('exception')->nullable(); // Full exception class name
            $table->text('file')->nullable(); // File where error occurred
            $table->string('line')->nullable(); // Line number
            $table->text('trace')->nullable(); // Full stack trace
            $table->ipAddress('ip')->nullable(); // User IP address
            $table->text('user_agent')->nullable(); // User browser/device info
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // If authenticated user
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('error_logs');
    }
};