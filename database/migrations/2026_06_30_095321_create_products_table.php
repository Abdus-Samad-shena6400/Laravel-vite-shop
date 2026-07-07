<?php
use App\Models\User;
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
        Schema::create('products', function (Blueprint $table) {
    $table->id();

    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->foreignId('brand_id')->constrained()->cascadeOnDelete();

    $table->string('name');
    $table->string('slug')->unique();

    $table->string('featured_image')->nullable();

    $table->string('short_description', 500);
    $table->longText('description')->nullable();

    $table->decimal('regular_price', 10, 2);
    $table->decimal('sale_price', 10, 2)->nullable();

    $table->integer('quantity')->default(0);

    $table->boolean('featured')->default(false);
    $table->boolean('status')->default(true);

    $table->foreignIdFor(User::class, 'created_by')->nullable();
    $table->foreignIdFor(User::class, 'updated_by')->nullable();
    $table->foreignIdFor(User::class, 'deleted_by')->nullable();

    $table->timestamps();
});
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
