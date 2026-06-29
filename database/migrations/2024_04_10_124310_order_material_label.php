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
        if (!Schema::hasTable('order_material_label')) {
            Schema::create('order_material_label', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id')->default(0)->index();
            $table->string('print_type',255)->nullable();
            $table->string('print_size',255)->nullable();
            $table->string('print_no_colors',255)->nullable();
            $table->integer('print_vendor_id')->default(0);
            $table->string('main_label',255)->nullable();
            $table->integer('main_label_vendor_id')->default(0);
            $table->text('washcare_label')->nullable();
            $table->integer('washcare_label_vendor_id')->default(0);
            $table->text('barcode_label')->nullable();
            $table->integer('barcode_label_vendor_id')->default(0);
            $table->text('hangtag')->nullable();
            $table->integer('hangtag_vendor_id')->default(0);
            $table->text('trims_notifications')->nullable();
            $table->string('polybag_size_thickness',255)->nullable();
            $table->string('polybag_material',255)->nullable();
            $table->text('polybag_print_details')->nullable();
            $table->integer('polybag_vendor_id')->default(0);
            $table->string('carton_dimensions',255)->nullable();
            $table->string('carton_color',255)->nullable();
            $table->string('carton_no_of_ply',255)->nullable();
            $table->integer('carton_vendor_id')->default(0);
            $table->string('carton_edge_finish',255)->nullable();
            $table->text('carton_mark_details')->nullable();
            $table->string('carton_make_up',255)->nullable();
            $table->string('air_freight',255)->nullable();
            $table->string('flims_cd',255)->nullable();
            $table->string('picture_card',255)->nullable();
            $table->string('inner_cardboard',255)->nullable();
            $table->string('shiping_size',255)->nullable();
            $table->integer('created_user_id')->default(0);
            $table->integer('crated_staff_id')->default(0);
            $table->integer('updated_user_id')->default(0);
            $table->integer('updated_staff_id')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
