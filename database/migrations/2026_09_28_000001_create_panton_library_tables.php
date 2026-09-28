<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('contact')->nullable();
            $table->string('fax')->nullable();
            $table->string('branch')->nullable();
            $table->string('employee_no')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('role')->default('View Only');
            $table->string('login_status')->default('Enable');
            $table->string('suspend_login')->default('No');
            $table->date('activation_date')->nullable();
            $table->date('disable_date')->nullable();
            $table->unsignedSmallInteger('maximum_attempts')->default(5);
            $table->string('last_login')->nullable();
            $table->string('last_logout')->nullable();
            $table->string('password')->nullable();
            $table->json('group_ids')->nullable();
            $table->json('widget_ids')->nullable();
            $table->json('delegate_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('document');
            $table->boolean('repository')->default(false);
            $table->json('security')->nullable();
            $table->json('approver_ids')->nullable();
            $table->json('control_ids')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('area_staff', function (Blueprint $table) {
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->primary(['area_id', 'staff_id']);
        });

        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('folders')->nullOnDelete();
            $table->string('name');
            $table->json('security')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference');
            $table->string('description');
            $table->string('rev')->default('0');
            $table->date('effective_on')->nullable();
            $table->string('workflow')->default('Draft');
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->string('rev');
            $table->date('effective_on')->nullable();
            $table->timestamps();
        });

        Schema::create('area_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->string('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_activities');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('area_staff');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('staff');
    }
};
