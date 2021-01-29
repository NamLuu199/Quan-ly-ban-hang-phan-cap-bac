<?php


namespace App\Http\Models;


use App\Elibs\Helper;

class Transaction extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_transaction';
    protected $table = self::table_name;
    static $unguarded = true;

    const DIEM_TIEUDUNG = 'DIEM_TIEUDUNG';
    const DIEM_HOAHONG = 'DIEM_HOAHONG';
    const DIEM_CHIETKHAU = 'DIEM_CHIETKHAU';
    const DIEM_CONGNO = 'DIEM_CONGNO';
    const KHODIEM_TIEUDUNG = 'KHODIEM_TIEUDUNG';
    const TIEUDUNG_TIEUDUNG_MEMBER_ORTHER = 'TIEUDUNG_TIEUDUNG_MEMBER_ORTHER';
    const CHIETKHAU_TICHLUY = 'CHIETKHAU_TICHLUY';
    const CHIETKHAU_TIEUDUNG = 'CHIETKHAU_TIEUDUNG';
    const VIHOAHONG_TIEUDUNG = 'VIHOAHONG_TIEUDUNG';
    const VITICHLUY_TIEUDUNG  = 'VITICHLUY_TIEUDUNG';
    const TICHLUY_CONGNO = 'TICHLUY_CONGNO';
    const VICHIETKHAU = 'vichietkhau';
    const VITIEUDUNG = 'vitieudung';
    const VITICHLUY = 'vitichluy';
    const VICONGNO = 'vicongno';
    const VIHOAHONG = 'vihoahong';

    static $objectRegister = [
        self::DIEM_TIEUDUNG => [
            'key' => self::DIEM_TIEUDUNG,
            'name' => 'MPG -> Tiêu dùng',
        ],
        self::DIEM_CHIETKHAU => [
            'key' => self::DIEM_CHIETKHAU,
            'name' => 'MPG -> Chiết khấu',
        ],
        self::DIEM_CONGNO => [
            'key' => self::DIEM_CONGNO,
            'name' => 'MPG -> Công nợ',
        ],
        self::DIEM_HOAHONG => [
            'key' => self::DIEM_HOAHONG,
            'name' => 'MPG -> Hoa hồng',
        ],
        self::CHIETKHAU_TICHLUY => [
            'key' => self::CHIETKHAU_TICHLUY,
            'name' => 'Chiết khấu -> Tích lũy',
        ],
        self::CHIETKHAU_TIEUDUNG => [
            'key' => self::CHIETKHAU_TIEUDUNG,
            'name' => 'Chiết khấu -> Tiêu dùng',
        ],
        self::KHODIEM_TIEUDUNG => [
            'key' => self::KHODIEM_TIEUDUNG,
            'name' => 'Kho điểm -> Tiêu dùng',
        ],
        self::TICHLUY_CONGNO => [
            'key' => self::TICHLUY_CONGNO,
            'name' => 'Tích luỹ -> Công nợ',
        ],
        self::VIHOAHONG_TIEUDUNG  => [
            'key' => self::VIHOAHONG_TIEUDUNG,
            'name' => 'Hoa hồng -> Tiêu dùng',
        ],
        self::VITICHLUY_TIEUDUNG   => [
            'key' => self::VITICHLUY_TIEUDUNG ,
            'name' => 'Tích luỹ -> Tiêu dùng',
        ],
        self::TIEUDUNG_TIEUDUNG_MEMBER_ORTHER    => [
            'key' => self::TIEUDUNG_TIEUDUNG_MEMBER_ORTHER  ,
            'name' => 'Chuyển điểm tiêu dùng liên tài khoản',
        ],

    ];

    static function getTransactionNotUpdatedByType($type, $object, $keyBy = false) {
        $now = Helper::getMongoDate('d/m/Y');
        $where = [
            'object' => $object,
            'type_giaodich' => $type,
            '$or' => [
                [
                    'updated_vi_at' => ['$exists' => false],
                ],
                [
                    'updated_vi_at' => ['$lt' => $now],
                ]
            ],
        ];
        if($keyBy) {
            return self::where($where)->get()->keyBy($keyBy)->toArray();
        }
        return self::where($where)->get()->toArray();
    }

    static function createTransaction($o, $typeGiaoDich, $object) {
        $toSaveTranViTieuDung = [
            'diem_da_nhan' => $o['diem_da_nhan'],
            'created_by' => Member::getCreatedByToSaveDb(),
            'created_at' => Helper::getMongoDate(),
            'status' => Transaction::STATUS_ACTIVE,
            'type_giaodich' => $typeGiaoDich,
            'object' => $object,
            'tai_khoan_nguon' => $o['tai_khoan_nguon'],
            'tai_khoan_nhan' => $o['tai_khoan_nhan'],
            'order_id' => @$o['order_id'],
            'detail_type_giaodich' => @$o['detail_type_giaodich'],
        ];
        if(isset($o['type_giaodich_hoahong_doanhthu'])) {
            $toSaveTranViTieuDung['type_giaodich_hoahong_doanhthu'] = true;
        }
        if(isset($o['percent_level'])) {
            $toSaveTranViTieuDung['percent_level'] = $o['percent_level'];
        }
        Transaction::insertGetId($toSaveTranViTieuDung);
    }
}