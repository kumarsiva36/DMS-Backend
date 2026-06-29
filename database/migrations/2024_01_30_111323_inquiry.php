<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\NullableType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('inquiry')) {
            Schema::create('inquiry', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('media_reference_id',255)->nullable();
                $table->string('factory_ids',255)->nullable();
                $table->text('read_by_factories')->nullable();
                $table->integer('category_id')->default(0);
                $table->integer('article_id')->nullable();
                $table->string('style_no',255)->nullable();
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('fabric_type_id')->default(0);
                $table->integer('fabric_composition_id')->default(0);
                $table->string('fabric_type',255)->nullable();
                $table->string('sample_imgae',255)->nullable();
                $table->string('fabric_GSM',255)->nullable();
                $table->string('yarn_count',255)->nullable();
                $table->text('measurement_sheet')->nullable();
                $table->text('style_article_description')->nullable();
                $table->text('special_finish')->nullable();
                $table->integer('total_qty')->default(0);
                $table->integer('total_qty_min_tol')->default(0);
                $table->integer('total_qty_max_tol')->default(0);
                $table->text('patterns')->nullable();
                $table->text('jurisdiction')->nullable();
                $table->text('customs_declaraion_document')->nullable();
                $table->text('penality')->nullable();
                $table->string('print_image',255)->nullable();
                $table->string('print_size',255)->nullable();
                $table->string('print_no_of_colors',255)->nullable();
                $table->string('print_type',255)->nullable();
                $table->text('main_lable')->nullable();
                $table->text('main_lable_info')->nullable();
                $table->text('washcare_lable')->nullable();
                $table->text('washcare_lable_info')->nullable();
                $table->text('hangtag_lable')->nullable();
                $table->text('hangtag_lable_info')->nullable();
                $table->text('barcode_lable')->nullable();
                $table->text('barcode_lable_info')->nullable();
                $table->text('trims_nominations')->nullable();
                $table->string('poly_bag_size',255)->nullable();
                $table->text('poly_bag_material')->nullable();
                $table->text('poly_bag_price')->nullable();
                $table->text('poly_bag_print')->nullable();
                $table->string('carton_bag_dimensions',255)->nullable();
                $table->string('carton_color',255)->nullable();
                $table->string('carton_material',255)->nullable();
                $table->string('carton_edge_finish',255)->nullable();
                $table->text('carton_mark')->nullable();
                $table->string('make_up',255)->nullable();
                $table->string('films_cd',255)->nullable();
                $table->string('picture_card',255)->nullable();
                $table->string('inner_cardboard',255)->nullable();
                $table->string('shipping_size',255)->nullable();
                $table->string('air_frieght',255)->nullable();
                $table->date('estimate_delivery_date')->nullable();
                $table->date('due_date')->nullable();
                $table->tinyInteger('incoterms')->default(0);
                $table->text('payment_terms')->nullable();
                $table->text('payment_instructions')->nullable();
                $table->float('target_price')->default(0.0);
                $table->string('currency',255)->nullable();
                $table->text('forbidden_substance_info')->nullable();
                $table->text('testing_requirements')->nullable();
                $table->text('sample_requirements')->nullable();
                $table->text('special_requests')->nullable();
                $table->tinyInteger('is_po_generated')->default(0);
                $table->tinyInteger('forwarder')->default(0)->comment('1=>"Buyer Nominated",2=>"Supplier Nominated",3=>"Others"');
                $table->integer('updated_user_id')->default(0);
                $table->integer('updated_staff_id')->default(0);
                $table->timestamps();
                $table->index(['factory_ids','article_id','user_id','staff_id','company_id','workspace_id','fabric_type_id','created_at']);
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
