<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Color as ColorModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\WebSite\Common\Color;
use Illuminate\Support\Facades\ENV;
use App\Common\logs;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\tests3fileupload;
use App\Http\Controllers\AddPermissionForUsers;


use Illuminate\Contracts\Filesystem\Filesystem;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use App\Http\Controllers\testEmail;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');

    // $text='This is a test email? this & n';
    // //$text='This is a test email 問い合わせ ,? &';
    // //$text='メインラベルは単なる衣服の一部ではなく';
    // $text = "<p>メインラベルは単なる衣服の一部ではなく、ブランドのアイデンティティを表現したものです。このラベルは、衣服のネックラインまたはウエストラインのいずれかのインテリアトップに配置され、ブランド名と衣服のサイズを示します。</p><p><br></p>";
    // //$text=' 問い合わせ This is a test email ,? &';
    // // $output= preg_match('/^[A-Za-z0-9 .,!?&-\'"]+$/', $text);
    // //$output= preg_match('/[A-Za-z]/', strip_tags($text));
    // $output= preg_match('/[\p{Hiragana}\p{Katakana}\p{Han}]/u', strip_tags($text));
    // dd($output);
});

Route::get('downloadfile', [ tests3fileupload::class, 'downloadfile' ]);
Route::get('testimage', [ tests3fileupload::class, 'index' ]);
Route::post('testimagestore', [ tests3fileupload::class, 'store' ]);

Route::get('/test1', function () {
    $colorArray=[];
    $colorArray['name']="Orange";
    $colorArray['company_id']=1;
    $colorArray['workspace_id']=2;
    $colorArray['user_id']=3;
    $colorArray['staff_id']=4;
    $colorArray['is_default']='1';
    $colorArray['status']='1';
    $colorArray['created_by']='1';
    $colorArray['created_at']=date("Y-m-d H:i:s");
    $colorArray['updated_at']=date("Y-m-d H:i:s");
   // DB::table('users')->insert($colorArray);
  //  Color::insert($colorArray);


  $taskDetails=[];
  $task=[
  ['id'=>'1','name'=>'Fabric Order','startDate'=>"2022-07-01",'startDate'=>"2022-07-15",'personInCharge'=>"saran"],
  ['id'=>'2','name'=>'Fabric Shipment','startDate'=>"2022-07-01",'startDate'=>"2022-07-15",'personInCharge'=>"saran"]
  ];
  $contract=[
    ['id'=>'1','name'=>'Lc Opening','startDate'=>"2022-07-01",'startDate'=>"2022-07-15",'personInCharge'=>"saran"],
    ['id'=>'2','name'=>'Sk Details','startDate'=>"2022-07-01",'startDate'=>"2022-07-15",'personInCharge'=>"saran"]
    ];
   $taskDetails['Fabric']=$task;
   $taskDetails['Contract']=$contract;
   print_r($taskDetails);
});

Route::get('/get-color', [Color::class, 'index']);

Route::get('/test', function () {

//$path = Storage::disk('s3')->put('images', $image_path);

//echo $awsCompanyPath = Storage::disk('local')->get('public/sampleLogo.png');
//Storage::disk('s3')->put($path,file_get_contents($file));

});
Route::get('/sent-email', [testEmail::class, 'index']);

// Route::get('/pendingTaskPDF',function(){
//     return view('pendingTaskPDF');
// });

Route::get('/pendingTaskPDF',[testEmail::class, 'getDataForPDF']);
Route::get('/mailOrderCreation', function(){
    return view('CreateOrder');
});

Route::get('/planExpiry', function(){
    return view('UserPlanTrialEnded');
});

Route::get("/pendingProductionPDF",[testEmail::class,'getProductionData']);
Route::get("/dailyUpdatesPDF",[testEmail::class,'getDailyDataPDF']);
Route::get("/orderPDF",[testEmail::class,'orderReport']);
Route::get("/planPaymentConfirm",function(){
    return view('PlanPaymentConfirmation');
});

Route::get("/addPermissions",function(){
    return view('AddPermissions');
});

Route::post("/add-permissions-for-users",[AddPermissionForUsers::class,'addPermissionForRoles'])->name('addPermissionUsers');


Route::get('/get-s3-pdf', function(){
    return Storage::disk('s3')->url('1658892278_Eagle/Orders/1659346656_pendingTasksPDF.pdf');

});
Route::get('/get-s3-temppdf', function(){
    return Storage::disk('s3')->temporaryUrl('1658892278_Eagle/Orders/1659346656_pendingTasksPDF.pdf', '+2 minutes');

});
