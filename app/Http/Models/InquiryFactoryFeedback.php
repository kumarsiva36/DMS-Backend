<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InquiryContact;
use Illuminate\Support\Facades\DB;

class InquiryFactoryFeedback extends Model
{
    use HasFactory;

    protected $table = 'inquiry_factory_feedback';

    public static function get_factory_ratings($request){
        $factory_contact_id = InquiryContact::where('factory_id', $request->factory_id)->pluck('id')->first();
        $data=array();
        if($factory_contact_id > 0){
            $where =[
                ['factory_contact_id','=',$factory_contact_id],
                ['buyer_id','=',$request->user_id]
            ];

            $result = InquiryFactoryFeedback::where($where)
                        ->join('inquiry','inquiry.id','inquiry_id')
                        ->select('inquiry_factory_feedback.*','inquiry.style_no',
                            //DB::raw('AVG(lowest_price) as lowest_price_avg'),
                            // DB::raw('SUM(ontime_delivery) as ontime_delivery_avg'),
                            // DB::raw('SUM(vendor_buyer_relation) as vendor_buyer_relation_avg'),
                            // DB::raw('SUM(sample_submission) as sample_submission_avg'),
                            // DB::raw('SUM(communication) as communication_avg'),
                            // DB::raw('SUM(good_sell_through) as good_sell_through_avg'),
                            // DB::raw('SUM(collaborative_approach) as collaborative_approach_avg'),
                            // DB::raw('SUM(less_quality_issue) as less_quality_issue_avg')
                            )
                        ->orderBy('inquiry_factory_feedback.created_at','desc')
                        ->get();
            $count = $result->count();
            $lowest_price=$ontime_delivery=$vendor_buyer_relation=$sample_submission=$communication=$less_quality_issue=$good_sell_through=$collaborative_approach= array();
            $i=0;
            foreach($result as $res){
                $lowest_price[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $lowest_price[$i]['style_no'] = $res->style_no;
                $lowest_price[$i]['rating'] = $res->lowest_price;
                $lowest_price[$i]['comments'] = $res->lowest_price_comments;
                //$lowest_price[$i]['avg'] = round($res->lowest_price_avg);

                $ontime_delivery[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $ontime_delivery[$i]['style_no'] = $res->style_no;
                $ontime_delivery[$i]['rating'] = $res->ontime_delivery;
                $ontime_delivery[$i]['comments'] = $res->ontime_delivery_comments;
                //$ontime_delivery[$i]['avg'] = round($res->ontime_delivery_avg/$count);

                $vendor_buyer_relation[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $vendor_buyer_relation[$i]['style_no'] = $res->style_no;
                $vendor_buyer_relation[$i]['rating'] = $res->vendor_buyer_relation;
                $vendor_buyer_relation[$i]['comments'] = $res->vendor_buyer_relation_comments;
                //$vendor_buyer_relation[$i]['avg'] = round($res->vendor_buyer_relation_avg/$count);

                $sample_submission[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $sample_submission[$i]['style_no'] = $res->style_no;
                $sample_submission[$i]['rating'] = $res->sample_submission;
                $sample_submission[$i]['comments'] = $res->sample_submission_comments;
                //$sample_submission[$i]['avg'] = round($res->sample_submission_avg/$count);

                $communication[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $communication[$i]['style_no'] = $res->style_no;
                $communication[$i]['rating'] = $res->communication;
                $communication[$i]['comments'] = $res->communication_comments;
                //$communication[$i]['avg'] = round($res->communication_avg/$count);

                $less_quality_issue[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $less_quality_issue[$i]['style_no'] = $res->style_no;
                $less_quality_issue[$i]['rating'] = $res->less_quality_issue;
                $less_quality_issue[$i]['comments'] = $res->less_quality_issue_comments;
                //$less_quality_issue[$i]['avg'] = round($res->less_quality_issue_avg/$count);

                $good_sell_through[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $good_sell_through[$i]['style_no'] = $res->style_no;
                $good_sell_through[$i]['rating'] = $res->good_sell_through;
                $good_sell_through[$i]['comments'] = $res->good_sell_through_comments;
                //$good_sell_through[$i]['avg'] = round($res->good_sell_through_avg/$count);

                $collaborative_approach[$i]['inquiry_id'] = "IN-".$res->inquiry_id;
                $collaborative_approach[$i]['style_no'] = $res->style_no;
                $collaborative_approach[$i]['rating'] = $res->collaborative_approach;
                $collaborative_approach[$i]['comments'] = $res->collaborative_approach_comments;
                //$collaborative_approach[$i]['avg'] = round($res->collaborative_approach_avg/$count);

                $i++;
            }
            $data['lowest_price']=$lowest_price;
            $data['ontime_delivery']=$ontime_delivery;
            $data['vendor_buyer_relation']=$vendor_buyer_relation;
            $data['sample_submission']=$sample_submission;
            $data['communication']=$communication;
            $data['less_quality_issue']=$less_quality_issue;
            $data['good_sell_through']=$good_sell_through;
            $data['collaborative_approach']=$collaborative_approach;
            $data['no_of_orders']=$count;

        }

        return $data;

    }
    /*Get factory feedback list*/
    public static function get_factory_feedback($request){
        $whereConditions=[
            ['inquiry_factory_feedback.company_id','=',$request->company_id],
            ['inquiry_factory_feedback.workspace_id','=',$request->workspace_id]
        ];

        if(isset($request->inquiry_id) && $request->inquiry_id!=''){
            $whereConditions[]=['inquiry_factory_feedback.inquiry_id','=',$request->inquiry_id];
        }
        if(isset($request->factory_id) && $request->factory_id!=''){
            $whereConditions[]=['inquiry_factory_feedback.factory_contact_id','=',$request->factory_id];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_factory_feedback.created_at','>=',$from];
            $whereConditions[]=['inquiry_factory_feedback.created_at','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['inquiry_factory_feedback.created_at','>=',$from];
            $whereConditions[]=['inquiry_factory_feedback.created_at','<=',$to];
        }

        $res = InquiryFactoryFeedback::where($whereConditions)
             ->join('inquiry_contact','inquiry_contact.id','inquiry_factory_feedback.factory_contact_id')
             ->select('inquiry_factory_feedback.*','inquiry_contact.factory',
             DB::raw('DATE_FORMAT(inquiry_factory_feedback.created_at,"%Y-%m-%d") as created_date'))
             ->orderBy('created_at','desc')
             ->get();
        return $res;
    }
    /*Get feedback factory list*/
    public static function get_feedback_factories($request){
        $whereConditions=[
            ['inquiry_factory_feedback.company_id','=',$request->company_id],
            ['inquiry_factory_feedback.workspace_id','=',$request->workspace_id]
        ];

        $res = InquiryFactoryFeedback::where($whereConditions)
             ->join('inquiry_contact','inquiry_contact.id','inquiry_factory_feedback.factory_contact_id')
             ->select('inquiry_contact.id','inquiry_contact.factory')
             ->orderBy('inquiry_contact.factory','asc')
             ->get();
        return $res;
    }
    /*Get feedback inquiries list*/
    public static function get_feedback_inquiries($request){
        $whereConditions=[
            ['inquiry_factory_feedback.company_id','=',$request->company_id],
            ['inquiry_factory_feedback.workspace_id','=',$request->workspace_id]
        ];

        $res = InquiryFactoryFeedback::where($whereConditions)
             ->select(DB::raw('DISTINCT(inquiry_id) as id'))
             ->orderBy('inquiry_id','asc')
             ->get();
        return $res;
    }
}
