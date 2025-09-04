<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mail_unsubscribes', function (Blueprint $table) {
            $table->id();
            $table->string('email', 320)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('reason', 50)->nullable(); // one-click, manual 等
            $table->timestamps();
            $table->index('email');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mail_unsubscribes');
    }
};
