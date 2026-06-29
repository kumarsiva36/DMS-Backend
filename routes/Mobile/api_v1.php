<?php
namespace App\Http\Controllers\Mobile\v1;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\v1\Common\Countries;
use App\Http\Controllers\Mobile\v1\Auth\Roles;
use App\Http\Controllers\Mobile\v1\Auth\Staffs;
use App\Http\Controllers\Mobile\v1\Auth\Users;
use App\Http\Controllers\Mobile\v1\Common\Size;
use App\Http\Controllers\Mobile\v1\Common\Color;
use App\Http\Controllers\Mobile\v1\Common\DashBoard;
use App\Http\Controllers\Mobile\v1\Common\DashboardNew;
use App\Http\Controllers\Mobile\v1\Common\HolidaySettings;
use App\Http\Controllers\Mobile\v1\Company\CompanySettings;
use App\Http\Controllers\Mobile\v1\Order\AddOrder;
use App\Http\Controllers\Mobile\v1\Order\AddOrderContacts;
use App\Http\Controllers\Mobile\v1\Order\AddOrderProduction;
use App\Http\Controllers\Mobile\v1\Order\AddOrderSKU;
use App\Http\Controllers\Mobile\v1\Order\ArticleNames;
use App\Http\Controllers\Mobile\v1\Order\EditOrders\EditOrder;
use App\Http\Controllers\Mobile\v1\Order\FabricTypes;
use App\Http\Controllers\Mobile\v1\Order\Factories;
use App\Http\Controllers\Mobile\v1\Order\IncomeTerms;
use App\Http\Controllers\Mobile\v1\Order\OrderCategories;
use App\Http\Controllers\Mobile\v1\Order\OrderTasks;
use App\Http\Controllers\Mobile\v1\Order\OrderTemplates;
use App\Http\Controllers\Mobile\v1\Order\PCUs;
use App\Http\Controllers\Mobile\v1\User\Register;
use App\Http\Controllers\Mobile\v1\Plan\ActivePlans;
use App\Http\Controllers\Mobile\v1\Common\UserSettings;
use App\Http\Controllers\Mobile\v1\Common\StaffSettings;
use App\Http\Controllers\Mobile\v1\Common\WeekOffs;
use App\Http\Controllers\Mobile\v1\Order\BasicInfo;
use App\Http\Controllers\Mobile\v1\Order\OrderAddSpecs;
use App\Http\Controllers\Mobile\v1\Order\Buyers;
use App\Http\Controllers\Mobile\v1\Order\DataInput\AddMultipleData;
use App\Http\Controllers\Mobile\v1\Order\DataInput\GetCalendarData;
use App\Http\Controllers\Mobile\v1\Order\EditOrders\EditContacts;
use App\Http\Controllers\Mobile\v1\Order\EditOrders\EditProduction;
use App\Http\Controllers\Mobile\v1\Order\EditOrders\EditSKU;
use App\Http\Controllers\Mobile\v1\Order\EditOrders\EditTasks;
use App\Http\Controllers\Mobile\v1\Order\GetOrder\GetOrder;
use App\Http\Controllers\Mobile\v1\Order\GetOrder\GetStyle;
use App\Http\Controllers\Mobile\v1\Order\RescheduleOrderTasks;
use App\Http\Controllers\Mobile\v1\Order\GetOrder\GetOrderRegistrationPageDropDownDetails;
use App\Http\Controllers\Mobile\v1\Order\GetOrderSkuDefaultColorandSize;
use App\Http\Controllers\Mobile\v1\Order\GetOrder\GetOrderDetails;
use App\Http\Controllers\Mobile\v1\Common\TimezoneFormatSetting;
use App\Http\Controllers\Mobile\v1\Inquiry\Inquiry;
use App\Http\Controllers\Mobile\v1\Order\SubTask;
use App\Http\Controllers\Mobile\v1\Fabric\Fabric;
use App\Http\Controllers\Mobile\v1\Inquiry\InquiryChat;
use App\Http\Controllers\Mobile\v1\PurchaseOrder\PO;
use App\Http\Controllers\Mobile\v1\TechPack\TeckPackDetail;

Route::group(['prefix' => 'v1'], function()
{
    /* User/Admin Login Routes */
    Route::post('/register-user',[Users::class, 'register']);
    Route::post('/user-get-otp',[Users::class, 'getOtp']);
    Route::post('/verify-otp',[Users::class, 'otpValidate']);

    /* Staff Login */
    Route::post('/staff-get-otp',[Staffs::class, 'getOtp']);
    Route::post('/staff-verify-otp',[Staffs::class, 'otpValidate']);

    Route::get('/get-languages',[Countries::class,'languages']);

    /*Admin Login Routes */
    Route::middleware(['auth:user-api','mobileplan'])->group(function(){
    // Route::post('/get-size',[Size::class,"index"]);
    Route::post('/logout',[Users::class,"userLogout"]);
    Route::post('/get-color', [Color::class, 'index']);
    Route::post('/add-color', [Color::class, 'store']);
    Route::post('/update-color', [Color::class, 'update']);
    Route::post('/delete-color', [Color::class, 'delete']);
    Route::post('/edit-color', [Color::class, 'edit']);

    /* Size (all including add,update and delete) routes */
    // Route::apiResource('/sizes',Size::class);
    Route::post('/size',[Size::class,"store"]);
    Route::post('/get-size',[Size::class,"index"]);

    /*Get TimeZone Format*/
    Route::get('/get-timezone',[TimezoneFormatSetting::class,'index']);

    /* Staff Login Routes */
    Route::post('/register-staff',[Staffs::class, 'register']);

    Route::post('/get-staffs',[Staffs::class, 'getStaffList']);
    Route::post('/edit-staff',[Staffs::class, 'editStaff']);
    Route::post('/update-staff',[Staffs::class, 'updateStaff']);
    Route::post('/get-staff-role',[Staffs::class, 'getStaffRole']);

    /* Country and Language Routes */
    Route::get('/get-countries',[Countries::class,'countries']);


        /* Roles and Permissions */
        Route::post('/get-roles-list',[Roles::class,'get_roles_list']);
        Route::post('/create-new-role',[Roles::class,'create_new_role']);
        Route::post('/add-privileges',[Roles::class,'add_role_privileges']);
        Route::post('/get-roles',[Roles::class,'getRoles']);
        Route::get('/get-modules',[Roles::class,'getModules']);

        /* Plan and Payments */
        Route::get('/get-plan',[ActivePlans::class, 'index']);
        Route::post('/new-plan-payment',[ActivePlans::class,'selectPlanType']);

        /* Company registration and workspace */
        Route::post('/company-settings',[CompanySettings::class,'registerCompany']);
        Route::post('/get-company-settings',[CompanySettings::class,'viewCompanyDetails']);
        Route::get('/workspace-type',[CompanySettings::class,'workspaceType']);
        Route::post('/create-workspace',[CompanySettings::class,'createWorkspace']);
        Route::post('/get-workspace',[CompanySettings::class,'getWorkspace']);

        /* To List All User/Staff Settings in Single API*/
        Route::post('/get-user-settings',[UserSettings::class,'getAllUsersettings']);
        Route::post('/get-staff-settings',[StaffSettings::class,'getAllStaffsettings']);

        /* User and Staff Preferences */
        Route::post('/user-preference',[UserSettings::class,'userPreference']);
        Route::post('/get-user-preference',[UserSettings::class,'getUserPreferences']);
        Route::get('/get-language-details',[Countries::class,'languages']);

          /*Update Language code */
     Route::post('/update-user-language',[UserSettings::class,'updateLanguageUsersettings']);
     Route::post('/update-staff-language',[StaffSettings::class,'updateLanguageStaffsettings']);

        /* User and Staff DashboardWidgets */
        Route::post('/add-dashboard-widgets',[UserSettings::class,'addDashboardWigets']);

        // Route::post('/staff-preference',[StaffSettings::class,'staffPreference']);
        // Route::post('/get-staff-preference',[StaffSettings::class,'getStaffPreferences']);
        Route::post('/get-notification-settings',[UserSettings::class,'getNotificationSettings']);
        Route::post('/notification-settings',[UserSettings::class,'notificationSettings']);
        // Route::post('/get-staff-notification-settings',[StaffSettings::class,'getNotificationSettings']);
        // Route::post('/staff-notification-settings',[StaffSettings::class,'notificationSettings']);
        Route::post('/get-email-settings',[UserSettings::class,'getEmailScheduleNotification']);
        Route::post('/email-settings',[UserSettings::class,'emailScheduleNotification']);
        // Route::post('/get-staff-email-settings',[StaffSettings::class,'getEmailScheduleNotification']);
         Route::post('/staff-email-settings',[StaffSettings::class,'emailScheduleNotification']);
        /*Email Schedule Task List*/
        Route::get('/get-emailscheduletask',[UserSettings::class,'getEmailScheduleTask']);

        /* Add Order Needed API's */
            /* Factory */
            Route::post('/get-user-factory',[Factories::class,'getFactory']);
            Route::post('/create-user-factory',[Factories::class,'createFactory']);

            /* Buyers */
            Route::post('/get-user-buyer',[Buyers::class,'getBuyers']);
            Route::post('/create-user-buyer',[Buyers::class,'createBuyer']);
            // Route::post('/get-staff-factory',[Factories::class,'getStaffFactory']);
            // Route::post('/create-staff-factory',[Factories::class,'createStaffFactory']);
            /* PCU's */
            Route::post('/get-user-pcu',[PCUs::class,'getPCU']);
            Route::post('/create-user-pcu',[PCUs::class,'createPCU']);
            // Route::post('/get-staff-pcu',[PCUs::class,'getStaffPCU']);
            // Route::post('/create-staff-pcu',[PCUs::class,'createStaffPCU']);
            /* Fabric Types */
            Route::post('/get-user-fabric',[FabricTypes::class,'getFabric']);
            Route::post('/create-user-fabric',[FabricTypes::class,'createFabric']);
            // Route::post('/get-staff-fabric',[FabricTypes::class,'getStaffFabric']);
            // Route::post('/create-staff-fabric',[FabricTypes::class,'createStaffFabric']);
            /* Categories */
            Route::post('/get-user-category',[OrderCategories::class,'getOrderCategory']);
            Route::post('/create-user-category',[OrderCategories::class,'createOrderCategory']);
            // Route::post('/get-staff-category',[OrderCategories::class,'getStaffOrderCategory']);
            // Route::post('/create-staff-category',[OrderCategories::class,'createStaffOrderCategory']);
            /* Article */
            Route::post('/get-user-article',[ArticleNames::class,'getArticle']);
            Route::post('/create-user-article',[ArticleNames::class,'createArticle']);
            // Route::post('/get-staff-article',[ArticleNames::class,'getStaffArticle']);
            // Route::post('/create-staff-article',[ArticleNames::class,'createStaffArticle']);
            /* Income Terms */
            Route::get('/get-income-terms',[IncomeTerms::class,'getIncomeTerms']);
            Route::get('/get-currencies',[Countries::class,'listCurrencies']);
            /* Basic Info */
            Route::post('/get-basic-info',[BasicInfo::class,'getBasicInfo']);

        /* Add Order */
        Route::post('/get-order-list',GetOrderRegistrationPageDropDownDetails::class);
        Route::post('/add-order',AddOrder::class);
        Route::post('/get-ordersku-list',GetOrderSkuDefaultColorandSize::class);
        Route::post('/add-order-sku',[AddOrderSKU::class,'addSku']);
        Route::post('/get-order-sku',[AddOrderSKU::class,'getSku']);
        Route::post('/add-order-contacts',[AddOrderContacts::class,'addContact']);
        Route::post('/get-order-contacts',[AddOrderContacts::class,'getContact']);
        Route::post('/get-order-contact',[AddOrderContacts::class,'getContacts']);
        Route::post('/add-order-production',[AddOrderProduction::class,'addProductionData']);
        Route::post('/get-order-production',[AddOrderProduction::class,'getProductionData']);
        Route::post('/create-order-template',[OrderTemplates::class,'createOrderTemplate']);
        Route::post('/get-order-template',[OrderTemplates::class,'getOrderTemplates']);
        Route::post('/create-task-data',[OrderTasks::class,'addTaskData']);
        Route::post('/get-task-data',[OrderTasks::class,'getTaskDetails']);
        Route::post('/update-task-data',[OrderTasks::class,'updateTaskData']);
        Route::post('/delete-task-data',[OrderTasks::class,'deleteTaskDetails']);
        /*Inprogress Percentage */
        Route::post('/update-inprogress-percentage',[OrderTasks::class,'update_inprogress_percentage']);
        Route::post('/inprogress-percentage-history',[OrderTasks::class,'inprogress_percentage_history']);
        /* Order Files */
        Route::post('/add-files',[OrderAddSpecs::class,'addAdditionalSpecs']);
        Route::post('/list-files',[OrderAddSpecs::class,'getUploadedFiles']);
        Route::post('/download-file',[OrderAddSpecs::class,'downloadFile']);
        Route::post('/delete-file',[OrderAddSpecs::class,'deleteFile']);
        /* Dashboard */
        Route::post('/dashboard-widgets',[DashBoard::class,'dashboardWidgets']);
        Route::post('/ongoing-list',[Dashboard::class,'onGoingList']);
        Route::post('/notification-dashboard',[Dashboard::class,'notifications']);

        /* Dashboard New */
        Route::post("/get-top-delay-tasks",[DashboardNew::class,'getTopDelayTask']);
        Route::post("/get-top-delay-prod",[DashboardNew::class,'getTopDelayProduction']);
        Route::post('/get-topdelay',[DashboardNew::class,'getTopDelayTaskandProduction']);
        Route::post('/newdashboard-widgets',[DashboardNew::class,'newDashboardWidgets']);
        Route::post('/get-order-status',[DashboardNew::class,'orderStatus']);
        Route::post('/new-dashboard-widgets',[DashboardNew::class,'dashboardWidgets']);
        Route::post('/get-order-task-prod-status',[DashboardNew::class,'getOrderTaskProductionStatus']);
        /* Holiday Settings */
        Route::post('/get-holidays',[HolidaySettings::class,'getHolidaySettings']);
        Route::post('/create-holidays',[HolidaySettings::class,'createHolidaySettings']);
        Route::post('/delete-holiday',[HolidaySettings::class,'deleteHolidaySettings']);
        /* WeekOffs */
        Route::post('/get-weekOffs',[WeekOffs::class,'getWeekOffs']);
        Route::post('/create-weekOffs',[WeekOffs::class,'createWeekOffs']);
        /* Edit Order */
        Route::post('/edit-order',[EditOrder::class,'getOrderDetails']);
        Route::post('/update-order',[EditOrder::class,'updateOrderDetails']);
        Route::post('/update-sku',[EditSKU::class,'editSku']);
        Route::post('/update-contacts',[EditContacts::class,'editContact']);
        Route::post('/update-order-tasks',[EditTasks::class,'editTaskData']);
        Route::post('/update-production-data',[EditProduction::class,'editProductionData']);
        /* Get Order */
        Route::post('/get-orders',[GetOrder::class,'getOrdersList']);
        Route::post('/get-orderdetails',GetOrderDetails::class);


        /* Get thr Style */
        Route::post('/get-styles',[GetStyle::class,'getStyles']);
        Route::post('/get-style',[GetStyle::class,'getTheStyle']);
        Route::post('/get-filter',[GetStyle::class,'getFilters']);

        /* Reschedule Task, Accomplished Task */
        Route::post('/reschedule-task',[RescheduleOrderTasks::class,'rescheduleTask']);
        Route::post('/get-reschedule-task',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
        Route::post('/accomplished-task',[OrderTasks::class,'accomplishedTask']);
        Route::post('/update-task',[OrderTasks::class,'updateTask']);
        Route::post('/reschedule-history',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
        Route::post('/add-subtask',[SubTask::class,'addSubTask']);
        Route::post('/delete-subtask',[SubTask::class,'deleteSubTask']);
        Route::post('/actual-start-date',[OrderTasks::class,'actualStartDate']);

        /* Data Input */
        Route::post('/get-calendar-data',[GetCalendarData::class,'getCalendarData']);
        Route::post('/add-data-input',[AddMultipleData::class,'addInputData']);
        Route::post('/add-data-input-excess',[AddMultipleData::class,'addInputDataAfterDateExceeded']);
        Route::post('/get-sku-data',[GetCalendarData::class,'getSkuForDate']);

        /* Inquiry Module */
        // Route::post('/inquiry-file-upload',[Inquiry::class,'inquiry_file_upload']);
        // Route::post('/save-inquiry',[Inquiry::class,'save_inquiry']);
        Route::post('/get-inquiry',[Inquiry::class,'get_inquirys']);
        Route::post('/inquiry-factory-response',[Inquiry::class,'inquiry_factory_response']);
        Route::post('/factory-get-inquiry',[Inquiry::class,'factory_get_inquirys']);
        Route::post('/save-inquiry-factory-response',[Inquiry::class,'save_inquiry_factory_response']);
        Route::post('/check-buyer-notification',[Inquiry::class,'check_buyer_notification']);
        Route::post('/check-factory-notification',[Inquiry::class,'check_factory_notification']);
        Route::post('/read-factory-notification',[Inquiry::class,'read_factory_notifications']);
        Route::post('/read-buyer-notification',[Inquiry::class,'read_buyer_notifications']);
        Route::post('/get-factory-list-response',[Inquiry::class,'get_factory_list_response']);
        Route::post('/save-buyer-inquiry-factory-response',[Inquiry::class,'save_buyer_inquiry_factory_response']);
        Route::post('/inquiry-details',[Inquiry::class,'inquiry_details']);
        Route::post('/inquiry-sku',[Inquiry::class,'inquiry_sku']);
        Route::post('/inquiry-media',[Inquiry::class,'inquiry_media']);
        Route::post('/get-inquiry-additional-info',[Inquiry::class,'get_inquiry_additional_info']);
        Route::post('/save-inquiry-contact',[Inquiry::class,'save_inquiry_contact']);
        Route::get('/get-inquiry-factory',[Inquiry::class,'get_inquiry_contact']);
        Route::post('/send-inquiry',[Inquiry::class,'send_inquiry']);
        Route::post('/inquiry-factory-list',[Inquiry::class,'inquiry_factory_list']);
        Route::post('/delete-inquiry',[Inquiry::class,'delete_inquiry']);
        // Route::post('/factory-inquiry-contact',[Inquiry::class,'factory_inquiry_contact']);
        // Route::post('/update-inquiry-contact',[Inquiry::class,'update_inquiry_contact']);
        // Route::post('/get-inquiry-master',[Inquiry::class,'get_inquiry_master']);
        // Route::post('/factory-inquiry-response',[Inquiry::class,'factory_inquiry_response']);
        // Route::post('/get-buyer-inquiry-list',[Inquiry::class,'get_buyer_inquiry_list']);
        // Route::post('/get-inquiry-factory-list',[Inquiry::class,'get_inquiry_factory_list']);
        // Route::post('/save-factory-feedback',[Inquiry::class,'save_factory_feedback']);
        // Route::post('/delete-inquiry-media',[Inquiry::class,'delete_inquiry_media']);
        // Route::post('/get-factory-ratings',[Inquiry::class,'get_factory_ratings']);
        // Route::post('/check-factory-feedback',[Inquiry::class,'check_factory_feedback']);
        Route::post('/label-file-upload',[InquiryChat::class,'label_file_upload']);
        Route::post('/add-label-content',[InquiryChat::class,'add_label_content']);
        Route::post('/get-inquiry-po-chat',[InquiryChat::class,'get_inquiry_po_chat']);
        Route::post('/download-po-inquiry-label',[InquiryChat::class,'download_po_inquiry_label']);
        Route::post('/label-file-delete',[InquiryChat::class,'label_file_delete']);
        Route::post('/get-label-inquiry-ids',[InquiryChat::class,'get_label_inquiry_ids']);
        Route::post('/get-label-content',[InquiryChat::class,'get_label_content']);
        Route::post('/edit-label-content',[InquiryChat::class,'edit_label_content']);

         /* Fabric Module */
        Route::post('/get-fabric-master',[Fabric::class,'get_fabric_master']);
        Route::post('/add-fabric-master-data',[Fabric::class,'add_fabric_master']);
        Route::post('/get-fabric-inquiry-ids',[Fabric::class,'get_fabric_inquiry_ids']);
        Route::post('/save-fabric-inquiry',[Fabric::class,'save_fabric_inquiry']);
        Route::post('/get-fabric-inquiry-list',[Fabric::class,'get_fabric_inquiry_list']);
        Route::post('/fabric-inquiry-details',[Fabric::class,'fabric_inquiry_details']);
        Route::post('/edit-fabric-inquiry',[Fabric::class,'update_fabric_inquiry']);
        Route::post('/send-fabric-inquiry',[Fabric::class,'send_fabric_inquiry']);
        Route::post('/add-fabric-contact',[Fabric::class,'add_fabric_contact']);
        Route::post('/get-fabric-contact',[Fabric::class,'get_fabric_contact']);
        Route::post('/fabric-supplier-list',[Fabric::class,'fabric_supplier_list']);
        Route::post('/inquiry-supplier-response',[Fabric::class,'inquiry_supplier_response']);
        Route::post('/inquiry-supplier-list',[Fabric::class,'inquiry_supplier_list']);
        Route::post('/delete-fabric-inquiry',[Fabric::class,'delete_fabric_inquiry']);
        Route::post('/get-supplier-list-response',[Fabric::class,'get_supplier_list_response']);
        Route::post('/save-fabric-inquiry-supplier-response',[Fabric::class,'save_fabric_inquiry_supplier_response']);
        Route::post('/get-inquiry-currency',[Fabric::class,'get_reference_inquiry_currency']);
        Route::post('/fabric-inquiry-pdf-download',[Fabric::class,'fabric_inquiry_pdf_download']);
        Route::post('/supplier-response-inquiries-download',[Fabric::class,'download_inquiry_supplier_response']);

        /* PO */
        Route::post('/generate-po',[PO::class,'generate_po_factory']);
        Route::post('/view-po',[PO::class,'view_company_po']);
        Route::post('/cancel-po',[PO::class,'cancel_po']);
        Route::post('/view-new-po',[PO::class,'view_all_po']);
        Route::post('/po-status-update',[PO::class,'update_po_status']);
        Route::post('/delete-po',[PO::class,'delete_po']);
        Route::post('/download-po-pdf',[PO::class,'generate_multiple_po_pdf_new']);
        Route::post('/get-multiple-po-details',[PO::class,'get_po_multiple']);
        Route::post('/add-po-comments',[PO::class,'addPOComments']);
        Route::post('/get-po-comments-details',[PO::class,'getPOComments']);
        Route::post('/delete-po-comments-file',[PO::class,'delete_po_comments_file']);
        Route::post('/add-po-comments-audio-file',[PO::class,'addPOCommentsAudioFile']);
        Route::post('/upload-media-po',[PO::class,'upload_file_po']);
        /* Start Tech Pack */
        Route::post('/view-techpack',[TeckPackDetail::class,'viewTeckPack']);
        Route::post('/download-techpackPDF',[TeckPackDetail::class,'downloadTeckPackPDF']);
        Route::post('/delete-techpack',[TeckPackDetail::class,'deleteTeckPackDetails']);
        Route::post('/publish-techpack',[TeckPackDetail::class,'publishTeckPack']);
        Route::post('/get-techpack-details',[TeckPackDetail::class,'getTeckPackDetails']);
        Route::post('/add-techpack-comments',[TeckPackDetail::class,'addTechPackComments']);
        Route::post('/get-techpack-comments-details',[TeckPackDetail::class,'getTechPackCommentsDetails']);
        Route::post('/update-techpack-comments-details',[TeckPackDetail::class,'updateTechPackCommentsDetails']);

        Route::post('/add-new-techpack-comments',[TeckPackDetail::class,'addNewTechPackComments']);
        Route::post('/get-new-techpack-comments-details',[TeckPackDetail::class,'getNewTechPackCommentsDetails']);
        Route::post('/download-new-techpackPDF',[TeckPackDetail::class,'downloadNewTechPackPDF']);
        Route::post('/update-techpack-comments-read-status',[TeckPackDetail::class,'UpdateCommentsReadStatus']);
        Route::post('/get-techpack-comments-notifications',[TeckPackDetail::class,'getCommentsNotifications']);
        Route::post('/upload-techpack-file',[TeckPackDetail::class,'addTechPackFile']);
        Route::post('/get-edit-techpack-info',[TeckPackDetail::class,'getEditTechpackInfo']);
        Route::post('/add-audio-file',[TeckPackDetail::class,'addAudioFile']);
        Route::post('/add-techpack-audio-file',[TeckPackDetail::class,'addTechpackAudioFile']);
        /* End Tech Pack */
    });

  /*Admin Login Routes */
    Route::middleware(['auth:staff-api'])->prefix('staff')->group(function(){
        Route::post('/get-size',[Size::class,"index"]);
        Route::post('/logout',[Staffs::class,"staffLogout"]);

        Route::post('/get-color', [Color::class, 'index']);
        Route::post('/add-color', [Color::class, 'store']);
        Route::post('/update-color', [Color::class, 'update']);
        Route::post('/delete-color', [Color::class, 'delete']);
        Route::post('/edit-color', [Color::class, 'edit']);

        /* Size (all including add,update and delete) routes */
        // Route::apiResource('/sizes',Size::class);
        Route::post('/size',[Size::class,"store"]);
        Route::post('/get-size',[Size::class,"index"]);



        /* Staff Login Routes */
        Route::post('/register-staff',[Staffs::class, 'register']);
        Route::post('/get-staffs',[Staffs::class, 'getStaffList']);
        Route::post('/edit-staff',[Staffs::class, 'editStaff']);
        Route::post('/update-staff',[Staffs::class, 'updateStaff']);
        Route::post('/get-staff-role',[Staffs::class, 'getStaffRole']);

        /* Country and Language Routes */
        Route::get('/get-countries',[Countries::class,'countries']);
        //Route::get('/get-languages',[Countries::class,'languages']);

        /*Get TimeZone Format*/
        Route::get('/get-timezone',[TimezoneFormatSetting::class,'index']);

        /* Roles and Permissions */
        Route::post('/get-roles-list',[Roles::class,'get_roles_list']);
        Route::post('/create-new-role',[Roles::class,'create_new_role']);
        Route::post('/add-privileges',[Roles::class,'add_role_privileges']);
        Route::post('/get-roles',[Roles::class,'getRoles']);
        Route::get('/get-modules',[Roles::class,'getModules']);

        /* Plan and Payments */
        Route::get('/get-plan',[ActivePlans::class, 'index']);
        Route::post('/new-plan-payment',[ActivePlans::class,'selectPlanType']);

        /* Company registration and workspace */
        Route::post('/company-settings',[CompanySettings::class,'registerCompany']);
        Route::post('/get-company-settings',[CompanySettings::class,'viewCompanyDetails']);
        Route::get('/workspace-type',[CompanySettings::class,'workspaceType']);
        Route::post('/create-workspace',[CompanySettings::class,'createWorkspace']);
        Route::post('/get-workspace',[CompanySettings::class,'getWorkspace']);


        /* To List All User/Staff Settings in Single API*/
        Route::post('/get-user-settings',[UserSettings::class,'getAllUsersettings']);
        Route::post('/get-staff-settings',[StaffSettings::class,'getAllStaffsettings']);

        /*Update Language code */
        Route::post('/update-user-language',[UserSettings::class,'updateLanguageUsersettings']);
        Route::post('/update-staff-language',[StaffSettings::class,'updateLanguageStaffsettings']);

        /* User and Staff Preferences */
        Route::post('/user-preference',[UserSettings::class,'userPreference']);
        Route::post('/get-user-preference',[UserSettings::class,'getUserPreferences']);
        Route::post('/staff-preference',[StaffSettings::class,'staffPreference']);

        Route::get('/get-language-details',[Countries::class,'languages']);
        /* User and Staff DashboardWidgets */
        Route::post('/add-dashboard-widgets',[UserSettings::class,'addDashboardWigets']);
        // Route::post('/get-staff-preference',[StaffSettings::class,'getStaffPreferences']);
        Route::post('/get-notification-settings',[UserSettings::class,'getNotificationSettings']);
        Route::post('/notification-settings',[UserSettings::class,'notificationSettings']);
        // Route::post('/get-staff-notification-settings',[StaffSettings::class,'getNotificationSettings']);
        Route::post('/staff-notification-settings',[StaffSettings::class,'notificationSettings']);
        Route::post('/get-email-settings',[UserSettings::class,'getEmailScheduleNotification']);
        Route::post('/email-settings',[UserSettings::class,'emailScheduleNotification']);
        // Route::post('/get-staff-email-settings',[StaffSettings::class,'getEmailScheduleNotification']);
        Route::post('/staff-email-settings',[StaffSettings::class,'emailScheduleNotification']);
        /*Email Schedule Task List*/
        Route::get('/get-emailscheduletask',[UserSettings::class,'getEmailScheduleTask']);

        /* Add Order Needed API's */
        /* Factory */
        Route::post('/get-user-factory',[Factories::class,'getFactory']);
        Route::post('/create-user-factory',[Factories::class,'createFactory']);

        /* Buyers */
        Route::post('/get-user-buyer',[Buyers::class,'getBuyers']);
        Route::post('/create-user-buyer',[Buyers::class,'createBuyer']);
        // Route::post('/get-staff-factory',[Factories::class,'getStaffFactory']);
        // Route::post('/create-staff-factory',[Factories::class,'createStaffFactory']);
        /* PCU's */
        Route::post('/get-user-pcu',[PCUs::class,'getPCU']);
        Route::post('/create-user-pcu',[PCUs::class,'createPCU']);
        // Route::post('/get-staff-pcu',[PCUs::class,'getStaffPCU']);
        // Route::post('/create-staff-pcu',[PCUs::class,'createStaffPCU']);
        /* Fabric Types */
        Route::post('/get-user-fabric',[FabricTypes::class,'getFabric']);
        Route::post('/create-user-fabric',[FabricTypes::class,'createFabric']);
        // Route::post('/get-staff-fabric',[FabricTypes::class,'getStaffFabric']);
        // Route::post('/create-staff-fabric',[FabricTypes::class,'createStaffFabric']);
        /* Categories */
        Route::post('/get-user-category',[OrderCategories::class,'getOrderCategory']);
        Route::post('/create-user-category',[OrderCategories::class,'createOrderCategory']);
        // Route::post('/get-staff-category',[OrderCategories::class,'getStaffOrderCategory']);
        // Route::post('/create-staff-category',[OrderCategories::class,'createStaffOrderCategory']);
        /* Article */
        Route::post('/get-user-article',[ArticleNames::class,'getArticle']);
        Route::post('/create-user-article',[ArticleNames::class,'createArticle']);
        // Route::post('/get-staff-article',[ArticleNames::class,'getStaffArticle']);
        // Route::post('/create-staff-article',[ArticleNames::class,'createStaffArticle']);
        /* Income Terms */
        Route::get('/get-income-terms',[IncomeTerms::class,'getIncomeTerms']);
        Route::get('/get-currencies',[Countries::class,'listCurrencies']);
        /* Basic Info */
        Route::post('/get-basic-info',[BasicInfo::class,'getBasicInfo']);

        /* Add Order */
        Route::post('/get-order-list',GetOrderRegistrationPageDropDownDetails::class);
        Route::post('/add-order',AddOrder::class);
        Route::post('/get-ordersku-list',GetOrderSkuDefaultColorandSize::class);
        Route::post('/add-order-sku',[AddOrderSKU::class,'addSku']);
        Route::post('/get-order-sku',[AddOrderSKU::class,'getSku']);
        Route::post('/add-order-contacts',[AddOrderContacts::class,'addContact']);
        Route::post('/get-order-contacts',[AddOrderContacts::class,'getContact']);
        Route::post('/get-order-contact',[AddOrderContacts::class,'getContacts']);
        Route::post('/add-order-production',[AddOrderProduction::class,'addProductionData']);
        Route::post('/get-order-production',[AddOrderProduction::class,'getProductionData']);
        Route::post('/create-order-template',[OrderTemplates::class,'createOrderTemplate']);
        Route::post('/get-order-template',[OrderTemplates::class,'getOrderTemplates']);
        Route::post('/create-task-data',[OrderTasks::class,'addTaskData']);
        Route::post('/get-task-data',[OrderTasks::class,'getTaskDetails']);
        Route::post('/update-task-data',[OrderTasks::class,'updateTaskData']);
        Route::post('/delete-task-data',[OrderTasks::class,'deleteTaskDetails']);
        /*Inprogress Percentage */
        Route::post('/update-inprogress-percentage',[OrderTasks::class,'update_inprogress_percentage']);
        Route::post('/inprogress-percentage-history',[OrderTasks::class,'inprogress_percentage_history']);
        /* Order Files */
        Route::post('/add-files',[OrderAddSpecs::class,'addAdditionalSpecs']);
        Route::post('/list-files',[OrderAddSpecs::class,'getUploadedFiles']);
        Route::post('/download-file',[OrderAddSpecs::class,'downloadFile']);
        Route::post('/delete-file',[OrderAddSpecs::class,'deleteFile']);
        /* Dashboard */
        Route::post('/dashboard-widgets',[DashBoard::class,'dashboardWidgets']);
        Route::post('/ongoing-list',[Dashboard::class,'onGoingList']);
        Route::post('/notification-dashboard',[Dashboard::class,'notifications']);
        /* Dashboard New */
        Route::post("/get-top-delay-tasks",[DashboardNew::class,'getTopDelayTask']);
        Route::post("/get-top-delay-prod",[DashboardNew::class,'getTopDelayProduction']);
        Route::post('/get-topdelay',[DashboardNew::class,'getTopDelayTaskandProduction']);
        Route::post('/newdashboard-widgets',[DashboardNew::class,'newDashboardWidgets']);
        Route::post('/get-order-status',[DashboardNew::class,'orderStatus']);
        Route::post('/new-dashboard-widgets',[DashboardNew::class,'dashboardWidgets']);
        Route::post('/get-order-task-prod-status',[DashboardNew::class,'getOrderTaskProductionStatus']);
        /* Holiday Settings */
        Route::post('/get-holidays',[HolidaySettings::class,'getHolidaySettings']);
        Route::post('/create-holidays',[HolidaySettings::class,'createHolidaySettings']);
        Route::post('/delete-holiday',[HolidaySettings::class,'deleteHolidaySettings']);
        /* WeekOffs */
        Route::post('/get-weekOffs',[WeekOffs::class,'getWeekOffs']);
        Route::post('/create-weekOffs',[WeekOffs::class,'createWeekOffs']);
        /* Edit Order */
        Route::post('/edit-order',[EditOrder::class,'getOrderDetails']);
        Route::post('/update-order',[EditOrder::class,'updateOrderDetails']);
        Route::post('/update-sku',[EditSKU::class,'editSku']);
        Route::post('/update-contacts',[EditContacts::class,'editContact']);
        Route::post('/update-order-tasks',[EditTasks::class,'editTaskData']);
        Route::post('/update-production-data',[EditProduction::class,'editProductionData']);
        /* Get Order */
        Route::post('/get-orders',[GetOrder::class,'getOrdersList']);
        Route::post('/get-orderdetails',GetOrderDetails::class);


        /* Get thr Style */
        Route::post('/get-styles',[GetStyle::class,'getStyles']);
        Route::post('/get-style',[GetStyle::class,'getTheStyle']);
        Route::post('/get-filter',[GetStyle::class,'getFilters']);

        /* Reschedule Task, Accomplished Task */
        Route::post('/reschedule-task',[RescheduleOrderTasks::class,'rescheduleTask']);
        Route::post('/get-reschedule-task',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
        Route::post('/accomplished-task',[OrderTasks::class,'accomplishedTask']);
        Route::post('/update-task',[OrderTasks::class,'updateTask']);
        Route::post('/reschedule-history',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
        Route::post('/add-subtask',[SubTask::class,'addSubTask']);
        Route::post('/delete-subtask',[SubTask::class,'deleteSubTask']);
        Route::post('/actual-start-date',[OrderTasks::class,'actualStartDate']);

        /* Data Input */
        Route::post('/get-calendar-data',[GetCalendarData::class,'getCalendarData']);
        Route::post('/add-data-input',[AddMultipleData::class,'addInputData']);
        Route::post('/add-data-input-excess',[AddMultipleData::class,'addInputDataAfterDateExceeded']);
        Route::post('/get-sku-data',[GetCalendarData::class,'getSkuForDate']);

        /* Inquiry Module */
        // Route::post('/inquiry-file-upload',[Inquiry::class,'inquiry_file_upload']);
        // Route::post('/save-inquiry',[Inquiry::class,'save_inquiry']);
        Route::post('/get-inquiry',[Inquiry::class,'get_inquirys']);
        Route::post('/inquiry-factory-response',[Inquiry::class,'inquiry_factory_response']);
        Route::post('/factory-get-inquiry',[Inquiry::class,'factory_get_inquirys']);
        Route::post('/save-inquiry-factory-response',[Inquiry::class,'save_inquiry_factory_response']);
        Route::post('/check-buyer-notification',[Inquiry::class,'check_buyer_notification']);
        Route::post('/check-factory-notification',[Inquiry::class,'check_factory_notification']);
        Route::post('/read-factory-notification',[Inquiry::class,'read_factory_notifications']);
        Route::post('/read-buyer-notification',[Inquiry::class,'read_buyer_notifications']);
        Route::post('/get-factory-list-response',[Inquiry::class,'get_factory_list_response']);
        Route::post('/save-buyer-inquiry-factory-response',[Inquiry::class,'save_buyer_inquiry_factory_response']);
        Route::post('/inquiry-details',[Inquiry::class,'inquiry_details']);
        Route::post('/inquiry-sku',[Inquiry::class,'inquiry_sku']);
        Route::post('/inquiry-media',[Inquiry::class,'inquiry_media']);
        Route::post('/get-inquiry-additional-info',[Inquiry::class,'get_inquiry_additional_info']);
        Route::post('/save-inquiry-contact',[Inquiry::class,'save_inquiry_contact']);
        Route::get('/get-inquiry-factory',[Inquiry::class,'get_inquiry_contact']);
        Route::post('/send-inquiry',[Inquiry::class,'send_inquiry']);
        Route::post('/inquiry-factory-list',[Inquiry::class,'inquiry_factory_list']);
        Route::post('/delete-inquiry',[Inquiry::class,'delete_inquiry']);
        Route::post('/label-file-upload',[InquiryChat::class,'label_file_upload']);
        Route::post('/add-label-content',[InquiryChat::class,'add_label_content']);
        Route::post('/get-inquiry-po-chat',[InquiryChat::class,'get_inquiry_po_chat']);
        Route::post('/download-po-inquiry-label',[InquiryChat::class,'download_po_inquiry_label']);
        Route::post('/label-file-delete',[InquiryChat::class,'label_file_delete']);
        Route::post('/get-label-inquiry-ids',[InquiryChat::class,'get_label_inquiry_ids']);
        Route::post('/get-label-content',[InquiryChat::class,'get_label_content']);
        Route::post('/edit-label-content',[InquiryChat::class,'edit_label_content']);

        /* Fabric Module */
        Route::post('/get-fabric-master',[Fabric::class,'get_fabric_master']);
        Route::post('/add-fabric-master-data',[Fabric::class,'add_fabric_master']);
        Route::post('/get-fabric-inquiry-ids',[Fabric::class,'get_fabric_inquiry_ids']);
        Route::post('/save-fabric-inquiry',[Fabric::class,'save_fabric_inquiry']);
        Route::post('/get-fabric-inquiry-list',[Fabric::class,'get_fabric_inquiry_list']);
        Route::post('/fabric-inquiry-details',[Fabric::class,'fabric_inquiry_details']);
        Route::post('/edit-fabric-inquiry',[Fabric::class,'update_fabric_inquiry']);
        Route::post('/send-fabric-inquiry',[Fabric::class,'send_fabric_inquiry']);
        Route::post('/add-fabric-contact',[Fabric::class,'add_fabric_contact']);
        Route::post('/get-fabric-contact',[Fabric::class,'get_fabric_contact']);
        Route::post('/fabric-supplier-list',[Fabric::class,'fabric_supplier_list']);
        Route::post('/inquiry-supplier-response',[Fabric::class,'inquiry_supplier_response']);
        Route::post('/inquiry-supplier-list',[Fabric::class,'inquiry_supplier_list']);
        Route::post('/delete-fabric-inquiry',[Fabric::class,'delete_fabric_inquiry']);
        Route::post('/get-supplier-list-response',[Fabric::class,'get_supplier_list_response']);
        Route::post('/save-fabric-inquiry-supplier-response',[Fabric::class,'save_fabric_inquiry_supplier_response']);
        Route::post('/get-inquiry-currency',[Fabric::class,'get_reference_inquiry_currency']);
        Route::post('/fabric-inquiry-pdf-download',[Fabric::class,'fabric_inquiry_pdf_download']);
        Route::post('/supplier-response-inquiries-download',[Fabric::class,'download_inquiry_supplier_response']);

        /* PO */
        Route::post('/generate-po',[PO::class,'generate_po_factory']);
        Route::post('/view-po',[PO::class,'view_company_po']);
        Route::post('/cancel-po',[PO::class,'cancel_po']);
        Route::post('/view-new-po',[PO::class,'view_all_po']);
        Route::post('/po-status-update',[PO::class,'update_po_status']);
        Route::post('/delete-po',[PO::class,'delete_po']);
        Route::post('/download-po-pdf',[PO::class,'generate_multiple_po_pdf_new']);
        Route::post('/get-multiple-po-details',[PO::class,'get_po_multiple']);
        Route::post('/add-po-comments',[PO::class,'addPOComments']);
        Route::post('/get-po-comments-details',[PO::class,'getPOComments']);
        Route::post('/delete-po-comments-file',[PO::class,'delete_po_comments_file']);
        Route::post('/add-po-comments-audio-file',[PO::class,'addPOCommentsAudioFile']);
        Route::post('/upload-media-po',[PO::class,'upload_file_po']);

        /* Start Tech Pack */
        Route::post('/view-techpack',[TeckPackDetail::class,'viewTeckPack']);
        Route::post('/download-techpackPDF',[TeckPackDetail::class,'downloadTeckPackPDF']);
        Route::post('/delete-techpack',[TeckPackDetail::class,'deleteTeckPackDetails']);
        Route::post('/publish-techpack',[TeckPackDetail::class,'publishTeckPack']);
        Route::post('/get-techpack-details',[TeckPackDetail::class,'getTeckPackDetails']);
        Route::post('/add-techpack-comments',[TeckPackDetail::class,'addTechPackComments']);
        Route::post('/get-techpack-comments-details',[TeckPackDetail::class,'getTechPackCommentsDetails']);
        Route::post('/update-techpack-comments-details',[TeckPackDetail::class,'updateTechPackCommentsDetails']);

        Route::post('/add-new-techpack-comments',[TeckPackDetail::class,'addNewTechPackComments']);
        Route::post('/get-new-techpack-comments-details',[TeckPackDetail::class,'getNewTechPackCommentsDetails']);
        Route::post('/download-new-techpackPDF',[TeckPackDetail::class,'downloadNewTechPackPDF']);
        Route::post('/update-techpack-comments-read-status',[TeckPackDetail::class,'UpdateCommentsReadStatus']);
        Route::post('/get-techpack-comments-notifications',[TeckPackDetail::class,'getCommentsNotifications']);
        Route::post('/upload-techpack-file',[TeckPackDetail::class,'addTechPackFile']);
        Route::post('/get-edit-techpack-info',[TeckPackDetail::class,'getEditTechpackInfo']);
        Route::post('/add-audio-file',[TeckPackDetail::class,'addAudioFile']);
        Route::post('/add-techpack-audio-file',[TeckPackDetail::class,'addTechpackAudioFile']);
        /* End Tech Pack */
    });



});
