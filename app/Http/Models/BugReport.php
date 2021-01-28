<?php

namespace App\Http\Models;

class BugReport extends BaseModel
{
    //
    const table_name = 'bug_report';
    protected $table = self::table_name;
    protected $fillable = [];
    static $unguarded = true;
   
}
