<?php

declare(strict_types=1);

use App\Enums\CommentStatus;
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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();

            $table->string('commenter_name');
            $table->text('body');

            $table->string('status', 32)->default(CommentStatus::Submitted->value)->index();
            $table->timestamp('published_at')->nullable()->index();

            $table->boolean('moderation_reject')->nullable();
            $table->decimal('moderation_confidence')->nullable();
            $table->text('moderation_explanation')->nullable();
            $table->text('moderation_error')->nullable();
            $table->timestamp('moderated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
