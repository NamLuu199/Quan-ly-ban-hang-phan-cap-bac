<?php

namespace App\Http\Models;
use App\Elibs\eView;
use App\Elibs\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Orders extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_orders';
    protected $table = self::table_name;
    static $unguarded = TRUE;

    const DEBT_YES = 'yes'; // nợ vl
    const DEBT_NO = 'no';   // không nợ
    const MP_MART = 'mp_mart';
    const min_mpg = 500000;
    const min_dai_ly = 20000000;
    const min_mpmart = 300000000;

    const ORDER_BUY_MPG = 'order_buy_mpg';
    const ORDER_CHUYENDIEM_MPG = 'order_chuyendiem_mpg';
    const ORDER_HOAHONG = 'order_hoahong';

    const EVERYDAY_PERCENT_CTV_CHIETKHAU_TICHLUY = 'EVERYDAY_PERCENT_CTV_CHIETKHAU_TICHLUY';
    const EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG = 'EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG';
    const EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY = 'EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY';
    const EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG = 'EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG';
    const PERCENT_DEBT_CTV = 0.25;
    const PERCENT_CHIETKHAU = 0.8;
    const PERCENT_CONGNO_DEBT_YES = 0.25;
    const PERCENT_CONGNO_PHIVANCHUYEN = 0.6;
    const PERCENT_CHIETKHAU_TO_TICHLUY_DEBT_YES = 0.002;
    static $PERCENT_CHIETKHAU_TO_TICHLUY_DEBT_YES = 0.002;
    static $PERCENT_CHIETKHAU_TO_TIEUDUNG_DEBT_NO = 0.005;

    static $objectRegister = [
        self::DEBT_YES => [
            'key' => self::DEBT_YES,
            'name' => 'Có nợ',
        ],
        self::DEBT_NO => [
            'key' => self::DEBT_NO,
            'name' => 'Không nợ',
        ],
        self::MP_MART => [
            'key' => self::MP_MART,
            'name' => 'MPMart',
        ],
    ];

    static function getPercentChietKhau($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_chietkhau'])) {
            return 0.8;
        }
        return $data['percent_chietkhau']/100;
    }

    static function getPercentCongNoDebtYes($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_congno_debt_yes'])) {
            return 0.25;
        }
        return $data['percent_congno_debt_yes']/100;
    }

    static function getPercentChietKhauDaiLy($old = false, $step2 = false, $step3 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step2) {
            return $data['percent_chietkhau_daily_step2']/100 - self::getPercentChietKhauCongTieuDungDaiLy($old, false, true);
        }
        if($step3) {
            return $data['percent_chietkhau_daily_step3']/100 - self::getPercentChietKhauCongTieuDungDaiLy($old, true);
        }
        if (!isset($data['percent_chietkhau_daily_step1'])) {
            return 0.65 - self::getPercentChietKhauCongTieuDungDaiLy($old);
        }
        return $data['percent_chietkhau_daily_step1']/100 - self::getPercentChietKhauCongTieuDungDaiLy($old);
    }

    static function getPercentChietKhauCtv($old = false, $step2 = false, $step3 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step2) {
            return $data['percent_chietkhau_ctv_step2']/100;
        }
        if($step3) {
            return $data['percent_chietkhau_ctv_step3']/100;
        }
        if (!isset($data['percent_chietkhau_ctv_step1'])) {
            return 0.3;
        }
        return $data['percent_chietkhau_ctv_step1']/100;
    }

    static function getPercentChietKhauMpMart($old = false, $step2 = false, $step3 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step2) {
            return $data['percent_chietkhau_mpmart_step2']/100 - self::getPercentChietKhauCongTieuDungMpMart($old, false, true);
        }
        if($step3) {
            return $data['percent_chietkhau_mpmart_step3']/100 - self::getPercentChietKhauCongTieuDungMpMart($old, true);
        }
        if (!isset($data['percent_chietkhau_mpmart_step1'])) {
            return 0.8 - self::getPercentChietKhauCongTieuDungMpMart($old, false, false);
        }
        return $data['percent_chietkhau_mpmart_step1']/100 - self::getPercentChietKhauCongTieuDungMpMart($old, false, false);;
    }

    static function getPercentKhoDiemTieuDungDeliveredDaiLyTinh($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_khodiem_tieudung_delivered_daily_tinh'])) {
            return 0.06;
        }
        return $data['percent_khodiem_tieudung_delivered_daily_tinh']/100;
    }

    static function getPercentKhoDiemTieuDungDeliveredDaiLyHuyen($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_khodiem_tieudung_delivered_daily_huyen'])) {
            return 0.045;
        }
        return $data['percent_khodiem_tieudung_delivered_daily_huyen']/100;
    }

    static function getPercentKhoDiemTieuDungDeliveredDaiLyTinhForFull($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_khodiem_tieudung_delivered_full_f'])) {
            return 0.015;
        }
        return $data['percent_khodiem_tieudung_delivered_full_f']/100;
    }

    static function getIsCTV($old = false, $step2 = false, $step3 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step2) {
            return $data['order_is_ctv_max_step2'];
        }
        if($step3) {
            return $data['order_is_ctv_max_step3'];
        }
        if (!isset($data['order_is_ctv_max_step1'])) {
            return 2000000;
        }
        return $data['order_is_ctv_max_step1'];
    }

    static function getIsDaiLy($old = false, $step2 = false, $step3 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step2) {
            return $data['order_is_daily_max_step2'];
        }
        if($step3) {
            return $data['order_is_daily_max_step3'];
        }
        if (!isset($data['order_is_daily_max_step1'])) {
            return 50000000;
        }
        return $data['order_is_daily_max_step1'];
    }

    static function getIsMPMart($old = false, $step2 = false, $step3 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step2) {
            return $data['order_is_mpmart_max_step2'];
        }
        if($step3) {
            return $data['order_is_mpmart_max_step3'];
        }
        if (!isset($data['order_is_mpmart_max_step1'])) {
            return 500000000;
        }
        return $data['order_is_mpmart_max_step1'];
    }

    static function getPercentChietKhauCongTieuDungDaiLy($old = false, $step1 = false, $step2 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step1) {
            return 0.3;
            return $data['percent_chietkhau_congtieudung_daily_step1']/100;
        }
        if($step2) {
            return 0.3;
            return $data['percent_chietkhau_congtieudung_daily_step2']/100;
        }
        if (!isset($data['percent_chietkhau_congtieudung_daily'])) {
            return 0.3;
        }
        return $data['percent_chietkhau_congtieudung_daily']/100;
    }

    static function getPercentChietKhauCongTieuDungMpMart($old = false, $step1 = false, $step2 = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if($step1) {
            return 0.3;
            return $data['percent_chietkhau_congtieudung_mpmart_step1']/100;
        }
        if($step2) {
            return 0.3;
            return $data['percent_chietkhau_congtieudung_mpmart_step2']/100;
        }
        if (!isset($data['percent_chietkhau_congtieudung_mpmart'])) {
            return 0.3;
        }
        return $data['percent_chietkhau_congtieudung_mpmart']/100;
    }

    static function getPercentCongNoPhiVanChuyen($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_congno_phivanchuyen'])) {
            return 0.06;
        }
        return $data['percent_congno_phivanchuyen']/100;
    }

    static function getMinMPMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['order_is_mpmart'])) {
            return 500000000;
        }
        return $data['order_is_mpmart'];
    }

    static function getMinDaiLy($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['order_is_daily'])) {
            return 30000000;
        }
        return $data['order_is_daily'];
    }

    static function getMinMPG($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['min_mpg'])) {
            return 500000;
        }
        return $data['min_mpg'];
    }

    static function getMinMPGAfterRegister($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['min_mpg_after_register'])) {
            return 50000;
        }
        return $data['min_mpg_after_register'];
    }

    static function getPrecentDiemHoaHongForF($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        $moneyForF = [
            @$data['muadiem_hoahong_f1']/100,
            @$data['muadiem_hoahong_f2']/100,
            @$data['muadiem_hoahong_f3']/100,
            @$data['muadiem_hoahong_f4']/100,
            @$data['muadiem_hoahong_f5']/100
        ];
        return $moneyForF;
    }

    static function getPrecentDiemMpMartHoaHongForF($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        $moneyForF = [
            @$data['muadiem_mpmart_hoahong_f1']/100,
            @$data['muadiem_mpmart_hoahong_f2']/100,
            @$data['muadiem_mpmart_hoahong_f3']/100,
            @$data['muadiem_mpmart_hoahong_f4']/100,
            @$data['muadiem_mpmart_hoahong_f5']/100
        ];
        return $moneyForF;
    }

    static function getPreCentChietKhauTieuDungForF($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        $moneyForF = [
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f1']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f2']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f3']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f4']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f5']/100
        ];
        return $moneyForF;
    }

    static function getEveryDayPercentChietKhauTichLuyDebtYes($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tichluy_debt_yes'])) {
            return 0.002;
        }
        return $data['everyday_percent_chietkhau_tichluy_debt_yes']/100;
    }

    static function getEveryDayPercentChietKhauTieuDungDebtNo($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tieudung_debt_no'])) {
            return 0.005;
        }
        return $data['everyday_percent_chietkhau_tieudung_debt_no']/100;
    }

    static function getEveryDayPercentChietKhauTichLuyDebtNoCTV($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tichluy_debt_no_ctv'])) {
            return 0.002;
        }
        return $data['everyday_percent_chietkhau_tichluy_debt_no_ctv']/100;
    }

    static function getEveryDayPercentChietKhauTieuDungDebtNoCTV($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tieudung_debt_no_ctv'])) {
            return 0.005;
        }
        return $data['everyday_percent_chietkhau_tieudung_debt_no_ctv']/100;
    }

    static function getEveryDayPercentChietKhauTichLuyDebtNoMpMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tichluy_debt_no_mp_mart'])) {
            return 0.002;
        }
        return $data['everyday_percent_chietkhau_tichluy_debt_no_mp_mart']/100;
    }

    static function getEveryDayPercentChietKhauTieuDungDebtNoMpMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tieudung_debt_no_mp_mart'])) {
            return 0.005;
        }
        return $data['everyday_percent_chietkhau_tieudung_debt_no_mp_mart']/100;
    }

    static function getListDebtMpMart($selected = FALSE, $case = false)
    {
        if($case) {
            switch ($case) {
                case self::ORDER_BUY_MPG:
                {
                    $listStatus = [
                        self::DEBT_YES => ['id' => self::DEBT_YES, 'style' => 'danger', 'text' => 'Có nợ', 'text-action' => 'Có nợ'],
                        self::DEBT_NO => ['id' => self::DEBT_NO, 'style' => 'secondary', 'text' => 'Không nợ', 'text-action' => 'Không nợ'],
                        self::MP_MART => ['id' => self::MP_MART, 'style' => 'success', 'text' => 'MP Mart', 'text-action' => 'MP Mart'],
                    ]; break;
                }
                case self::ORDER_HOAHONG:
                {
                    $listStatus = [
                        self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'style' => 'success', 'text' => 'Chờ', 'text-action' => 'Kích hoạt hiển thị'],
                        self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'style' => 'secondary', 'text' => 'Chờ kích hoạt', 'text-action' => 'Chờ kích hoạt'],
                        self::STATUS_DISABLE => ['id' => self::STATUS_DISABLE, 'style' => 'warning', 'text' => 'Khóa', 'text-action' => 'Hủy'],
                    ]; break;
                }
            }
        }

        if($selected && !isset($listStatus[$selected])) {
            return false;
        }
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    static function getListStatus($selected = FALSE, $case = false)
    {
        if($case) {
            switch ($case) {
                case self::ORDER_BUY_MPG:
                {
                    $listStatus = [
                        self::STATUS_PROCESS_DONE => ['id' => self::STATUS_PROCESS_DONE, 'style' => 'success', 'text' => 'Đã duyệt', 'text-action' => 'Đã duyệt'],
                        self::STATUS_NO_PROCESS => ['id' => self::STATUS_NO_PROCESS, 'style' => 'secondary', 'text' => 'Chờ xử lý', 'text-action' => 'Chờ xử lý'],
                        self::STATUS_DELETED => ['id' => self::STATUS_DELETED, 'style' => 'danger', 'text' => 'Đã xóa', 'text-action' => 'Đã xóa'],
                    ]; break;
                }
                case self::ORDER_HOAHONG:
                {
                    $listStatus = [
                        self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'style' => 'success', 'text' => 'Chờ', 'text-action' => 'Kích hoạt hiển thị'],
                        self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'style' => 'secondary', 'text' => 'Chờ kích hoạt', 'text-action' => 'Chờ kích hoạt'],
                        self::STATUS_DISABLE => ['id' => self::STATUS_DISABLE, 'style' => 'warning', 'text' => 'Khóa', 'text-action' => 'Hủy'],
                    ]; break;
                }
            }
        }

        if($selected && !isset($listStatus[$selected])) {
            return [];
        }
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    static function getMonthsEndRunAuto() {
        return 10;
    }

    static function getOrderDebtByArrIds($ids, $keyBy = false) {
        $now = Helper::getMongoDate('d/m/Y');

        $where = [
            'status' => self::STATUS_PROCESS_DONE,
            '$or' => [
                [
                    'updated_vi_at' => ['$exists' => false],
                ],
                [
                    'updated_vi_at' => ['$lt' => $now],
                ]
            ],
            // chỉ lấy ra những tk có nợ và ko phải là mpmart
            /*'$or' => [
                [
                    'mpmart' => ['$exists' => false],
                ],
                [
                    'debt' => ['$in' => [self::DEBT_YES, self::DEBT_NO]],
                ]
            ],*/
        ];
        $data = self::where($where)->whereIn('_id', $ids)->get();
        if($keyBy) {
            return $data->keyBy($keyBy)->toArray();
        }
        return $data->toArray();
    }

    static function getDanhSachDonHangChuaCapNhatViMoiNgay($keyBy = false) {
        // lấy danh sahcs đơn hàng chưa được 10 tháng cập nhật ví tiêu dùng, tích luỹ mỗi ngày,
        $now = Helper::getMongoDate('d/m/Y');
        $where = [
            'status' => self::STATUS_PROCESS_DONE,
            '$or' => [
                [
                    'end_updated_vi_at' => ['$exists' => false],
                ],
                [
                    'end_updated_vi_at' => ['$gt' => $now],
                ]
            ],
            '$or' => [
                [
                    'updated_vi_at' => ['$exists' => false],
                ],
                [
                    'updated_vi_at' => ['$lt' => $now],
                ]
            ],
        ];
        $data = self::where($where)->get();
        if($keyBy) {
            return $data->keyBy($keyBy)->toArray();
        }
        return $data->toArray();
    }

    static function getDanhSachDonHangCapNhatViMoiNgayTrenSoDuConLai($keyBy = false) {
        // lấy danh sahcs đơn hàng được 10 tháng cập nhật ví tiêu dùng, tích luỹ mỗi ngày,
        $now = Helper::getMongoDate(date('d/m/Y'));
        $where = [
            'status' => self::STATUS_PROCESS_DONE,
            '$or' => [
                [
                    'end_updated_vi_at' => ['$exists' => false],
                ],
                [
                    'end_updated_vi_at' => ['$lt' => $now],
                ]
            ],
            'updated_vi_at' => ['$lt' => $now],
        ];
        $data = self::where($where)->get();
        if($keyBy) {
            return $data->keyBy($keyBy)->toArray();
        }
        return $data->toArray();
    }


    static function getByMaTaiKhoanKichHoat($code, $account) {
        $where = [
            'status' => self::STATUS_NO_PROCESS,
            'tai_khoan_nhan.account' => $account,
            '$or' => [
                ['debt' => ['$exists' => true, '$eq' => 'no']],
                ['debt' => ['$exists' => false]],
            ],
        ];
        return self::where($where)->orderBy('_id', 'DESC')->first();

    }

    static function getTotalDoanhThuLevel($level, $old)
    {
        if ($old) {
            $data = $old;
        } else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['total_doanhthu_level_' . $level])) {
            return false;
        }
        return $data['total_doanhthu_level_' . $level];
    }

    static function getPercentHoaHongDoanhThuLevel($level, $old)
    {
        if ($old) {
            $data = $old;
        } else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_hoahong_doanhthu_level_' . $level])) {
            return false;
        }
        return $data['percent_hoahong_doanhthu_level_' . $level];
    }

    static function checkLevelDoanhThu($tongdoanhthu)
    {
        $data = UnauthorizedPersonnel::getUn();
        if ($tongdoanhthu >= @$data['total_doanhthu_level_1']) {
            return TongDoanhThu::LV1;
        } elseif ($tongdoanhthu >= @$data['total_doanhthu_level_2']) {
            return TongDoanhThu::LV2;
        } elseif ($tongdoanhthu >= @$data['total_doanhthu_level_3']) {
            return TongDoanhThu::LV3;
        } elseif ($tongdoanhthu >= @$data['total_doanhthu_level_4']) {
            return TongDoanhThu::LV4;
        } elseif ($tongdoanhthu >= @$data['total_doanhthu_level_5']) {
            return TongDoanhThu::LV5;
        } elseif ($tongdoanhthu >= @$data['total_doanhthu_level_6']) {
            return TongDoanhThu::LV6;
        }
        return 0;
    }

    static function getPercentLevelDoanhThu($level, $lsObj)
    {
        $percent = self::getTotalPercentLevelDoanhThu($level);
        $percentNew = 0;
        $levelOld = 0;
        $percentOld = 0;
        foreach ($lsObj as $obj) {
            if(!isset($obj['level_doanhthu'])) {
                $obj['level_doanhthu'] = 0;
            }
            // nếu đằng trước mà cấp thấp hơn đằng sau thì trừ
            if(str_replace('LV', '', $obj['level_doanhthu']) < str_replace('LV', '', $level) &&
                str_replace('LV', '', $obj['level_doanhthu']) > str_replace('LV', '', $levelOld)) {
                $percentNew -= $percentOld;
                $percentOld = self::getTotalPercentLevelDoanhThu($obj['level_doanhthu']);
                $percentNew += $percentOld;
            }
            $levelOld = $obj['level_doanhthu'];
        }
        return $percent ? $percent-$percentNew : 0;
    }

    static function getTotalPercentLevelDoanhThu($level)
    {
        $data = UnauthorizedPersonnel::getUn();
        if ($level == TongDoanhThu::LV1) {
            return @$data['percent_hoahong_doanhthu_level_1'] / 100;
        } elseif ($level == TongDoanhThu::LV2) {
            return @$data['percent_hoahong_doanhthu_level_2'] / 100;
        } elseif ($level == TongDoanhThu::LV3) {
            return @$data['percent_hoahong_doanhthu_level_3'] / 100;
        } elseif ($level == TongDoanhThu::LV4) {
            return @$data['percent_hoahong_doanhthu_level_4'] / 100;
        } elseif ($level == TongDoanhThu::LV5) {
            return @$data['percent_hoahong_doanhthu_level_5'] / 100;
        } elseif ($level == TongDoanhThu::LV6) {
            return @$data['percent_hoahong_doanhthu_level_6'] / 100;
        }
        return 0;
    }

    static function getPercentLevelDoanhThuToanQuoc($level)
    {
        $data = UnauthorizedPersonnel::getUn();
        if ($level == TongDoanhThu::LV6) {
            return @$data['percent_hoahong_doanhthu_toanquoc_level_6'] / 100;
        }
        return '';
    }

    static function soDuDoanhThu($tongdoanhthu)
    {
        $data = UnauthorizedPersonnel::getUn();
        if ($tongdoanhthu > @$data['total_doanhthu_level_1']) {
            return $tongdoanhthu - $data['total_doanhthu_level_1'];
        } elseif ($tongdoanhthu > @$data['total_doanhthu_level_2']) {
            return $tongdoanhthu - $data['total_doanhthu_level_2'];
        } elseif ($tongdoanhthu > @$data['total_doanhthu_level_3']) {
            return $tongdoanhthu - $data['total_doanhthu_level_3'];
        } elseif ($tongdoanhthu > @$data['total_doanhthu_level_4']) {
            return $tongdoanhthu - $data['total_doanhthu_level_4'];
        } elseif ($tongdoanhthu > @$data['total_doanhthu_level_5']) {
            return $tongdoanhthu - $data['total_doanhthu_level_5'];
        } elseif ($tongdoanhthu > @$data['total_doanhthu_level_6']) {
            return $tongdoanhthu - $data['total_doanhthu_level_6'];
        }
        return false;
    }

    static function calcLevelTheoTongDoanhThu($sotiengiaodich, $taikhoannguon)
    {
        $account = $taikhoannguon['account'];
        $temp = [];
        $flagHH = true;
        $flagHHLevelMax = true;
        $giaPha = Customer::buildFullTreeNguocBaoGomCaGoc('', $temp, $account); // gia phả dòng họ
        if ($giaPha) {
            //$lsTdt = TongDoanhThu::whereIn('account', array_column($giaPha, 'account'))->get()->keyBy('account')->toArray();
            $levelOld = 0;
            $now = date('Y/m/d');
            $n = new Carbon($now);
            foreach ($giaPha as $g) {
                $dudieukien = false;
                if (isset($g['recruits']['last_time'])) {
                    $a = new Carbon(Helper::showMongoDate($g['recruits']['last_time'], 'Y/m/d'));
                }else {
                    $a = new Carbon(Helper::showMongoDate($g['created_at'], 'Y/m/d'));
                }
                $ago = $a->diffInDays($n);
                if ($ago <= 15) {
                    $dudieukien = true;
                }
                if ($dudieukien) {

                    $tongdoanhthu = TongDoanhThu::CongTruViVer2($g['account'], $sotiengiaodich, TongDoanhThu::table_name);
                    if (!isset($g['level_doanhthu'])) {
                        $levelDT = self::checkLevelDoanhThu($tongdoanhthu['total_money']);
                        $g['level_doanhthu'] = 0;
                    } else {
                        $levelDT = $g['level_doanhthu'];
                    }
                    if ($levelDT) {
                        if (str_replace('LV', '', $g['level_doanhthu']) < str_replace('LV', '', $levelDT)) {
                            Customer::where('account', $g['account'])->update(['level_doanhthu' => $levelDT]);
                            $customerAfterSave = Customer::find($g['_id'])->toArray();
                            Logs::createLogNew([
                                'type' => Logs::TYPE_UPDATED,
                                'object_id' => $g['_id'],
                                'note' => 'Tài khoản "' . $g['account'] . '" đã đạt level: ' . $levelDT
                            ], Customer::table_name, $g, $customerAfterSave);
                            $soduDT = self::soDuDoanhThu($tongdoanhthu['total_money']);
                            $levelDT = $customerAfterSave['level_doanhthu'];
                        } else {
                            $soduDT = $sotiengiaodich;
                        }
                        if (str_replace('LV', '', $levelDT) > str_replace('LV', '', $levelOld)) {
                            $flagHH = true;
                        }
                        $percentLevel = self::getPercentLevelDoanhThu($levelDT, $giaPha);
                        if ($flagHHLevelMax) {
                            // case lấy ra số thành viên đạt cấp bậc tối cao
                            $arrTongDoanhThuLevelMax = [TongDoanhThu::LV6];
                            $lsMemberLevelMax = Customer::getLsCustomerByLevelDoanhThu($arrTongDoanhThuLevelMax, true);
                            if (!empty($lsMemberLevelMax)) {
                                foreach ($arrTongDoanhThuLevelMax as $iLevel) {
                                    // case xử lý level 4
                                    if (isset($lsMemberLevelMax[$iLevel])) {
                                        $totalMemberLevel4Max = count($lsMemberLevelMax[$iLevel]);
                                        if ($totalMemberLevel4Max) {
                                            $percentIsLevelMax = self::getPercentLevelDoanhThuToanQuoc($iLevel);;
                                            $percentIsLevelMax /= $totalMemberLevel4Max;
                                            $moneyHHDTIsLevelMax = $percentIsLevelMax * $sotiengiaodich;
                                            foreach ($lsMemberLevelMax[$iLevel] as $mlv) {
                                                ViHoaHong::CongTruViVer2($mlv['account'], $moneyHHDTIsLevelMax, ViHoaHong::table_name);
                                                $saveTransactionHoaHong = [
                                                    'diem_da_nhan' => $moneyHHDTIsLevelMax,
                                                    'percent_level' => $percentIsLevelMax,
                                                    'tai_khoan_nguon' => Customer::getTaiKhoanToSaveDb($taikhoannguon),
                                                    'tai_khoan_nhan' => Customer::getTaiKhoanToSaveDb($mlv),
                                                    'type_giaodich_hoahong_doanhthu' => true,
                                                    'detail_type_giaodich' => 'Hoa hồng được thưởng cho tổng doanh thu toàn quốc',
                                                    'order_id' => '',
                                                ];
                                                Transaction::createTransaction($saveTransactionHoaHong, Transaction::DIEM_HOAHONG, Transaction::VIHOAHONG);
                                                $flagHHLevelMax = false;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($soduDT && $percentLevel && $flagHH) {
                            //dump($percentLevel, $soduDT, $tongdoanhthu['total_money']);
                            $flagHH = false;
                            $moneyHHDT = $percentLevel * $soduDT;
                            ViHoaHong::CongTruViVer2($g['account'], $moneyHHDT, ViHoaHong::table_name);
                            $saveTransactionHoaHong = [
                                'diem_da_nhan' => $moneyHHDT,
                                'percent_level' => $percentLevel,
                                'tai_khoan_nguon' => Customer::getTaiKhoanToSaveDb($taikhoannguon),
                                'tai_khoan_nhan' => Customer::getTaiKhoanToSaveDb($g),
                                'type_giaodich_hoahong_doanhthu' => true,
                                'detail_type_giaodich' => 'Hoa hồng được thưởng cho tổng doanh thu',
                                'order_id' => '',
                            ];
                            Transaction::createTransaction($saveTransactionHoaHong, Transaction::DIEM_HOAHONG, Transaction::VIHOAHONG);
                        }
                        $levelOld = $levelDT;
                    }
                }else {
                    $tongdoanhthu = TongDoanhThu::getViByAccount($g['account']);
                    if (!isset($g['level_doanhthu'])) {
                        $levelDT = self::checkLevelDoanhThu(@$tongdoanhthu['total_money']);
                    } else {
                        $levelDT = $g['level_doanhthu'];
                    }
                    if (str_replace('LV', '', $levelDT) > str_replace('LV', '', $levelOld)) {
                        $flagHH = false;
                    }
                    $levelOld = $levelDT;
                }
            }
        }
    }


}