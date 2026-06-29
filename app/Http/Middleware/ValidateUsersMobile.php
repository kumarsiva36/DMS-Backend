<?php

namespace App\Http\Middleware;

use App\Common\CommonApp;
use App\Models\CompanySettings;
use Closure;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;

class ValidateUsersMobile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $spltReq=explode("/",$request->server("REQUEST_URI"));
         if($spltReq[1] == "api"){
        if($spltReq[2] != "get-company-settings" &&
        $spltReq[2] != "update-plan" &&
        $spltReq[2] != "confirm-plan-payment"){

            $request1= CommonApp::apiDecrypt($request->getContent());
            if(isset($request1->company_id)){
                if(isset($request1->company_id) && $request1->company_id != ""){

                    try{
                    $plan = CompanySettings::where('id', $request1->company_id)->select('account_expire_at')->first();
                    if($plan->account_expire_at != "" || $plan->account_expire_at != null){
                        $today = new DateTime(date("Y-m-d"));
                        $planExpiryDate = new DateTime(date('Y-m-d', strtotime($plan->account_expire_at)));
                        $dayDiff = (int)$today->diff($planExpiryDate)->format("%r%a");
                        if($dayDiff < 0){
                            try{
                                $tokenId = (new \Lcobucci\JWT\Token\Parser(new \Lcobucci\JWT\Encoding\JoseEncoder()))
                                ->parse($request->bearerToken())->claims()->all()['jti'];
                                $theToken = Token::find($tokenId);
                                $theToken->revoked = 1;
                                $theToken->save();
                              //  dd($theToken);
                            }catch(Exception $e){
                                Log::info($e->getMessage());
                            }
                            $res = json_encode(["status_code"=>401,"status" =>"failure","message"=>"Plan Expired"]);
                            // $token = $request->user()->token();
                            // $token->revoke();
                            return response(CommonApp::apiEncrypt($res));
                        }
                    }
                 }catch(Exception $e){
                   // Log::info($e->getMessage());

                }



                 }
                }




            }
        }

        return $next($request);
    }
}
