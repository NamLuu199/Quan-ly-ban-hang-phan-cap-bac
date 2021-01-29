<?php


namespace App\Http\Models;


class KichHoatTaiKhoan extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_kichhoattaikhoan';
    protected $table = self::table_name;
    static $unguarded = TRUE;
}