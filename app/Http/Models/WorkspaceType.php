<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceType extends Model
{
    use HasFactory;

    protected $table = 'workspace_type';

    public static function getAllWorkspaceType(){
        $types = WorkspaceType::all();
        return $types;
    }
}
