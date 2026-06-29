<?php
use App\Http\Controllers\WebSite\Order\PendingTasks\DownloadPDF;
use App\Http\Controllers\WebSite\Auth\Roles;
use App\Http\Controllers\WebSite\Auth\Staffs;
use App\Http\Controllers\WebSite\Auth\Users;
use App\Http\Controllers\WebSite\Common\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebSite\Common\Color;
use App\Http\Controllers\WebSite\Common\Countries;
use App\Http\Controllers\WebSite\Common\DashBoard;
use App\Http\Controllers\WebSite\Common\DashboardNew;
use App\Http\Controllers\WebSite\Common\DashboardWidgetsSettings;
use App\Http\Controllers\WebSite\Common\EmailConfigs;
use App\Http\Controllers\WebSite\Common\HolidaySettings;
use App\Http\Controllers\WebSite\Common\PlanValidation;
use App\Http\Controllers\WebSite\Company\CompanySettings;
use App\Http\Controllers\WebSite\Order\AddOrder;
use App\Http\Controllers\WebSite\Order\AddOrderContacts;
use App\Http\Controllers\WebSite\Order\AddOrderProduction;
use App\Http\Controllers\WebSite\Order\AddOrderSKU;
use App\Http\Controllers\WebSite\Order\ArticleNames;
use App\Http\Controllers\WebSite\Order\EditOrders\EditOrder;
use App\Http\Controllers\WebSite\Order\FabricTypes;
use App\Http\Controllers\WebSite\Order\Factories;
use App\Http\Controllers\WebSite\Order\IncomeTerms;
use App\Http\Controllers\WebSite\Order\OrderCategories;
use App\Http\Controllers\WebSite\Order\OrderTasks;
use App\Http\Controllers\WebSite\Order\OrderTemplates;
use App\Http\Controllers\WebSite\Order\PCUs;
use App\Http\Controllers\WebSite\User\Register;
use App\Http\Controllers\WebSite\Plan\ActivePlans;
use App\Http\Controllers\WebSite\Common\UserSettings;
use App\Http\Controllers\WebSite\Common\StaffSettings;
use App\Http\Controllers\WebSite\Common\WeekOffs;
use App\Http\Controllers\WebSite\Order\BasicInfo;
use App\Http\Controllers\WebSite\Order\OrderAddSpecs;
use App\Http\Controllers\WebSite\Order\Buyers;
use App\Http\Controllers\WebSite\Order\DataInput\AddInputData;
use App\Http\Controllers\WebSite\Order\DataInput\AddInputMultipleData;
use App\Http\Controllers\WebSite\Order\DataInput\GetData;
use App\Http\Controllers\WebSite\Order\EditOrders\EditContacts;
use App\Http\Controllers\WebSite\Order\EditOrders\EditProduction;
use App\Http\Controllers\WebSite\Order\EditOrders\EditSKU;
use App\Http\Controllers\WebSite\Order\EditOrders\EditTasks;
use App\Http\Controllers\WebSite\Order\GetOrder\GetOrder;
use App\Http\Controllers\WebSite\Order\GetOrder\GetStyle;
use App\Http\Controllers\WebSite\Order\PendingTasks\DownloadProductionPDF;
use App\Http\Controllers\WebSite\Order\PendingTasks\GetOrders;
use App\Http\Controllers\WebSite\Order\PendingTasks\PendingProduction;
use App\Http\Controllers\WebSite\Order\PendingTasks\PendingTask;
use App\Http\Controllers\WebSite\Order\RescheduleOrderTasks;
use App\Http\Controllers\WebSite\Order\UpdateOrderActionData;
use App\Http\Controllers\WebSite\Order\ViewOrder;
use App\Http\Controllers\WebSite\Order\OrderFeedback;
use App\Http\Controllers\WebSite\Chats\Chats;
use App\Http\Controllers\WebSite\OrderStatus\OrderStatusDownloadPDF;
use App\Http\Controllers\WebSite\OrderStatus\PartialDeliveries;
use App\Http\Controllers\WebSite\Reports\DailyProdReports;
use App\Http\Controllers\WebSite\Reports\DailyProdReportsDownload;
use App\Http\Controllers\WebSite\Reports\ProductionReportDownload;
use App\Http\Controllers\WebSite\Reports\ProductionReports;
use App\Http\Controllers\WebSite\Reports\TaskFilters;
use App\Http\Controllers\WebSite\Reports\TaskReportDownload;
use App\Http\Controllers\WebSite\Reports\TaskReports;
use App\Http\Controllers\WebSite\Common\TimeZoneFormatSettings;
use App\Http\Controllers\WebSite\Order\SubTask;
use App\Http\Controllers\WebSite\Reports\OrderReport;
use App\Http\Controllers\WebSite\Reports\OrderReportDownload;
use App\Http\Controllers\WebSite\Reports\SKUDailyProdReportDownload;
use App\Http\Controllers\WebSite\Reports\SKUDailyProdReports;
use App\Http\Controllers\WebSite\Inquiry\Inquiry;
use App\Http\Controllers\WebSite\Inquiry\DownloadInquiries;
use App\Http\Controllers\WebSite\Fabric\Fabric;
use App\Http\Controllers\WebSite\Fabric\FabricCompositions;
use App\Http\Controllers\WebSite\Order\GetAllInOneOrder;
use App\Http\Controllers\WebSite\PurchaseOrder\PO;
use App\Http\Controllers\WebSite\Inquiry\InquiryChat;
use App\Http\Controllers\WebSite\SAM\Sam;
use App\Http\Controllers\WebSite\Order\OrderMaterialsLabel;
use App\Http\Controllers\PdfChart;
use App\Http\Controllers\WebSite\Inquiry\Chatbox;
use App\Http\Controllers\WebSite\Order\OrderApprovalHistoryLog;
use App\Http\Controllers\WebSite\Order\OrderTaskExcelUpload;
use App\Http\Controllers\WebSite\PurchaseOrder\POrder;
use App\Http\Controllers\WebSite\TechPack\TechPackDetail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::group(['prefix' => 'v1'], function(){
/* User/Admin Login Routes */
Route::post('/register-user',[Users::class, 'register']);
Route::post('/user-get-otp',[Users::class, 'getOtp']);
Route::post('/verify-otp',[Users::class, 'otpValidate']);

/* Staff Login Routes */

Route::post('/staff-get-otp',[Staffs::class, 'getOtp']);
Route::post('/staff-verify-otp',[Staffs::class, 'otpValidate']);
Route::post('/get-staffs',[Staffs::class, 'getStaffList']);


/* Country and Language Routes */
Route::get('/get-countries',[Countries::class,'countries']);
Route::get('/get-languages',[Countries::class,'languages']);

/*Get TimeZone Format*/
Route::get('/get-timezone',[TimeZoneFormatSettings::class,'index']);

/* Plan and Payments */
Route::get('/get-plan',[ActivePlans::class, 'index']);
Route::post('/new-plan-payment',[ActivePlans::class,'selectPlanType']);
/* Workspace Type */
Route::get('/workspace-type',[CompanySettings::class,'workspaceType']);
/* Plan Price Payment */
Route::post('/confirm-plan-payment',[ActivePlans::class,'paymentUpdation']);

Route::post('/download-files',[OrderMaterialsLabel::class,'download_files']);
Route::get('/download-ms-file',[OrderMaterialsLabel::class,'download_ms_files']);

Route::middleware('auth:user-api')->group(function(){
    // Route::post('/get-size',[Size::class,"index"]);
    Route::post('/register-staff',[Staffs::class, 'register']);
    Route::post('/register-staffprofile',[Staffs::class, 'registerWithImage']);
    Route::post('/edit-staff',[Staffs::class, 'editStaff']);
    Route::post('/edit-staffprofile',[Staffs::class, 'editStaffProfile']);
    Route::post('/update-staff',[Staffs::class, 'updateStaff']);
    Route::post('/update-staffprofile',[Staffs::class, 'updateStaffProfile']);
    Route::post('/logout',[Users::class,"userLogout"]);

    Route::post('/get-color', [Color::class, 'index']);
    Route::post('/add-color', [Color::class, 'store']);
    Route::post('/update-color', [Color::class, 'update']);
    Route::post('/delete-color', [Color::class, 'delete']);
    Route::post('/edit-color', [Color::class, 'edit']);

    /* Size (all including add,update and delete) routes */
   // Route::apiResource('/size',Size::class);
    Route::post('/get-size-category',[Size::class,"get_size_categories"]);
    Route::post('/add-size-category',[Size::class,"add_size_category"]);
    Route::post('/size',[Size::class,"store"]);
    Route::post('/get-size',[Size::class,"index"]);

    /* Roles and Permissions */
    Route::post('/get-roles-list',[Roles::class,'get_roles_list']);
    Route::post('/create-new-role',[Roles::class,'create_new_role']);
    Route::post('/add-privileges',[Roles::class,'add_role_privileges']);
    Route::post('/get-roles',[Roles::class,'getRoles']);
    Route::get('/get-modules',[Roles::class,'getModules']);


    /* Company registration and workspace */
    Route::post('/company-settings',[CompanySettings::class,'registerCompany']);
    Route::post('/get-company-settings',[CompanySettings::class,'viewCompanyDetails']);
    Route::post('/create-workspace',[CompanySettings::class,'createWorkspace']);
    Route::post('/get-workspace',[CompanySettings::class,'getWorkspace']);
    Route::post('/update-company-settings',[CompanySettings::class,'updateCompanyDetails']);
    Route::post('/update-default-logo',[CompanySettings::class,'changeDefaultLogo']);

    /* User and Staff Preferences */
    Route::post('/user-preference',[UserSettings::class,'userPreference']);
    Route::post('/get-user-preference',[UserSettings::class,'getUserPreferences']);
    // Route::post('/staff-preference',[StaffSettings::class,'staffPreference']);
    // Route::post('/get-staff-preference',[StaffSettings::class,'getStaffPreferences']);
    Route::post('/get-notification-settings',[UserSettings::class,'getNotificationSettings']);
    Route::post('/notification-settings',[UserSettings::class,'notificationSettings']);
    // Route::post('/get-staff-notification-settings',[StaffSettings::class,'getNotificationSettings']);
    // Route::post('/staff-notification-settings',[StaffSettings::class,'notificationSettings']);
    Route::post('/get-email-settings',[UserSettings::class,'getEmailScheduleNotification']);
    Route::post('/email-settings',[UserSettings::class,'emailScheduleNotification']);
    Route::post('/email-settings-order-ids',[UserSettings::class,'emailScheduleReportsOrderIds']);
    Route::post('/get-email-settings-order-ids',[UserSettings::class,'get_emailScheduleReportsOrderIds']);
    // Route::post('/get-staff-email-settings',[StaffSettings::class,'getEmailScheduleNotification']);
    // Route::post('/staff-email-settings',[StaffSettings::class,'emailScheduleNotification']);
    Route::post('/get-dashboard-settings',[UserSettings::class,'dashboardNotifications']);
    Route::post('/add-dashboard-settings',[UserSettings::class,'addDashboardNotifications']);
    /* Widgets Orders */
    Route::post('/get-dashboard-settings-orders',[DashboardWidgetsSettings::class,'getWidgetOrders']);
    Route::post('/add-dashboard-settings-orders',[DashboardWidgetsSettings::class,'addDashboardOrders']);
    /* New DashBoard Widgets */
    Route::post('/get-tasks-status',[DashboardNew::class,'getTaskStatus']);
    Route::post('/get-top-delay-tasks',[DashboardNew::class,'getTopDelayTask']);
    Route::post('/get-prod-status',[DashboardNew::class,'getProductionStatus']);
    Route::post('/get-top-delay-prod',[DashboardNew::class,'getTopDelayProduction']);
    Route::post('/order-status-dashboard',[DashboardNew::class,'orderStatus']);
    Route::post('/get-plan-validity',[DashboardNew::class,'getPlanValidityDays']);
    Route::post('/new-dashboard-widgets',[DashboardNew::class,'dashboardWidgets']);
    Route::post('/plan-status',[ActivePlans::class,'getPlanStatus']);
    Route::post('/recent-order-details',[DashboardNew::class,'dashboardRecentOrderDetails']);
    Route::post('/staff-invite-details',[DashboardNew::class,'staffInviteDetails']);
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


    /*Fabric Compositions */
    Route::post('/get-fabric-composition',[FabricCompositions::class,'getFabricComposition']);
    Route::post('/create-fabric-composition',[FabricCompositions::class,'createFabricComposition']);


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
    /* Order Registration- All in One */
    Route::post('/get-all-order-details',[GetAllInOneOrder::class,'getOrderAllDetails']);
    Route::post('/get-all-sku',[GetAllInOneOrder::class,'getSizeAndColor']);
    /* Add Order */
    Route::post('/add-order',AddOrder::class);
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
    Route::post('/add-order-comments',[AddOrder::class,'addComments']);
    /* Order Files */
    Route::post('/add-files',[OrderAddSpecs::class,'addAdditionalSpecs']);
    Route::post('/list-files',[OrderAddSpecs::class,'getUploadedFiles']);
    Route::post('/download-file',[OrderAddSpecs::class,'downloadFile']);
    Route::post('/delete-file',[OrderAddSpecs::class,'deleteFile']);
    /* Dashboard */
    Route::post('/dashboard-widgets',[DashBoard::class,'dashboardWidgets']);
    Route::post('/ongoing-list',[Dashboard::class,'onGoingList']);
    Route::post('/notification-dashboard',[Dashboard::class,'notifications']);
    Route::post('/dashboard-taskstatus',[Dashboard::class,'DashboardTaskStatus']);
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
    Route::post('/update-order-comments',[EditOrder::class,'updateComments']);
    Route::post('/delete-order-comments-file',[EditOrder::class,'deleteCommentsFile']);
    Route::post('/update-sku',[EditSKU::class,'editSku']);
    Route::post('/update-contacts',[EditContacts::class,'editContact']);
    Route::post('/update-order-tasks',[EditTasks::class,'editTaskData']);
    Route::post('/update-production-data',[EditProduction::class,'editProductionData']);
    /* Get Order */
    Route::post('/get-orders',[GetOrder::class,'getOrdersList']);

    /*View Order*/
    Route::post('/view-order',ViewOrder::class);


    /* Get thr Style */
    Route::post('/get-styles',[GetStyle::class,'getStyles']);
    Route::post('/get-style',[GetStyle::class,'getTheStyle']);

    /* Reschedule Task, Accomplished Task */
    Route::post('/reschedule-task',[RescheduleOrderTasks::class,'rescheduleTask']);
    // Route::post('/get-reschedule-task',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
    Route::post('/accomplished-task',[OrderTasks::class,'accomplishedTask']);
    Route::post('/update-task',[OrderTasks::class,'updateTask']);
    Route::post('/reschedule-history',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
    Route::post('/actual-start-date',[OrderTasks::class,'actualStartDate']);

    /* SubTask */
    Route::post('/add-subtask',[SubTask::class,'addSubTask']);
    Route::post('/delete-subtask',[SubTask::class,'deleteSubTask']);

    /* Pending Task */
    Route::post('/get-ordersList',[GetOrders::class,'getOrdersList']);
    Route::post('/get-pending-tasks-list',[PendingTask::class,'pendingTasks']);
    Route::post('/download-pendingTask-pdf',DownloadPDF::class);

    /*Inprogress Percentage */
    Route::post('/update-inprogress-percentage',[OrderTasks::class,'update_inprogress_percentage']);
    Route::post('/inprogress-percentage-history',[OrderTasks::class,'inprogress_percentage_history']);

    /* Pending Production */
    Route::post('/get-pending-production-list',[PendingProduction::class,'pendingProduction']);
    Route::post('/download-production-pdf',DownloadProductionPDF::class);

    /* Data Input */
    Route::post('/get-calendar-data',[GetData::class,'getCalendarData']);
    Route::post('/add-input-data',[AddInputMultipleData::class,'addInputData']);
    Route::post('/add-input-data-excess',[AddInputMultipleData::class,'addInputDataAfterDateExceeded']);

    /* Chats */
    Route::post('/add-chat',[Chats::class,'addChats']);
    Route::post('/get-chat',[Chats::class,'getChat']);
    Route::post('/export-chat',[Chats::class,'exportChats']);
    Route::post('/delete-chat',[Chats::class,'deleteChat']);
    Route::post('/update-chat-type',[Chats::class,'changeCommentType']);

    /*Update Order Action Like Cancel or Delete*/
    Route::post('/update-order-action',UpdateOrderActionData::class);
    /*Order Status*/
    Route::post('/download-orderstatus-pdf',OrderStatusDownloadPDF::class);
    /* Order Feedback */
    Route::post('/add-order-feedback',[OrderFeedback::class,'addOrderFeedback']);
    Route::post('/view-order-feedback',[OrderFeedback::class,'viewOrderFeedback']);
    /* Plan Validation */
    Route::post('/validate-plan',[PlanValidation::class,'orderValidation']);
    /* Update/Upgrade Plan  */
    Route::post('/update-plan',[ActivePlans::class,'updatePlan']);
    /*Reports */
    Route::post('/get-filters',[TaskFilters::class,'getSecondBasedOnFirst']);
    /* Production */
    Route::post('/get-production-report',[ProductionReports::class,'getProductionReports']);
    Route::post('/download-production-report',ProductionReportDownload::class);
    /* Tasks */
    Route::post('/get-tasks-report',[TaskReports::class,'taskReports']);
    Route::post('/download-tasks-report',TaskReportDownload::class);
    Route::post('/get-possible-delay-tasks-report',[TaskReports::class,'possibleDelyTaskReports']);
    Route::post('/download-possible-delay-tasks-report',[TaskReports::class,'downloadPossibleDelyTaskReports']);
    /* Prod Daily Updates */
    Route::post('/get-daily-prod-report',[DailyProdReports::class,'dailyProdReports']);
    Route::post('/download-daily-prod-report',DailyProdReportsDownload::class);
    /* Order Reports */
    Route::post('/get-order-reports',[OrderReport::class,'orderStatusReport']);
    Route::post('/download-order-reports',OrderReportDownload::class);
    /* Prod Daily SKU Updates */
    Route::post('/get-daily-sku-reports',[SKUDailyProdReports::class,'skuDailyProdData']);
    Route::post("/download-daily-sku-reports",SKUDailyProdReportDownload::class);
    /* Prod Daily Updates Logs */
    Route::post('/get-daily-prod-report-log',[DailyProdReports::class,'dailyProdReportsLogs']);


    /* Email Configurations */
    Route::post('/add-email-config',[EmailConfigs::class,'addEmailConfigs']);
    /* Staff Register Re-Send Mail */
    Route::post('/resend-email-staff',[Staffs::class,'resendRegisterEmail']);
    /* Payment History */
    Route::post('/payment-history',[ActivePlans::class,'getPaymentHistoryOfCompany']);
    /* Partial Delivery */
    Route::post('/add-partial-delivery',[PartialDeliveries::class,'addPartialDelivery']);
    Route::post('/get-delivery-details',[PartialDeliveries::class,'getDetailsForPartialDelivery']);
    /* Order Materials and label */
    Route::post('/all-vendor-list',[InquiryChat::class,'all_vendor_list']);
    Route::post('/add-order-materials-label-media',[OrderMaterialsLabel::class,'add_materials_label_media']);
    Route::post('/delete-order-materials-label-media',[OrderMaterialsLabel::class,'delete_order_media']);
    Route::post('/add-order-materials-label',[OrderMaterialsLabel::class,'add_materials_label']);
    Route::post('/get-order-materials-label',[OrderMaterialsLabel::class,'get_materials_label']);
    Route::post('/update-order-materials-label',[OrderMaterialsLabel::class,'update_materials_label']);
    Route::post('/download-order-materials-label',[OrderMaterialsLabel::class,'download_materials_label']);

    Route::post('/add-order-bom',[OrderMaterialsLabel::class,'add_order_bom']);
    Route::post('/get-order-bom',[OrderMaterialsLabel::class,'get_order_bom']);
    Route::post('/update-order-bom',[OrderMaterialsLabel::class,'update_order_bom']);
    Route::post('/download-order-bom',[OrderMaterialsLabel::class,'download_order_bom']);
    Route::post('/create-user-unit',[OrderMaterialsLabel::class,'createOrderUnit']);
    Route::post('/get-dashboard-bom',[OrderMaterialsLabel::class,'get_dashboard_bom']);

    /* Inquiry Module */
    Route::post('/inquiry-file-upload',[Inquiry::class,'inquiry_file_upload']);
    Route::post('/save-inquiry',[Inquiry::class,'save_inquiry']);
    Route::post('/get-inquiry',[Inquiry::class,'get_inquirys']);
    Route::post('/inquiry-details',[Inquiry::class,'inquiry_details']);
    Route::post('/inquiry-sku',[Inquiry::class,'inquiry_sku']);
    Route::post('/inquiry-media',[Inquiry::class,'inquiry_media']);
    Route::post('/save-inquiry-factory-response',[Inquiry::class,'save_inquiry_factory_response']);
    Route::post('/save-inquiry-contact',[Inquiry::class,'save_inquiry_contact']);
    Route::post('/get-inquiry-factory',[Inquiry::class,'get_inquiry_contact']);
    Route::post('/send-inquiry',[Inquiry::class,'send_inquiry']);
    Route::post('/inquiry-factory-list',[Inquiry::class,'inquiry_factory_list']);
    Route::post('/inquiry-factory-response',[Inquiry::class,'inquiry_factory_response']);
    Route::post('/delete-inquiry',[Inquiry::class,'delete_inquiry']);
    Route::post('/factory-get-inquiry',[Inquiry::class,'factory_get_inquirys']);
    Route::post('/factory-inquiry-contact',[Inquiry::class,'factory_inquiry_contact']);
    Route::post('/update-inquiry-contact',[Inquiry::class,'update_inquiry_contact']);
    Route::post('/get-inquiry-master',[Inquiry::class,'get_inquiry_master']);
    Route::post('/factory-inquiry-response',[Inquiry::class,'factory_inquiry_response']);
    Route::post('/get-buyer-inquiry-list',[Inquiry::class,'get_buyer_inquiry_list']);
    Route::post('/get-inquiry-factory-list',[Inquiry::class,'get_inquiry_factory_list']);
    Route::post('/save-factory-feedback',[Inquiry::class,'save_factory_feedback']);
    Route::post('/delete-inquiry-media',[Inquiry::class,'delete_inquiry_media']);
    Route::post('/get-factory-ratings',[Inquiry::class,'get_factory_ratings']);
    Route::post('/check-factory-feedback',[Inquiry::class,'check_factory_feedback']);
    Route::post('/pdf-merge',[Inquiry::class,'pdfmerge']);
    Route::post('/check-buyer-notification',[Inquiry::class,'check_buyer_notification']);
    Route::post('/check-factory-notification',[Inquiry::class,'check_factory_notification']);
    Route::post('/get-factory-list-response',[Inquiry::class,'get_factory_list_response']);
    Route::post('/save-buyer-inquiry-factory-response',[Inquiry::class,'save_buyer_inquiry_factory_response']);
    Route::post('/add-inquiry-master-data',[Inquiry::class,'add_inquiry_master']);
    Route::post('/inquiry-pdf-download',[Inquiry::class,'inquiry_pdf_download']);
    Route::post('/buyer-inquiries-download',[DownloadInquiries::class,'download_buyer_inquirys']);
    Route::post('/factory-inquiries-download',[DownloadInquiries::class,'download_factory_inquirys']);
    Route::post('/factory-response-inquiries-download',[DownloadInquiries::class,'download_inquiry_factory_response']);
    Route::post('/edit-inquiry',[Inquiry::class,'update_inquiry']);
    Route::post('/factory-feedback',[Inquiry::class,'get_factory_feedback']);
    Route::post('/factory-feedback-download',[DownloadInquiries::class,'download_factory_feedback']);
    Route::post('/get-factory-inquiry-list',[Inquiry::class,'get_factory_inquiry_list']);
    Route::post('/get-buyer-factory-list',[Inquiry::class,'get_buyer_factory_list']);
    Route::post('/get-inquiry-label',[DownloadInquiries::class,'get_inquiry_label']);
    Route::post('/download-get-inquiry-label',[DownloadInquiries::class,'download_get_inquiry_label']);
    Route::post('/label-file-upload',[InquiryChat::class,'label_file_upload']);
    Route::post('/add-label-content',[InquiryChat::class,'add_label_content']);
    Route::post('/get-inquiry-po-chat',[InquiryChat::class,'get_inquiry_po_chat']);
    Route::post('/download-po-inquiry-label',[InquiryChat::class,'download_po_inquiry_label']);
    Route::post('/label-file-delete',[InquiryChat::class,'label_file_delete']);
    Route::post('/get-label-inquiry-ids',[InquiryChat::class,'get_label_inquiry_ids']);
    Route::post('/get-label-content',[InquiryChat::class,'get_label_content']);
    Route::post('/edit-label-content',[InquiryChat::class,'edit_label_content']);
    Route::post('/label-vendor-list',[InquiryChat::class,'label_vendor_list']);
    Route::post('/add-label-vendor',[InquiryChat::class,'add_label_vendor']);
    Route::post('/get-label-vendor-info',[InquiryChat::class,'get_label_vendor']);
    Route::post('/edit-label-vendor',[InquiryChat::class,'edit_label_vendor']);
    Route::post('/assign-po-vendor',[InquiryChat::class,'assign_po_vendor']);
    Route::post('/po-details',[Inquiry::class,'po_details']);
    Route::post('/po-sku',[Inquiry::class,'po_sku']);
    Route::post('/po-media',[Inquiry::class,'po_media']);
    Route::post('/duplicate-inquiry',[Inquiry::class,'duplicate_inquiry']);
    Route::post('/get-inquiry-additional-info',[Inquiry::class,'get_inquiry_additional_info']);
    Route::post('/delete-multiple-inquiry-media',[Inquiry::class,'delete_multiple_inquiry_media']);
    Route::post('/send-mail-inquiry',[Inquiry::class,'send_mail_inquiry']);
    Route::post('/inquiry-sent-mail-details',[Inquiry::class,'inquiry_sent_mail_details']);

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

    /* PO Module */
    Route::post('/generate-po',[PO::class,'generate_po_factory']);
    Route::post('/view-po',[PO::class,'view_company_po']);
    Route::post('/edit-po',[PO::class,'get_po']);
    Route::post('/upload-media',[PO::class,'upload_file_po']);
    Route::post('/delete-media',[PO::class,'delete_inquiry_po_media']);
    Route::post('/update-po',[PO::class,'update_po']);
    Route::post('/cancel-po',[PO::class,'cancel_po']);
    Route::post('/get-po-factory',[PO::class,'get_po_factory_list']);
    Route::post('/get-po-additional-info',[PO::class,'get_po_additional_info']);
    Route::post('/delete-multiple-po-media',[PO::class,'delete_multiple_po_media']);

    Route::post('/generate-new-po',[POrder::class,'generate_po']);
    Route::post('/download-po-pdf',[POrder::class,'generate_po_pdf_new']);
    Route::post('/view-new-po',[POrder::class,'view_new_po']);
    Route::post('/upload-media-po',[POrder::class,'upload_file_po']);
    Route::post('/delete-media-po',[POrder::class,'delete_inquiry_po_media']);
    Route::post('/get-po-details',[POrder::class,'get_po']);
    Route::post('/update-new-po',[POrder::class,'update_po']);
    Route::post('/po-status-update',[POrder::class,'update_po_status']);
    Route::post('/download-po-list',[POrder::class,'download_po_list']);
    Route::post('/get-po-id',[POrder::class,'get_po_id']);
    Route::post('/delete-po',[POrder::class,'delete_po']);

    Route::post('/generate-new-multiple-po',[POrder::class,'generate_po_multiple']);
    Route::post('/view-new-multiple-po',[POrder::class,'view_new_po_multiple']);
    Route::post('/get-multiple-po-details',[POrder::class,'get_po_multiple']);
    Route::post('/download-multiple-po-pdf',[POrder::class,'generate_multiple_po_pdf_new']);
    Route::post('/multiple-po-status-update',[POrder::class,'update_multiple_po_status']);
    Route::post('/delete-multiple-po',[POrder::class,'delete_multi_po']);
    Route::post('/update-new-multiple-po',[POrder::class,'update_multi_po']);
    Route::post('/delete-style-po',[POrder::class,'delete_style_po']);
    Route::post('/all-forwarder-list',[POrder::class,'all_forwarer_list']);
    Route::post('/add-forwarder',[POrder::class,'add_forwarder']);
    Route::post('/edit-forwarder',[POrder::class,'edit_forwarder']);
    Route::post('/add-po-comments',[POrder::class,'addPOComments']);
    Route::post('/get-po-comments-details',[POrder::class,'getPOComments']);
    Route::post('/delete-po-comments-file',[POrder::class,'delete_po_comments_file']);
    Route::post('/add-po-comments-audio-file',[POrder::class,'addPOCommentsAudioFile']);
    Route::post('/translate-po',[POrder::class,'translate_po']);

    /*SAM Module */
    Route::post('/get-sam-master-data',[Sam::class,'get_sam_master_data']);
    Route::post('/add-sam-master-data',[Sam::class,'add_sam_master_data']);
    Route::post('/save-sam-report-settings',[Sam::class,'save_sam_report_settings']);
    Route::post('/get-sam-report-settings',[Sam::class,'get_sam_report_settings']);
    Route::post('/get-sam-report-time',[Sam::class,'get_sam_report_time']);
    Route::post('/save-sam-quantity',[Sam::class,'save_sam_quantity']);
    Route::post('/get-sam-quantity',[Sam::class,'get_sam_quantity']);
    Route::post('/get-sam-report',[Sam::class,'get_sam_report']);
    Route::post('/download-sam-report',[Sam::class,'download_sam_report']);
    Route::post('/sam-report-setting-details',[Sam::class,'sam_report_setting_details']);
    Route::post('/update-sam-report-setting',[Sam::class,'update_report_setting_details']);
    Route::post('/get-previous-sam-report-setting',[Sam::class,'get_pervious_sam_report_settings']);
    Route::post('/get-previous-sam-report-details',[Sam::class,'get_pervious_sam_report_details']);

    /*Email Notification Setting Order Delay Emails for No of Days*/
    Route::post('/email-notification-settings',[UserSettings::class,'emailNotificationSetting']);
    Route::post('/view-email-notification-settings',[UserSettings::class,'viewEmailNotificationSetting']);

    /*Order BOM Approval */
    Route::post('/add-orderbom-approval',[OrderApprovalHistoryLog::class,'addBOMApprovalLog']);
    Route::post('/view-orderbom-approval',[OrderApprovalHistoryLog::class,'viewBOMApprovalLog']);

    /* Task Template Upload */
    Route::post('/upload-task-template',[OrderTaskExcelUpload::class,'excel_file_upload']);

    /*Start New Dashboard v2*/
    /*New Dashboard v2 production status limit 2 */
    Route::post('/get-dashboard-staff-prod-status',[DashboardNew::class,'getStaffProductionStatus']);
    /*New Dashboard v2 Task status limit 2 */
    Route::post('/get-dashboard-staff-tasks-status',[DashboardNew::class,'getStaffTaskStatus']);
    Route::post('/get-ongoing-staff-order',[DashboardNew::class,'onGoingStaffList']);
    Route::post('/get-staff-order-list',[DashboardNew::class,'getStafOvelallTaskList']);
    Route::post('/get-staff-orders',[DashboardNew::class,'dashboardOrderList']);
    Route::post('/get-production-week-view',[DashboardNew::class,'getProductionWeekByWeekView']);
    /*End New Dashboard v2*/
    /* Start Tech Pack */

    Route::post('/create-techpack',[TechPackDetail::class,'addTechPack']);
    Route::post('/delete-techpack-file',[TechPackDetail::class,'deleteTechPackFile']);
    Route::post('/view-techpack',[TechPackDetail::class,'viewTechPack']);
    Route::post('/edit-techpack',[TechPackDetail::class,'editTechPack']);
    Route::post('/update-techpack',[TechPackDetail::class,'updateTechPack']);
    Route::post('/download-techpackPDF',[TechPackDetail::class,'downloadTechPackPDF']);
    Route::post('/upload-techpack-file',[TechPackDetail::class,'addTechPackFile']);
    Route::post('/delete-techpack',[TechPackDetail::class,'deleteTechPackDetails']);
    Route::post('/publish-techpack',[TechPackDetail::class,'publishTechPack']);
    Route::post('/get-all-techpack-type',[TechPackDetail::class,'getTechPackAllDetails']);
    Route::post('/add-techpack-comments',[TechPackDetail::class,'addTechPackComments']);
    Route::post('/get-techpack-comments-details',[TechPackDetail::class,'getTechPackCommentsDetails']);
    Route::post('/update-techpack-comments-details',[TechPackDetail::class,'updateTechPackCommentsDetails']);
    Route::post('/add-new-techpack-comments',[TechPackDetail::class,'addNewTechPackComments']);
    Route::post('/get-new-techpack-comments-details',[TechPackDetail::class,'getNewTechPackCommentsDetails']);
    Route::post('/download-new-techpackPDF',[TechPackDetail::class,'downloadNewTechPackPDF']);
    Route::post('/update-techpack-comments-read-status',[TechPackDetail::class,'UpdateCommentsReadStatus']);
    Route::post('/get-techpack-comments-notifications',[TechPackDetail::class,'getCommentsNotifications']);
    Route::post('/get-po-basic-information',[POrder::class,'get_basic_information']);
    Route::post('/get-edit-techpack-info',[TechPackDetail::class,'getEditTechpackInfo']);
    Route::post('/translate-techpack-comment',[TechPackDetail::class,'translateTechpackComment']);
    Route::post('/add-audio-file',[TechPackDetail::class,'addAudioFile']);
    Route::post('/add-techpackpack-comments-video-file',[TechPackDetail::class,'addTechpackCommentsVideoFile']);
    /* End Tech Pack */
    /* Inquiry Dashboard Starts */
    Route::post('/inquiry-dashboard-details',[POrder::class,'inquiry_dashboard_details']);
    /* End Inquiry Dashboard */

    /* Inquiry Roles and Permissions Starts*/
    Route::post('/get-inquiry-roles-list',[Roles::class,'get_inquiry_roles_list']);
    Route::get('/get-inquiry-modules',[Roles::class,'getInquiryModules']);
    /* Inquiry Roles and Permissions End*/
});


/* Staff Login */
Route::post('/staff-get-otp',[Staffs::class, 'getOtp']);
Route::post('/staff-verify-otp',[Staffs::class, 'otpValidate']);
Route::middleware('auth:staff-api')->prefix('staff')->group(function(){




    Route::post('/staff-general-update',[Staffs::class, 'getStaffGeneralUpdate']);
    Route::post('/logout',[Staffs::class,"staffLogout"]);
    Route::post('/get-color', [Color::class, 'index']);
    Route::post('/add-color', [Color::class, 'store']);
    Route::post('/update-color', [Color::class, 'update']);
    Route::post('/delete-color', [Color::class, 'delete']);
    Route::post('/edit-color', [Color::class, 'edit']);

    /* Size (all including add,update and delete) routes */
    //Route::apiResource('/size',Size::class);
    Route::post('/get-size-category',[Size::class,"get_size_categories"]);
    Route::post('/add-size-category',[Size::class,"add_size_category"]);
    Route::post('/size',[Size::class,"store"]);
    Route::post('/get-size',[Size::class,"index"]);

    /* Staff Login Routes */
    Route::post('/register-staff',[Staffs::class, 'register']);
    Route::post('/register-staffprofile',[Staffs::class, 'registerWithImage']);
    Route::post('/get-staffs',[Staffs::class, 'getStaffList']);
    Route::post('/edit-staff',[Staffs::class, 'editStaff']);
    Route::post('/edit-staffprofile',[Staffs::class, 'editStaffProfile']);
    Route::post('/update-staff',[Staffs::class, 'updateStaff']);
    Route::post('/update-staffprofile',[Staffs::class, 'updateStaffProfile']);
    Route::post('/get-staff-role',[Staffs::class, 'getStaffRole']);

    /* Country and Language Routes */
    Route::get('/get-countries',[Countries::class,'countries']);
    Route::get('/get-languages',[Countries::class,'languages']);

    /* Roles and Permissions */
    Route::post('/get-roles-list',[Roles::class,'get_roles_list']);
    Route::post('/create-new-role',[Roles::class,'create_new_role']);
    Route::post('/add-privileges',[Roles::class,'add_role_privileges']);
    Route::post('/get-roles',[Roles::class,'getRoles']);
    Route::get('/get-modules',[Roles::class,'getModules']);

    /* Company registration and workspace */
    Route::post('/company-settings',[CompanySettings::class,'registerCompany']);
    Route::post('/get-company-settings',[CompanySettings::class,'viewCompanyDetails']);
    Route::get('/workspace-type',[CompanySettings::class,'workspaceType']);
    Route::post('/create-workspace',[CompanySettings::class,'createWorkspace']);
    Route::post('/get-workspace',[CompanySettings::class,'getWorkspace']);
    Route::post('/update-company-settings',[CompanySettings::class,'updateCompanyDetails']);

    /* User and Staff Preferences */
    // Route::post('/user-preference',[UserSettings::class,'userPreference']);
    // Route::post('/get-user-preference',[UserSettings::class,'getUserPreferences']);
    Route::post('/staff-preference',[StaffSettings::class,'staffPreference']);
    Route::post('/get-staff-preference',[StaffSettings::class,'getStaffPreferences']);
    // Route::post('/get-notification-settings',[UserSettings::class,'getNotificationSettings']);
    // Route::post('/notification-settings',[UserSettings::class,'notificationSettings']);
    Route::post('/get-staff-notification-settings',[StaffSettings::class,'getNotificationSettings']);
    Route::post('/staff-notification-settings',[StaffSettings::class,'notificationSettings']);
    // Route::post('/get-email-settings',[UserSettings::class,'getEmailScheduleNotification']);
    // Route::post('/email-settings',[UserSettings::class,'emailScheduleNotification']);
    Route::post('/get-staff-email-settings',[StaffSettings::class,'getEmailScheduleNotification']);
    Route::post('/staff-email-settings',[StaffSettings::class,'emailScheduleNotification']);
    Route::post('/email-settings-order-ids',[UserSettings::class,'emailScheduleReportsOrderIds']);
    Route::post('/get-email-settings-order-ids',[UserSettings::class,'get_emailScheduleReportsOrderIds']);
    Route::post('/get-dashboard-settings',[UserSettings::class,'dashboardNotifications']);
    Route::post('/add-dashboard-settings',[UserSettings::class,'addDashboardNotifications']);
    /* Widgets Orders */
    Route::post('/get-dashboard-settings-orders',[DashboardWidgetsSettings::class,'getWidgetOrders']);
    Route::post('/add-dashboard-settings-orders',[DashboardWidgetsSettings::class,'addDashboardOrders']);
    /* New DashBoard Widgets */
    Route::post('/get-tasks-status',[DashboardNew::class,'getTaskStatus']);
    Route::post('/get-top-delay-tasks',[DashboardNew::class,'getTopDelayTask']);
    Route::post('/get-prod-status',[DashboardNew::class,'getProductionStatus']);
    Route::post('/get-top-delay-prod',[DashboardNew::class,'getTopDelayProduction']);
    Route::post('/order-status-dashboard',[DashboardNew::class,'orderStatus']);
    Route::post('/new-dashboard-widgets',[DashboardNew::class,'dashboardWidgets']);
    Route::post('/plan-status',[ActivePlans::class,'getPlanStatus']);
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

        /*Fabric Compositions */
        Route::post('/get-fabric-composition',[FabricCompositions::class,'getFabricComposition']);
        Route::post('/create-fabric-composition',[FabricCompositions::class,'createFabricComposition']);
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
    /* Order Registration- All in One */
    Route::post('/get-all-order-details',[GetAllInOneOrder::class,'getOrderAllDetails']);
    Route::post('/get-all-sku',[GetAllInOneOrder::class,'getSizeAndColor']);
    /* Add Order */
    Route::post('/add-order',AddOrder::class);
    Route::post('/add-order-sku',[AddOrderSKU::class,'addSku']);
    Route::post('/get-order-sku',[AddOrderSKU::class,'getSku']);
    Route::post('/add-order-contacts',[AddOrderContacts::class,'addContact']);
    Route::post('/get-order-contacts',[AddOrderContacts::class,'getContact']);
    Route::post('/get-order-contact',[AddOrderContacts::class,'getContacts']);
    Route::post('/add-order-production',[AddOrderProduction::class,'addProductionData']);
    Route::post('/get-order-production',[AddOrderProduction::class,'getProductionData']);
    Route::post('/create-order-template',[OrderTemplates::class,'createOrderTemplate']);
    Route::post('/get-order-template',[OrderTemplates::class,'getOrderTemplates']);
    Route::post('/get-order-template-details',[OrderTemplates::class,'getOrderTemplatesDetails']);
    Route::post('/create-task-data',[OrderTasks::class,'addTaskData']);
    Route::post('/get-task-data',[OrderTasks::class,'getTaskDetails']);
    Route::post('/update-task-data',[OrderTasks::class,'updateTaskData']);
    Route::post('/delete-task-data',[OrderTasks::class,'deleteTaskDetails']);
    Route::post('/add-order-comments',[AddOrder::class,'addComments']);

    /*Update Order Action Like Cancel or Delete*/
    Route::post('/update-order-action',UpdateOrderActionData::class);
    /* Order Files */
    Route::post('/add-files',[OrderAddSpecs::class,'addAdditionalSpecs']);
    Route::post('/list-files',[OrderAddSpecs::class,'getUploadedFiles']);
    Route::post('/download-file',[OrderAddSpecs::class,'downloadFile']);
    Route::post('/delete-file',[OrderAddSpecs::class,'deleteFile']);
    /* Dashboard */
    Route::post('/dashboard-widgets',[DashBoard::class,'dashboardWidgets']);
    Route::post('/ongoing-list',[Dashboard::class,'onGoingList']);
    Route::post('/notification-dashboard',[Dashboard::class,'notifications']);
    Route::post('/dashboard-taskstatus',[Dashboard::class,'DashboardTaskStatus']);

    Route::post('/recent-order-details',[DashboardNew::class,'dashboardRecentOrderDetails']);
    Route::post('/staff-invite-details',[DashboardNew::class,'staffInviteDetails']);
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
    Route::post('/update-order-comments',[EditOrder::class,'updateComments']);
    Route::post('/delete-order-comments-file',[EditOrder::class,'deleteCommentsFile']);
    Route::post('/update-sku',[EditSKU::class,'editSku']);
    Route::post('/update-contacts',[EditContacts::class,'editContact']);
    Route::post('/update-order-tasks',[EditTasks::class,'editTaskData']);
    Route::post('/update-production-data',[EditProduction::class,'editProductionData']);
    /* Get Order */
    Route::post('/get-orders',[GetOrder::class,'getOrdersList']);
    /*View Order*/
    Route::post('/view-order',ViewOrder::class);

    /* Get thr Style */
    Route::post('/get-styles',[GetStyle::class,'getStyles']);
    Route::post('/get-style',[GetStyle::class,'getTheStyle']);

    /* Reschedule Task, Accomplished Task */
    Route::post('/reschedule-task',[RescheduleOrderTasks::class,'rescheduleTask']);
    // Route::post('/get-reschedule-task',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
    Route::post('/accomplished-task',[OrderTasks::class,'accomplishedTask']);
    Route::post('/update-task',[OrderTasks::class,'updateTask']);
    Route::post('/reschedule-history',[RescheduleOrderTasks::class,'getRescheduleTaskUsingId']);
    Route::post('/actual-start-date',[OrderTasks::class,'actualStartDate']);

    /* SubTask */
    Route::post('/add-subtask',[SubTask::class,'addSubTask']);
    Route::post('/delete-subtask',[SubTask::class,'deleteSubTask']);

    /* Pending Task */
    Route::post('/get-ordersList',[GetOrders::class,'getOrdersList']);
    Route::post('/get-pending-tasks-list',[PendingTask::class,'pendingTasks']);
    Route::post('/download-pendingTask-pdf',DownloadPDF::class);

    /*Inprogress Percentage */
    Route::post('/update-inprogress-percentage',[OrderTasks::class,'update_inprogress_percentage']);
    Route::post('/inprogress-percentage-history',[OrderTasks::class,'inprogress_percentage_history']);

    /* Pending Production */
    Route::post('/get-pending-production-list',[PendingProduction::class,'pendingProduction']);
    Route::post('/download-production-pdf',DownloadProductionPDF::class);

    /* Data Input */
    Route::post('/get-calendar-data',[GetData::class,'getCalendarData']);
    Route::post('/add-input-data',[AddInputMultipleData::class,'addInputData']);
    Route::post('/add-input-data-excess',[AddInputMultipleData::class,'addInputDataAfterDateExceeded']);

    /* Chats */
    Route::post('/add-chat',[Chats::class,'addChats']);
    Route::post('/get-chat',[Chats::class,'getChat']);
    Route::post('/export-chat',[Chats::class,'exportChats']);
    Route::post('/delete-chat',[Chats::class,'deleteChat']);
    Route::post('/update-chat-type',[Chats::class,'changeCommentType']);

    /*Update Order Action Like Cancel or Delete*/
    Route::post('/update-order-action',UpdateOrderActionData::class);

    /*Order Status*/
    Route::post('/download-orderstatus-pdf',OrderStatusDownloadPDF::class);
    /* Order Feedback */
    Route::post('/add-order-feedback',[OrderFeedback::class,'addOrderFeedback']);
    Route::post('/view-order-feedback',[OrderFeedback::class,'viewOrderFeedback']);
    /* Plan Validation */
    Route::post('/validate-plan',[PlanValidation::class,'orderValidation']);

    /*Reports */
    Route::post('/get-filters',[TaskFilters::class,'getSecondBasedOnFirst']);
    /* Production */
    Route::post('/get-production-report',[ProductionReports::class,'getProductionReports']);
    Route::post('/download-production-report',ProductionReportDownload::class);
    /* Tasks */
    Route::post('/get-tasks-report',[TaskReports::class,'taskReports']);
    Route::post('/download-tasks-report',TaskReportDownload::class);
    Route::post('/get-possible-delay-tasks-report',[TaskReports::class,'possibleDelyTaskReports']);
    Route::post('/download-possible-delay-tasks-report',[TaskReports::class,'downloadPossibleDelyTaskReports']);
    /* Prod Daily Updates */
    Route::post('/get-daily-prod-report',[DailyProdReports::class,'dailyProdReports']);
    Route::post('/download-daily-prod-report',DailyProdReportsDownload::class);
    /* Order Reports */
    Route::post('/get-order-reports',[OrderReport::class,'orderStatusReport']);
    Route::post('/download-order-reports',OrderReportDownload::class);
    /* Prod Daily SKU Updates */
    Route::post('/get-daily-sku-reports',[SKUDailyProdReports::class,'skuDailyProdData']);
    Route::post("/download-daily-sku-reports",SKUDailyProdReportDownload::class);

    /* Staff Add Last Seen */
    Route::post("/staff-last-seen",[Staffs::class,'addLastSeenToStaff']);
    /* Partial Delivery */
    Route::post('/add-partial-delivery',[PartialDeliveries::class,'addPartialDelivery']);
    Route::post('/get-delivery-details',[PartialDeliveries::class,'getDetailsForPartialDelivery']);

    /* Order Materials and label */
    Route::post('/all-vendor-list',[InquiryChat::class,'all_vendor_list']);
    Route::post('/add-order-materials-label-media',[OrderMaterialsLabel::class,'add_materials_label_media']);
    Route::post('/delete-order-materials-label-media',[OrderMaterialsLabel::class,'delete_order_media']);
    Route::post('/add-order-materials-label',[OrderMaterialsLabel::class,'add_materials_label']);
    Route::post('/get-order-materials-label',[OrderMaterialsLabel::class,'get_materials_label']);
    Route::post('/update-order-materials-label',[OrderMaterialsLabel::class,'update_materials_label']);
    Route::post('/download-order-materials-label',[OrderMaterialsLabel::class,'download_materials_label']);

    Route::post('/add-order-bom',[OrderMaterialsLabel::class,'add_order_bom']);
    Route::post('/get-order-bom',[OrderMaterialsLabel::class,'get_order_bom']);
    Route::post('/update-order-bom',[OrderMaterialsLabel::class,'update_order_bom']);
    Route::post('/download-order-bom',[OrderMaterialsLabel::class,'download_order_bom']);
    Route::post('/create-user-unit',[OrderMaterialsLabel::class,'createOrderUnit']);
    Route::post('/get-dashboard-bom',[OrderMaterialsLabel::class,'get_dashboard_bom']);

    /* Inquiry Module */
    Route::post('/inquiry-file-upload',[Inquiry::class,'inquiry_file_upload']);
    Route::post('/save-inquiry',[Inquiry::class,'save_inquiry']);
    Route::post('/get-inquiry',[Inquiry::class,'get_inquirys']);
    Route::post('/inquiry-details',[Inquiry::class,'inquiry_details']);
    Route::post('/inquiry-sku',[Inquiry::class,'inquiry_sku']);
    Route::post('/inquiry-media',[Inquiry::class,'inquiry_media']);
    Route::post('/save-inquiry-factory-response',[Inquiry::class,'save_inquiry_factory_response']);
    Route::post('/save-inquiry-contact',[Inquiry::class,'save_inquiry_contact']);
    Route::post('/get-inquiry-factory',[Inquiry::class,'get_inquiry_contact']);
    Route::post('/send-inquiry',[Inquiry::class,'send_inquiry']);
    Route::post('/inquiry-factory-list',[Inquiry::class,'inquiry_factory_list']);
    Route::post('/inquiry-factory-response',[Inquiry::class,'inquiry_factory_response']);
    Route::post('/delete-inquiry',[Inquiry::class,'delete_inquiry']);
    Route::post('/factory-get-inquiry',[Inquiry::class,'factory_get_inquirys']);
    Route::post('/factory-inquiry-contact',[Inquiry::class,'factory_inquiry_contact']);
    Route::post('/update-inquiry-contact',[Inquiry::class,'update_inquiry_contact']);
    Route::post('/get-inquiry-master',[Inquiry::class,'get_inquiry_master']);
    Route::post('/factory-inquiry-response',[Inquiry::class,'factory_inquiry_response']);
    Route::post('/get-buyer-inquiry-list',[Inquiry::class,'get_buyer_inquiry_list']);
    Route::post('/get-inquiry-factory-list',[Inquiry::class,'get_inquiry_factory_list']);
    Route::post('/save-factory-feedback',[Inquiry::class,'save_factory_feedback']);
    Route::post('/delete-inquiry-media',[Inquiry::class,'delete_inquiry_media']);
    Route::post('/get-factory-ratings',[Inquiry::class,'get_factory_ratings']);
    Route::post('/check-factory-feedback',[Inquiry::class,'check_factory_feedback']);
    Route::post('/pdf-merge',[Inquiry::class,'pdfmerge']);
    Route::post('/check-buyer-notification',[Inquiry::class,'check_buyer_notification']);
    Route::post('/check-factory-notification',[Inquiry::class,'check_factory_notification']);
    Route::post('/get-factory-list-response',[Inquiry::class,'get_factory_list_response']);
    Route::post('/save-buyer-inquiry-factory-response',[Inquiry::class,'save_buyer_inquiry_factory_response']);
    Route::post('/add-inquiry-master-data',[Inquiry::class,'add_inquiry_master']);
    Route::post('/inquiry-pdf-download',[Inquiry::class,'inquiry_pdf_download']);
    Route::post('/buyer-inquiries-download',[DownloadInquiries::class,'download_buyer_inquirys']);
    Route::post('/factory-inquiries-download',[DownloadInquiries::class,'download_factory_inquirys']);
    Route::post('/factory-response-inquiries-download',[DownloadInquiries::class,'download_inquiry_factory_response']);
    Route::post('/edit-inquiry',[Inquiry::class,'update_inquiry']);
    Route::post('/factory-feedback',[Inquiry::class,'get_factory_feedback']);
    Route::post('/factory-feedback-download',[DownloadInquiries::class,'download_factory_feedback']);
    Route::post('/get-factory-inquiry-list',[Inquiry::class,'get_factory_inquiry_list']);
    Route::post('/get-buyer-factory-list',[Inquiry::class,'get_buyer_factory_list']);
    Route::post('/label-file-upload',[InquiryChat::class,'label_file_upload']);
    Route::post('/add-label-content',[InquiryChat::class,'add_label_content']);
    Route::post('/get-inquiry-po-chat',[InquiryChat::class,'get_inquiry_po_chat']);
    Route::post('/download-po-inquiry-label',[InquiryChat::class,'download_po_inquiry_label']);
    Route::post('/label-file-delete',[InquiryChat::class,'label_file_delete']);
    Route::post('/get-label-inquiry-ids',[InquiryChat::class,'get_label_inquiry_ids']);
    Route::post('/get-label-content',[InquiryChat::class,'get_label_content']);
    Route::post('/edit-label-content',[InquiryChat::class,'edit_label_content']);
    Route::post('/label-vendor-list',[InquiryChat::class,'label_vendor_list']);
    Route::post('/add-label-vendor',[InquiryChat::class,'add_label_vendor']);
    Route::post('/get-label-vendor-info',[InquiryChat::class,'get_label_vendor']);
    Route::post('/edit-label-vendor',[InquiryChat::class,'edit_label_vendor']);
    Route::post('/assign-po-vendor',[InquiryChat::class,'assign_po_vendor']);
    Route::post('/po-details',[Inquiry::class,'po_details']);
    Route::post('/po-sku',[Inquiry::class,'po_sku']);
    Route::post('/po-media',[Inquiry::class,'po_media']);
    Route::post('/duplicate-inquiry',[Inquiry::class,'duplicate_inquiry']);
    Route::post('/get-inquiry-additional-info',[Inquiry::class,'get_inquiry_additional_info']);
    Route::post('/delete-multiple-inquiry-media',[Inquiry::class,'delete_multiple_inquiry_media']);
    Route::post('/send-mail-inquiry',[Inquiry::class,'send_mail_inquiry']);
    Route::post('/inquiry-sent-mail-details',[Inquiry::class,'inquiry_sent_mail_details']);


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

    /* PO Module */
    Route::post('/generate-po',[PO::class,'generate_po_factory']);
    Route::post('/view-po',[PO::class,'view_company_po']);
    Route::post('/edit-po',[PO::class,'get_po']);
    Route::post('/upload-media',[PO::class,'upload_file_po']);
    Route::post('/delete-media',[PO::class,'delete_inquiry_po_media']);
    Route::post('/update-po',[PO::class,'update_po']);
    Route::post('/cancel-po',[PO::class,'cancel_po']);
    Route::post('/get-po-factory',[PO::class,'get_po_factory_list']);
    Route::post('/get-po-additional-info',[PO::class,'get_po_additional_info']);
    Route::post('/delete-multiple-po-media',[PO::class,'delete_multiple_po_media']);

    Route::post('/generate-new-po',[POrder::class,'generate_po']);
    Route::post('/download-po-pdf',[POrder::class,'generate_po_pdf_new']);
    Route::post('/view-new-po',[POrder::class,'view_new_po']);
    Route::post('/upload-media-po',[POrder::class,'upload_file_po']);
    Route::post('/delete-media-po',[POrder::class,'delete_inquiry_po_media']);
    Route::post('/get-po-details',[POrder::class,'get_po']);
    Route::post('/update-new-po',[POrder::class,'update_po']);
    Route::post('/po-status-update',[POrder::class,'update_po_status']);
    Route::post('/download-po-list',[POrder::class,'download_po_list']);
    Route::post('/get-po-id',[POrder::class,'get_po_id']);
    Route::post('/delete-po',[POrder::class,'delete_po']);

    Route::post('/generate-new-multiple-po',[POrder::class,'generate_po_multiple']);
    Route::post('/view-new-multiple-po',[POrder::class,'view_new_po_multiple']);
    Route::post('/get-multiple-po-details',[POrder::class,'get_po_multiple']);
    Route::post('/download-multiple-po-pdf',[POrder::class,'generate_multiple_po_pdf_new']);
    Route::post('/multiple-po-status-update',[POrder::class,'update_multiple_po_status']);
    Route::post('/delete-multiple-po',[POrder::class,'delete_multi_po']);
    Route::post('/update-new-multiple-po',[POrder::class,'update_multi_po']);
    Route::post('/delete-style-po',[POrder::class,'delete_style_po']);
    Route::post('/all-forwarder-list',[POrder::class,'all_forwarer_list']);
    Route::post('/add-forwarder',[POrder::class,'add_forwarder']);
    Route::post('/edit-forwarder',[POrder::class,'edit_forwarder']);
    Route::post('/add-po-comments',[POrder::class,'addPOComments']);
    Route::post('/get-po-comments-details',[POrder::class,'getPOComments']);
    Route::post('/delete-po-comments-file',[POrder::class,'delete_po_comments_file']);
    Route::post('/add-po-comments-audio-file',[POrder::class,'addPOCommentsAudioFile']);
    Route::post('/translate-po',[POrder::class,'translate_po']);

    /*SAM Module */
    Route::post('/get-sam-master-data',[Sam::class,'get_sam_master_data']);
    Route::post('/add-sam-master-data',[Sam::class,'add_sam_master_data']);
    Route::post('/save-sam-report-settings',[Sam::class,'save_sam_report_settings']);
    Route::post('/get-sam-report-settings',[Sam::class,'get_sam_report_settings']);
    Route::post('/get-sam-report-time',[Sam::class,'get_sam_report_time']);
    Route::post('/save-sam-quantity',[Sam::class,'save_sam_quantity']);
    Route::post('/get-sam-quantity',[Sam::class,'get_sam_quantity']);
    Route::post('/get-sam-report',[Sam::class,'get_sam_report']);
    Route::post('/download-sam-report',[Sam::class,'download_sam_report']);
    Route::post('/sam-report-setting-details',[Sam::class,'sam_report_setting_details']);
    Route::post('/update-sam-report-setting',[Sam::class,'update_report_setting_details']);
    Route::post('/get-previous-sam-report-setting',[Sam::class,'get_pervious_sam_report_settings']);
    Route::post('/get-previous-sam-report-details',[Sam::class,'get_pervious_sam_report_details']);


    /*Order BOM Approval */
    Route::post('/add-orderbom-approval',[OrderApprovalHistoryLog::class,'addBOMApprovalLog']);
    Route::post('/view-orderbom-approval',[OrderApprovalHistoryLog::class,'viewBOMApprovalLog']);

    /* Task Template Upload */
    Route::post('/upload-task-template',[OrderTaskExcelUpload::class,'excel_file_upload']);

    /*Start New Dashboard v2*/
    /*New Dashboard v2 production status limit 2 */
    Route::post('/get-dashboard-staff-prod-status',[DashboardNew::class,'getStaffProductionStatus']);
    /*New Dashboard v2 Task status limit 2 */
    Route::post('/get-dashboard-staff-tasks-status',[DashboardNew::class,'getStaffTaskStatus']);
    Route::post('/get-ongoing-staff-order',[DashboardNew::class,'onGoingStaffList']);
    Route::post('/get-staff-order-list',[DashboardNew::class,'getStafOvelallTaskList']);
    Route::post('/get-staff-orders',[DashboardNew::class,'dashboardOrderList']);
    Route::post('/get-production-week-view',[DashboardNew::class,'getProductionWeekByWeekView']);
    /*End New Dashboard v2*/

    /* Start Tech Pack */

    Route::post('/create-techpack',[TechPackDetail::class,'addTechPack']);
    Route::post('/delete-techpack-file',[TechPackDetail::class,'deleteTechPackFile']);
    Route::post('/view-techpack',[TechPackDetail::class,'viewTechPack']);
    Route::post('/edit-techpack',[TechPackDetail::class,'editTechPack']);
    Route::post('/update-techpack',[TechPackDetail::class,'updateTechPack']);
    Route::post('/download-techpackPDF',[TechPackDetail::class,'downloadTechPackPDF']);
    Route::post('/upload-techpack-file',[TechPackDetail::class,'addTechPackFile']);
    Route::post('/delete-techpack',[TechPackDetail::class,'deleteTechPackDetails']);
    Route::post('/publish-techpack',[TechPackDetail::class,'publishTechPack']);
    Route::post('/get-all-techpack-type',[TechPackDetail::class,'getTechPackAllDetails']);
    Route::post('/add-techpack-comments',[TechPackDetail::class,'addTechPackComments']);
    Route::post('/get-techpack-comments-details',[TechPackDetail::class,'getTechPackCommentsDetails']);
    Route::post('/update-techpack-comments-details',[TechPackDetail::class,'updateTechPackCommentsDetails']);
    Route::post('/add-new-techpack-comments',[TechPackDetail::class,'addNewTechPackComments']);
    Route::post('/get-new-techpack-comments-details',[TechPackDetail::class,'getNewTechPackCommentsDetails']);
    Route::post('/download-new-techpackPDF',[TechPackDetail::class,'downloadNewTechPackPDF']);
    Route::post('/update-techpack-comments-read-status',[TechPackDetail::class,'UpdateCommentsReadStatus']);
    Route::post('/get-techpack-comments-notifications',[TechPackDetail::class,'getCommentsNotifications']);
    Route::post('/get-po-basic-information',[POrder::class,'get_basic_information']);
    Route::post('/get-edit-techpack-info',[TechPackDetail::class,'getEditTechpackInfo']);
    Route::post('/translate-techpack-comment',[TechPackDetail::class,'translateTechpackComment']);
    Route::post('/add-audio-file',[TechPackDetail::class,'addAudioFile']);
    Route::post('/add-techpackpack-comments-video-file',[TechPackDetail::class,'addTechpackCommentsVideoFile']);
    /* End Tech Pack */
    /* Inquiry Dashboard Starts */
    Route::post('/inquiry-dashboard-details',[POrder::class,'inquiry_dashboard_details']);
    /* End Inquiry Dashboard */
    /* Inquiry Roles and Permissions Starts*/
    Route::post('/get-inquiry-roles-list',[Roles::class,'get_inquiry_roles_list']);
    Route::get('/get-inquiry-modules',[Roles::class,'getInquiryModules']);
    /* Inquiry Roles and Permissions End*/
});


//Delete the unused media data in Database and AWS server
Route::get('/delete-unused-inquiry-media',[Inquiry::class,'delete_unused_inquiry_media']);

Route::post('/pdf-chart',[PdfChart::class, 'download_pdf_chart']);

//For mobile view
Route::post('/inquiry-details-mobile',[Inquiry::class,'inquiry_details']);
Route::post('/inquiry-media-mobile',[Inquiry::class,'inquiry_media']);
Route::post('/inquiry-sku-mobile',[Inquiry::class,'inquiry_sku']);
Route::post('/get-inquiry-additional-info-mobile',[Inquiry::class,'get_inquiry_additional_info']);

Route::post('/po-details-mobile',[Inquiry::class,'po_details']);
Route::post('/po-media-mobile',[Inquiry::class,'po_media']);
Route::post('/po-sku-mobile',[Inquiry::class,'po_sku']);
Route::post('/get-po-additional-info-mobile',[PO::class,'get_po_additional_info']);

Route::post('/get-po-details-mobile',[POrder::class,'get_po_mobile']);
Route::post('/get-inquiry-master-mobile',[Inquiry::class,'get_inquiry_master']);

//For chat box
Route::post('/add-live-chat',[Chatbox::class,'add_live_chat']);
Route::post('/get-live-chat',[Chatbox::class,'get_live_chat']);
Route::post('/get-pervious-user-chat',[Chatbox::class,'get_pervious_user_chat']);
Route::post('/get-chat-list',[ChatBox::class, 'get_chat_list']);
Route::post('/get-chat-detail',[ChatBox::class, 'get_chat_detail']);
Route::post('/chat-export',[ChatBox::class,'chat_export']);

//});



