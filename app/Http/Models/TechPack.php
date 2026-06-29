<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechPack extends Model
{
    use HasFactory;
    protected $table = 'techpack';
    protected $fillable = [
       'company_id','workspace_id','user_id','staff_id','po_no','po_id','style_no','article_id','article_name','category_id','category_name','fabric_id','fabric_name','size_id','size_name','is_publish','status','created_by','created_by_type','updated_by','update_by_type','reference_id','created_at','updated_at'
    ];

    public static function getTechpackList($whereCondition,$request){
        $parent_po_id = $request->parentpo_id ?? 0;
        if ($request->staff_id > 0){
            $qry = TechPack::select('techpack.*')
                        ->selectSub(function($query) use ($request) {
                            $query->from('techpack_comments')
                                ->selectRaw('COUNT(id)')
                                ->whereColumn('techpack_comments.techpack_id', 'techpack.id')
                                ->whereRaw("NOT FIND_IN_SET(?, techpack_comments.staff_read)",[$request->staff_id])
                                //->where('techpack_comments.created_by_type','!=','Staff')
                                ->where('techpack_comments.created_by','!=',$request->staff_id);
                        }, 'unread_count')
                        ->selectSub(function($query) use ($request) {
                            $query->from('techpack as tp')
                                ->selectRaw('COUNT(id)')
                                ->whereColumn('tp.parent_po_id', 'techpack.parent_po_id')
                                ->where('tp.parent_po_id','!=','0')
                                ->groupBy('tp.parent_po_id');
                        }, 'po_count')
                    ->where($whereCondition);
                if($parent_po_id > 0){
                    $qry->orderByRaw('parent_po_id = '.$parent_po_id.' DESC');
                    $qry->orderBy('parent_po_id', 'DESC');
                }
                else
                    $qry->orderBy('parent_po_id', 'DESC');

                    $qry->orderBy('id', 'DESC');
            $res = $qry->paginate(20, ['*'], 'page', $request->page);

        }else{
            $qry = TechPack::select('techpack.*')
                        ->selectSub(function($query) {
                            $query->from('techpack_comments')
                                ->selectRaw('COUNT(id)')
                                ->whereColumn('techpack_comments.techpack_id', 'techpack.id')
                                ->where('techpack_comments.admin_read','0')
                                ->where('techpack_comments.created_by_type','!=','Admin');
                        }, 'unread_count')
                        ->selectSub(function($query) use ($request) {
                            $query->from('techpack as tp')
                                ->selectRaw('COUNT(id)')
                                ->whereColumn('tp.parent_po_id', 'techpack.parent_po_id')
                                ->where('tp.parent_po_id','!=','0')
                                ->groupBy('tp.parent_po_id');
                        }, 'po_count')
                        ->where($whereCondition);
                if($parent_po_id > 0){
                    $qry->orderByRaw('parent_po_id = '.$parent_po_id.' DESC');
                    $qry->orderBy('parent_po_id', 'DESC');
                }
                else
                    $qry->orderBy('parent_po_id', 'DESC');

                    $qry->orderBy('id', 'DESC');
            $res = $qry->paginate(20, ['*'], 'page', $request->page);
        }
        //dd($res);
        return $res;
    }

    public static function getCommentsCount($request){
        $whereCondition = [
            ['techpack.workspace_id', '=', $request->workspace_id],
            ['techpack.company_id', '=', $request->company_id]
        ];
        if ($request->staff_id > 0){
            $res = TechPack::select('techpack.id')
                        ->selectSub(function($query) use ($request) {
                            $query->from('techpack_comments')
                                ->selectRaw('COUNT(id)')
                                ->whereColumn('techpack_comments.techpack_id', 'techpack.id')
                                ->whereRaw("NOT FIND_IN_SET(?, techpack_comments.staff_read)",[$request->staff_id])
                                //->where('techpack_comments.created_by_type','!=','Staff')
                                ->where('techpack_comments.created_by','!=',$request->staff_id);
                        }, 'unread_count')
                    ->where($whereCondition)->orderBy('id', 'DESC')->get();

        }else{
           $res = TechPack::select('techpack.id')
                        ->selectSub(function($query) {
                            $query->from('techpack_comments')
                                ->selectRaw('COUNT(id)')
                                ->whereColumn('techpack_comments.techpack_id', 'techpack.id')
                                ->where('techpack_comments.admin_read','0')
                                ->where('techpack_comments.created_by_type','!=','Admin');
                        }, 'unread_count')
                    ->where($whereCondition)->orderBy('id', 'DESC')->get();
        }
        $count=0;
        foreach($res as $r){
            if($r->unread_count > 0){
                $count++;
            }
        }

        return $count;
    }
}
