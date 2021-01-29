<?php


namespace App\Http\Models;


class TongDoanhThu extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_tongdoanhthu';
    protected $table = self::table_name;
    static $unguarded = true;

    const LV1 = 'LV1';
    const LV2 = 'LV2';
    const LV3 = 'LV3';
    const LV4 = 'LV4';
    const LV5 = 'LV5';
    const LV6 = 'LV6';

    static function getListStatus($selected = FALSE)
    {
        $listStatus = [
            self::LV1 => ['id' => self::LV1, 'style' => 'success', 'text' => 'Cấp 1', 'text-action' => 'Cấp 1'],
            self::LV2 => ['id' => self::LV2, 'style' => 'secondary', 'text' => 'Cấp 2', 'text-action' => 'Cấp 2'],
            self::LV3 => ['id' => self::LV3, 'style' => 'warning', 'text' => 'Cấp 3', 'text-action' => 'Cấp 3'],
            self::LV4 => ['id' => self::LV4, 'style' => 'warning', 'text' => 'Cấp 4', 'text-action' => 'Cấp 4'],
            self::LV5 => ['id' => self::LV5, 'style' => 'success', 'text' => 'Cấp 5', 'text-action' => 'Cấp 5'],
            self::LV6 => ['id' => self::LV6, 'style' => 'success', 'text' => 'Cấp 6', 'text-action' => 'Cấp 6'],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    /**
     * @param $selected
     * @return array|mixed
     * @note: dùng trong các view tránh if els quá nhiều
     */
    static function getStatus($selected, $case = false)
    {
        if($case) {
            $list = self::getListStatus($selected, $case);
        }else {
            $list = self::getListStatus($selected);
        }

        if (isset($list[$selected])) {
            return $list[$selected];
        } else {
            return [
                'id' => 0,
                'style' => 'warning',
                'text' => 'Không xác định: ' . $selected,
                'text-action' => 'Không xác định',
            ];
        }
    }
}