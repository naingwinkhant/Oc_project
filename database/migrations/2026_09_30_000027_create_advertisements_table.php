<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Advertising a manager writes and the storefront shows.
 *
 * Separate from notices: a notice is a line of text in the bell, while an
 * advertisement is a picture in the carousel at the top of the catalogue, so
 * the two are edited in different places and go to different places.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();

            $table->string('title', 160);
            $table->string('eyebrow', 60)->nullable();
            $table->string('body', 300)->nullable();

            // A picture, a video, or neither: a worded slide over the store's
            // own colours is better than a blank frame.
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->string('poster')->nullable();

            // Where the button goes. Held as a path rather than a full URL so a
            // form cannot make the shop link off to somewhere else.
            $table->string('link')->nullable();
            $table->string('button_label', 40)->nullable();

            // Order in the carousel, lowest first.
            $table->unsignedInteger('position')->default(0);

            // The window it appears in, either side optional.
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
