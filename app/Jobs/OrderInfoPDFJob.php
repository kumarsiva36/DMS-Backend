<?php

namespace App\Jobs;

use App\Mail\staffOrderInviteMail;
use App\Models\OrderContacts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Jobs\staffOrderInviteJob;
use App\Common\GetUserLanguage;
use App\Models\Staff;
use App\Models\Workspace;
use App\Models\Order;

class OrderInfoPDFJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $details;
    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*Order basic info pdf creation starts*/
        OrderContacts::create_orderinfo_pdf($this->details);
        $filePath = public_path() . '/OrderInfo';
        if (!file_exists($filePath)) {
            File::makeDirectory($filePath, 0777, true, true);
        }
        $pdf_path = $filePath.'/order_info_'.$this->details->order_id.".pdf";
        if(!file_exists($pdf_path)) {
            OrderContacts::create_orderinfo_pdf($this->details);
        }else{
            $orderNo = Order::where('id',$this->details->order_id)->where('company_id',$this->details->company_id)->where('workspace_id',$this->details->workspace_id)
                        ->select('order_no','style_no')->first();
            $workspaceName = (Workspace::where('id',$this->details->workspace_id)->where('company_id',$this->details->company_id)->first())->name;
            foreach ($this->details->contacts as $contact){
                /* For Contact inivite mail for the order*/
                $language = GetUserLanguage::getLanguageOfUserWithId($this->details->company_id,$this->details->workspace_id,"Staff",$contact['staff_id']);
                $details=[];
                $staff = Staff::where('id',$contact['staff_id'])->first();
                $details['to']=$staff->email;
                $details['userName']=$staff->first_name." ".$staff->last_name;
                $details['workspaceName'] = $workspaceName;
                $details['orderNo'] = $orderNo->order_no;
                $details['language'] = $language;
                $details['pdf_path'] =$pdf_path;
                //staffOrderInviteJob::dispatch($details);

                $to = $staff->email;
                $emailDetails = new staffOrderInviteMail($details);
                Mail::to($to)->locale($details['language'])->send($emailDetails);

            }
        }
        /*Order basic info pdf creation end*/

    }
}
