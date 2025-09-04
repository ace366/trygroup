<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $t->enum('role', ['system','user','assistant']);
            $t->longText('content');
            $t->json('meta')->nullable();  // 任意: トークン数、言語、モデル名など
            $t->timestamps();
            $t->index(['conversation_id','created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('messages');
    }
};
