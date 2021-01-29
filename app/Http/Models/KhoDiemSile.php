<?php


namespace App\Http\Models;


class KhoDiemSile extends KhoDiem
{
    public $timestamps = FALSE;
    const table_name = 'io_khodiemsile';
    protected $table = self::table_name;
    static $unguarded = TRUE;
}