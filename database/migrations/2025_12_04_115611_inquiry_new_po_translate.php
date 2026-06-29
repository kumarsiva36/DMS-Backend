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
        if (!Schema::hasTable('inquiry_new_po_translate')) {
            Schema::create('inquiry_new_po_translate', function (Blueprint $table) {
                $table->bigIncrements('pid');
                $table->bigInteger('id')->default(0);
                $table->bigInteger('parent_id')->default(0)->comment('parent PO Id');
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->integer('updated_user_id')->default(0);
                $table->integer('updated_staff_id')->default(0);
                $table->tinyInteger('status')->default(0);
                $table->string('media_reference_id',255)->nullable();
                $table->text('buyer')->nullable();
                $table->text('seller')->nullable();
                $table->text('maker')->nullable();
                $table->string('style_no',255)->nullable();
                $table->integer('article_id')->default(0);
                $table->string('article_name',255)->nullable();
                $table->text('article_description')->nullable();
                $table->integer('fabric_type_id')->default(0);
                $table->string('fabric_type',255)->nullable();
                $table->integer('fabric_composition_id')->default(0);
                $table->text('fabric_composition')->nullable();
                $table->string('fabric_GSM',255)->nullable();
                $table->string('gsm_tolerance',50)->nullable();
                $table->tinyInteger('gsm_percent_type')->default(1);
                $table->string('yarn_count_type',255)->nullable();
                $table->integer('total_qty')->default(0);
                $table->integer('total_qty_min_tol')->default(0);
                $table->integer('total_qty_max_tol')->default(0);
                $table->tinyInteger('total_qty_percent_type')->default(1);
                $table->integer('units')->default(0);
                $table->string('currency',255)->nullable();
                $table->float('price',10,2)->default(0);
                $table->integer('price_units')->default(0);
                $table->tinyInteger('incoterms')->default(0);
                $table->string('payment_terms',100)->nullable();
                $table->date('delivery_date')->nullable();
                $table->string('delivery_date_type',255)->nullable();
                $table->string('origin_port',255)->nullable();
                $table->string('destination_port',255)->nullable();
                $table->string('mode_of_shipment',10)->nullable();
                $table->string('document_requirement',255)->nullable();
                $table->string('hs_code',255)->nullable();
                $table->string('place_of_jurisdiction',255)->nullable();
                $table->text('penality')->nullable();
                $table->text('testing_requirements')->nullable();
                $table->text('fabric_testing_agency')->nullable();
                $table->text('garment_testing_agency')->nullable();
                $table->text('testing_cost')->nullable();
                $table->text('additional_information')->nullable();
                $table->text('forwarder')->nullable();
                $table->integer('forwarder_id')->default(0);
                $table->text('forwarder_address')->nullable();
                $table->string('forwarder_contact_person',255)->nullable();
                $table->string('forwarder_phone',255)->nullable();
                $table->string('forwarder_email',255)->nullable();
                $table->string('po_number',255)->nullable();
                $table->string('inspection_company',255)->nullable();
                $table->string('inspection_type',255)->nullable();
                $table->text('inspection_cost')->nullable();
                $table->string('sign_option',20)->nullable();
                $table->tinyInteger('same_commertial_info')->default(0)->comment('0-No, 1-Yes');
                $table->tinyInteger('same_testing_agency')->default(0)->comment('0-No, 1-Yes');
                $table->string('language',10)->default('en');
                $table->tinyInteger('translated')->default(0)->comment('0-No, 1-Yes');
                $table->timestamps();
                $table->index(['company_id','workspace_id','user_id','staff_id','article_id','status','parent_id'],'inquiry_new_po_translate_idx');
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
