<?php


namespace App\Http\Models;


class ViTieuDungSiLe extends ViTieuDung
{
    public $timestamps = FALSE;
    const table_name = 'io_vitieudungsile';
    protected $table = self::table_name;
    static $unguarded = TRUE;
}