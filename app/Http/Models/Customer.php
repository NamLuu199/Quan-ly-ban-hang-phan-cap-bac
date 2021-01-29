<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Customer extends Member
{
    public $timestamps = false;
    const table_name = 'io_customers';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    static $basicFiledsForBuildTree = ['account', '_id', 'parent_id', 'ma_gioi_thieu', 'status', 'name', 'verified', 'phone', 'email', 'level_doanhthu', 'recruits', 'created_at'];
    protected $dates = [];
    protected $dateFormat = 'd/m/Y';
    const IS_DAILY = 'daily';// ĐẠI LÝ
    const IS_CTV = 'ctv';// ĐẠI LÝ
    const IS_MPMART = 'mpmart';// ĐẠI LÝ
    const LEVEL = 'step';// ĐẠI LÝ
    const floor = 5;

    static function getByPhone($alias)
    {
        $where = [
            'phone' => $alias
        ];
        return self::where($where)->first();
    }

    static function getByEmail($alias)
    {
        $where = [
            'email' => $alias
        ];
        return self::where($where)->first();
    }

    static function getListLevel($selected = FALSE) {
        $listStatus = [
            self::IS_CTV.'_'.self::LEVEL.'1' => ['id' => self::IS_CTV.'_'.self::LEVEL.'1', 'style' => 'primary', 'text' => 'Cộng tác viên mức 1', 'text-action' => 'Cộng tác viên mức 1'],
            self::IS_CTV.'_'.self::LEVEL.'2' => ['id' => self::IS_CTV.'_'.self::LEVEL.'2', 'style' => 'primary', 'text' => 'Cộng tác viên mức 2', 'text-action' => 'Cộng tác viên mức 2'],
            self::IS_CTV.'_'.self::LEVEL.'3' => ['id' => self::IS_CTV.'_'.self::LEVEL.'3', 'style' => 'primary', 'text' => 'Cộng tác viên mức 3', 'text-action' => 'Cộng tác viên mức 3'],
            self::IS_DAILY.'_'.self::LEVEL.'1' => ['id' => self::IS_DAILY.'_'.self::LEVEL.'1', 'style' => 'primary', 'text' => 'Đại lý mức 1', 'text-action' => 'Đại lý mức 1'],
            self::IS_DAILY.'_'.self::LEVEL.'2' => ['id' => self::IS_DAILY.'_'.self::LEVEL.'2', 'style' => 'primary', 'text' => 'Đại lý mức 2', 'text-action' => 'Đại lý mức 2'],
            self::IS_DAILY.'_'.self::LEVEL.'3' => ['id' => self::IS_DAILY.'_'.self::LEVEL.'3', 'style' => 'primary', 'text' => 'Đại lý mức 3', 'text-action' => 'Đại lý mức 3'],
            self::IS_MPMART.'_'.self::LEVEL.'1' => ['id' => self::IS_MPMART.'_'.self::LEVEL.'1', 'style' => 'primary', 'text' => 'Mpmart mức 1', 'text-action' => 'Mpmart mức 1'],
            self::IS_MPMART.'_'.self::LEVEL.'2' => ['id' => self::IS_MPMART.'_'.self::LEVEL.'2', 'style' => 'primary', 'text' => 'Mpmart mức 2', 'text-action' => 'Mpmart mức 2'],
            self::IS_MPMART.'_'.self::LEVEL.'3' => ['id' => self::IS_MPMART.'_'.self::LEVEL.'3', 'style' => 'primary', 'text' => 'Mpmart mức 3', 'text-action' => 'Mpmart mức 3'],
        ];

        if($selected && !isset($listStatus[$selected])) {
            return false;
        }
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    static function getLevel($selected, $case = false)
    {
        $list = self::getListLevel($selected);
        if (isset($list[$selected])) {
            return $list[$selected];
        }
        return [
            'id' => 0,
            'style' => 'warning',
            'text' => 'Không xác định: ' . $selected,
            'text-action' => 'Không xác định',
        ];
    }

    static function buildTree(array &$menu_data, $parent_id = '0', $selected = [], $loop = 0) {
        $data = [];
        foreach ($menu_data as $k => &$item) {
            if ($item['parent_id'] == $parent_id) {
                $children = self::buildTree($menu_data, $item['ma_gioi_thieu']??$item['account']);
                if ($children) {
                    $item['children'] = $children;
                }
                $data[@$item['account']] = $item;
                unset($menu_data[$k]);
            }
        }
        return $data;
    }

    public static function buildTreeNguoc($cur_id,&$data,$account = '',$dept = 1, &$temp = 0)
    {
        if($temp < $dept || $dept === 'all')
            $cur_item = Customer::select(self::$basicFiledsForBuildTree)->where('status', '!=', Customer::STATUS_INACTIVE)->where(!$cur_id ? 'account' : 'ma_gioi_thieu', !$cur_id ? $account : $cur_id)->first();
        if(!empty($cur_item)) {
            $cur_item = $cur_item->toArray();
            $p_item = Customer::select(self::$basicFiledsForBuildTree)->where('status', '!=', Customer::STATUS_INACTIVE)->where('ma_gioi_thieu', $cur_item['parent_id'])->first();
            if(!empty($p_item)) {
                $p_item = $p_item->toArray();
                $temp++;
                $data[] = $p_item;
                self::buildTreeNguoc($p_item['ma_gioi_thieu'], $data,$p_item['account'],$dept,$temp);
            }
        }
        return $data;
    }

    // hàm build cây bao gồm cả account gốc
    public static function buildTreeNguocBaoGomCaGoc($cur_id,&$data,$account = '',$dept = 1, &$temp = 0)
    {
        if($temp < $dept)
            $cur_item = Customer::select('account', '_id', 'parent_id', 'ma_gioi_thieu', 'status', 'name', 'verified', 'phone', 'email')->where('status', '!=', Customer::STATUS_INACTIVE)->where(!$cur_id ? 'account' : 'ma_gioi_thieu', !$cur_id ? $account : $cur_id)->first();
        if(!empty($cur_item)) {
            $cur_item = $cur_item->toArray();
            $p_item = Customer::select('account', '_id', 'parent_id', 'ma_gioi_thieu', 'status', 'name', 'verified', 'phone', 'email')->where('status', '!=', Customer::STATUS_INACTIVE)->where('ma_gioi_thieu', $cur_item['parent_id'])->first();
            if(!empty($p_item)) {
                $p_item = $p_item->toArray();
                $temp++;
                $data[] = $cur_item;
                self::buildTreeNguoc($cur_item['ma_gioi_thieu'], $data,$cur_item['account'],$dept,$temp);
            }
        }
        return $data;
    }

    // hàm build full cây bao gồm cả account gốc
    public static function buildFullTreeNguocBaoGomCaGoc($cur_id,&$data,$account = '',$dept = 1, &$temp = 0)
    {
        $cur_item = Customer::select(self::$basicFiledsForBuildTree)->where('status', '!=', Customer::STATUS_INACTIVE)
            ->where(!$cur_id ? 'account' : 'ma_gioi_thieu', !$cur_id ? $account : $cur_id)->first();
        if(!empty($cur_item)) {
            $cur_item = $cur_item->toArray();
            $p_item = Customer::select(self::$basicFiledsForBuildTree)->where('status', '!=', Customer::STATUS_INACTIVE)
                ->where('ma_gioi_thieu', $cur_item['parent_id'])->first();
            if(!empty($p_item)) {
                $p_item = $p_item->toArray();
                $data[] = $cur_item;
                self::buildTreeNguoc($cur_item['ma_gioi_thieu'], $data,$cur_item['account'], 'all');
            }
        }
        return $data;
    }

    public static function checkF($tai_khoan_nguon, $tai_khoan_nhan)
    {
        $temp= [];
        self::buildTreeNguoc('', $temp, $tai_khoan_nguon, self::floor);
        if(!empty($temp)) {
            foreach ($temp as $f => $t) {
                if($t['account'] == $tai_khoan_nhan) {
                    return ++$f;
                }
            }
        }
        return 0;
    }

    static function getTaiKhoanToSaveDb($customer)
    {
        return [
            'id'      => (string)@$customer['_id']??$customer['id'],
            'name'    => @$customer['name'],
            'account' => $customer['account'],
            'email' => @$customer['email'],
            'phone' => @$customer['phone'],
            'verified' => @$customer['verified'],
        ];
    }

    static function buildLinkMaGioiThieu($ma_gioi_thieu) {
        if(!$ma_gioi_thieu) {
            return 'javascript:void(0);';
        }
        return tv_admin_link('auth/register?ma_gioi_thieu=' . $ma_gioi_thieu . '&token=' . Helper::buildTokenString($ma_gioi_thieu));
    }

    static function getLsCustomerByLevelDoanhThu($level_group, $groupBy = false) {
        if(!$level_group) {
            return false;
        }
        $where = [
            'status' => self::STATUS_ACTIVE,
            'level_doanhthu' => [
                '$in' => $level_group
            ],
        ];
        $data = self::where($where)->select(self::$basicFiledsForBuildTree)->get();
        if($groupBy) {
            $data = $data->groupBy('level_doanhthu');
        }
        return $data->toArray();
    }

    static function getCountLsCustomerByLevelDoanhThu($level_group) {
        if(!$level_group) {
            return false;
        }
        $where = [
            'status' => self::STATUS_ACTIVE,
            'level_doanhthu' => $level_group,
        ];
        return self::where($where)->count();
    }

}
