<?php


namespace App\Http\Controllers\AdminKichHoatTaiKhoan;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Agency;
use App\Http\Models\BaseModel;
use App\Http\Models\Customer;
use App\Http\Models\Logs;
use App\Http\Models\Member;
use App\Http\Models\Orders;
use App\Http\Models\Role;
use App\Http\Models\Transaction;
use App\Http\Models\ViChietKhau;
use App\Http\Models\ViCongNo;
use App\Http\Models\ViHoaHong;
use App\Http\Models\ViTichLuy;
use App\Http\Models\ViTieuDung;
use App\Http\Models\KichHoatTaiKhoan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MngKichHoatTaiKhoan extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->input();
        }
    }

    function input() {
        $tpl = [];
        if (!empty($_POST)) {
            return $this->_save();
        }
        HtmlHelper::getInstance()->setTitle('Yêu cầu chuyển điểm');
        $id = Request::capture()->input('id', 0);
        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }
    function _save() {
        $obj = Request::capture()->input('obj', []);
        if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VITICHLUY') {
            $vi = ViTichLuy::getViByAccount(Member::getCurentAccount());
            $obj['type_giaodich'] = 'VITICHLUY_TIEUDUNG';
            $type_vi = 'vitichluy';
        }else if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VIHOAHONG') {
            $vi = ViHoaHong::getViByAccount(Member::getCurentAccount());
            $obj['type_giaodich'] = 'VIHOAHONG_TIEUDUNG';
            $type_vi = 'vihoahong';
        }else {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
        }
        if(!$vi) {
            return eView::getInstance()->getJsonError('Không tìm thấy ví giao dịch bạn yêu cầu');
        }
        if($vi['total_money'] <= 0) {
            return eView::getInstance()->getJsonError('Ví của bạn không đủ điểm để giao dịch');
        }

        $code = strip_tags(trim($obj['ma_tai_khoan_nhan_kich_hoat']));
        if(!isset($code) || $code == '') {
            return eView::getInstance()->getJsonError('Mã kích hoạt không được bỏ trống');
        }

        $memberInactive = Member::getMemberByMaTaiKhoanKichHoat($code);
        if (!$memberInactive) {
            return eView::getInstance()->getJsonError('Mã kích hoạt không tồn tại');
        }
        $memberInactive = $memberInactive->toArray();
        $memberInactive['created_at'] = Helper::showMongoDate($memberInactive['created_at'], 'd/m/Y H:i:s');
        $OrderInactiveOfMember = Orders::getByMaTaiKhoanKichHoat($code, $memberInactive['account']);
        if (!$OrderInactiveOfMember) {
            return eView::getInstance()->getJsonError('Không tìm thấy đơn hàng nào');
        }
        if($OrderInactiveOfMember['status'] == Orders::STATUS_PROCESS_DONE) {
            $updated_at = Helper::showMongoDate($OrderInactiveOfMember['updated_at'], 'd/m/Y H:i:s');
            return eView::getInstance()->getJsonError('Đơn hàng này đã duyệt thành công vào lúc '.$updated_at);
        }
        $phigiaodich = 3000;
        if($OrderInactiveOfMember['so_diem_can_mua'] <= 5000000){
            $phigiaodich = 2000;
        }elseif($OrderInactiveOfMember['so_diem_can_mua'] <= 10000000){
            $phigiaodich = 3000;
        }elseif($OrderInactiveOfMember['so_diem_can_mua'] > 10000000){
            $phigiaodich = 5000;
        }
        $sotiengiaodich = (double)$OrderInactiveOfMember['so_diem_can_mua'] + $phigiaodich;
        $soducuoi = $vi['total_money'] - $sotiengiaodich;
        if($soducuoi < BaseModel::getLotVi()) {
            return eView::getInstance()->getJsonError('Số dư tối thiểu để duy trì hoạt động trên ví là '.Helper::formatMoney(BaseModel::getLotVi()));
        }
        $customer = Customer::getById($OrderInactiveOfMember['tai_khoan_nhan']['id']);
        if(!$customer) {
            return eView::getInstance()->getJsonError('Không tìm thấy khách hàng này.');
        }else {
            if($customer['status'] != Customer::STATUS_ACTIVE && !isset($customer['actived_at'])) {
                $customer->update(['status' => Customer::STATUS_ACTIVE]);
            }elseif($customer['status'] == Customer::STATUS_DISABLE) {
                return eView::getInstance()->getJsonError('Khách hàng này đã bị khoá');
            }
        }

        if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VITICHLUY') {
            $noteLog = 'Ví tích luỹ của acc: ' . @$vi['account'] . ' đã trừ  ' . (double)$sotiengiaodich;
            $tablNameLog = ViTichLuy::table_name;
        }else if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VIHOAHONG') {
            $noteLog = 'Ví hoa hồng của acc: ' . @$vi['account'] . ' đã trừ  ' . (double)$sotiengiaodich;
            $tablNameLog = ViHoaHong::table_name;
        }
        $objToSave = [
            'status' => KichHoatTaiKhoan::STATUS_PROCESS_DONE,
            'created_at' => Helper::getMongoDate(),
            'actived_at' => Helper::getMongoDate(),
            'created_by' => [
                'id'      => Member::getCurentId(),
                'name'    => Member::getCurrentName(),
                'account' => Member::getCurentAccount(),
                'email' => Member::getCurrentEmail(),
            ],
            'tai_khoan_nhan' => $OrderInactiveOfMember['tai_khoan_nhan'],
            'order_id' => $OrderInactiveOfMember['_id']
        ];
        $objToSave['so_tien_muon_giao_dich'] = (double)$OrderInactiveOfMember['so_diem_can_mua'];
        $objToSave['phi_giao_dich'] = (double)$phigiaodich;
        $objToSave['so_du_cuoi'] = (double)$soducuoi;
        $objToSave['type_vi'] = $type_vi;
        KichHoatTaiKhoan::insert($objToSave);
        $oldVi = $vi->toArray();
        $vi->update(['total_money' => $soducuoi]);
        Logs::createLogNew([
            'type' => Logs::TYPE_UPDATED,
            'object_id' => (string)$vi['_id'],
            'note' => $noteLog,
        ], $tablNameLog, $oldVi, $vi->toArray());
        $this->_update_status($OrderInactiveOfMember['_id']);
    }

    public function _update_status($idC) {


        $order = Orders::find($idC);
        if(!$order) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ!');
        }

        $status = Orders::STATUS_PROCESS_DONE;
        $listStatus = Orders::getListStatus(false, Orders::ORDER_BUY_MPG);
        if (!isset($listStatus[$status]) && $status != Orders::STATUS_DELETED_PHY) {
            return eView::getInstance()->getJsonError('Trạng thái của đơn hàng không hợp lệ');
        }elseif (isset($listStatus[$status]) && $status == Orders::STATUS_DELETED) {
            return eView::getInstance()->getJsonError('Trạng thái của đơn hàng không hợp lệ');
        }

        $customer = Customer::getById($order['tai_khoan_nhan']['id']);
        if(!$customer) {
            return eView::getInstance()->getJsonError('Không tìm thấy khách hàng này.');
        }else {
            if($customer['status'] != Customer::STATUS_ACTIVE) {
                return eView::getInstance()->getJsonError('Khách hàng này không hoạt động');
            }
        }
        $now = date('d/m/Y');
        $objToSave = [
            'status' => $status,
            'updated_by_customer' => true,
            'start_updated_vi_at' => Helper::getMongoDate($now),
            'updated_at' => Helper::getMongoDate(),
            'actived_at' => Helper::getMongoDate(),
            'updated_by' => Member::getCreatedByToSaveDb(),
        ];
        $a = Carbon::now()->addMonths(Orders::getMonthsEndRunAuto());
        $a = $a->toDateString();
        $objToSave['end_updated_vi_at'] = Helper::getMongoDate($a);
        Orders::where('_id', $idC)->update($objToSave);
        Logs::createLogNew([
            'type' => Logs::TYPE_UPDATED,
            'object_id' => (string)$order['_id'],
            'note' => "Đơn mua điểm " . $order['_id'] . ' được sửa thông tin bởi ' . Member::getCurentAccount()
        ], Customer::table_name, $order->toArray(), Orders::find($order['_id'])->toArray());

        if($status == Orders::STATUS_PROCESS_DONE) {
            //case cập nhật mã giới thiệu cho khách hàng
            $customer = Customer::getById($customer['_id']);
            if(!isset($customer['ma_gioi_thieu'])) {
                Customer::find($customer['_id'])->update(['ma_gioi_thieu' => uniqid('MPG_')]);
                Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$customer['_id'],
                    'note' => 'Khách hàng acc: ' . @$customer['account'] . ' đã được cập nhật mã giới thiệu từ đơn hàng ' . (string)$idC
                ], Customer::table_name, $customer->toArray(), Customer::find($customer['_id'])->toArray());
            }
            if(isset($customer['chuc_danh']) && $customer['chuc_danh'] != Customer::IS_DAILY && $order['so_diem_can_mua'] >= Orders::getMinDaiLy(@$order['percents']) && $order['so_diem_can_mua'] < Orders::getMinMPMart(@$order['percents'])) {

                Customer::where('_id', $customer['_id'])->update(['chuc_danh' => Customer::IS_DAILY]);
                $newCus = Customer::find($customer['_id'])->toArray();
                Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$customer['_id'],
                    'note' => 'Khách hàng acc: ' . @$customer['account'] . ' đã được cập nhật thành đại lý từ đơn hàng ' . (string)$id
                ], Customer::table_name, $customer->toArray(), $newCus);
            }elseif(isset($customer['chuc_danh']) && $customer['chuc_danh'] != Customer::IS_MPMART && $order['so_diem_can_mua'] >= Orders::getMinMPMart(@$order['percents'])) {
                Customer::where('_id', $customer['_id'])->update(['chuc_danh' => Customer::IS_MPMART]);
                $newCus = Customer::find($customer['_id'])->toArray();
                Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$customer['_id'],
                    'note' => 'Khách hàng acc: ' . @$customer['account'] . ' đã được cập nhật thành siêu thị MP Mart từ đơn hàng ' . (string)$id
                ], Customer::table_name, $customer->toArray(), $newCus);
            }
            if(isset($newCus)) {
                $customer = $newCus;
            }
            if (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_DAILY) {
                if (isset($customer['level']) && $customer['level'] == Customer::IS_DAILY.'_'.Customer::LEVEL.'2') {
                    if($order['so_diem_can_mua'] < Orders::getIsDaiLy(@$order['percents'], true)) {
                        $level = 'daily_step2';
                    }else {
                        $level = 'daily_step3';
                    }
                }elseif (isset($customer['level']) && $customer['level'] == Customer::IS_DAILY.'_'.Customer::LEVEL.'3') {
                    $level = 'daily_step3';
                }else {
                    if ($order['so_diem_can_mua'] < Orders::getIsDaiLy(@$order['percents'])){
                        $level = 'daily_step1';
                    } elseif($order['so_diem_can_mua'] < Orders::getIsDaiLy(@$order['percents'], true)) {
                        $level = 'daily_step2';
                    }else {
                        $level = 'daily_step3';
                    }
                }

            }
            elseif (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_MPMART) {
                if (isset($customer['level']) && $customer['level'] == Customer::IS_MPMART.'_'.Customer::LEVEL.'2') {
                    if ($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'])){
                        $level = 'mpmart_step1';
                    } elseif($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'], true)) {
                        $level = 'mpmart_step2';
                    }else {
                        $level = 'mpmart_step3';
                    }
                }elseif (isset($customer['level']) && $customer['level'] == Customer::IS_MPMART.'_'.Customer::LEVEL.'3') {
                    $level = 'mpmart_step3';
                }else {
                    if ($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'])){
                        $level = 'mpmart_step1';
                    } elseif($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'], true)) {
                        $level = 'mpmart_step2';
                    }else {
                        $level = 'mpmart_step3';
                    }
                }

            }
            else {
                $level = 'ctv_step1';
            }
            Customer::where('_id', $customer['_id'])->update(['level' => $level]);
            $newCus = Customer::find($customer['_id'])->toArray();
            if(isset($newCus)) {
                $customer = $newCus;
            }
            // lấy ra đơn hàng vừa cập nhật
            $order = Orders::where('_id', $idC)->first()->toArray();
            if(!isset($order['so_diem_duoc_nhan'])) {
                $order['so_diem_duoc_nhan'] = $order['so_diem_can_mua'];
            }
            if($order['so_diem_duoc_nhan'] >= 50000000) {
                $objToSaveAgency = [
                    'name' => @$order['tai_khoan_nhan']['name'],
                    'member' => @$order['tai_khoan_nhan'],
                    'dai_ly_tra_hang' => Agency::AGENCY_TRA_HANG_CAP_HUYEN,
                    'street' => @$customer['addr'],
                    'city' => @$customer['city'],
                    'district' => @$customer['district'],
                    'town' => @$customer['town'],
                    'created_at' => Helper::getMongoDateTime(),
                    'created_by' => Member::getCreatedByToSaveDb()
                ];
                Agency::insertGetId($objToSaveAgency);
            }
            // case nhảy 100% vào ví tiêu dùng
            // thêm case nhảy hoàn tiền của ctv vào tiêu dùng
            $stepCtv = false;
            $stepDaiLy= false;
            $stepMpMart= false;
            $order['so_diem_can_mua_moi'] = $order['so_diem_can_mua'];
            if (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_CTV) {
                if($order['so_diem_can_mua'] < Orders::getIsCTV(@$order['percents'])) {
                    $perCtv = Orders::getPercentChietKhauCtv(@$order['percents']);        // < 2tr
                }elseif ($order['so_diem_can_mua'] < Orders::getIsCTV(@$order['percents'], true)){
                    $perCtv = Orders::getPercentChietKhauCtv(@$order['percents'],true); // 5tr
                }else {
                    $perCtv = Orders::getPercentChietKhauCtv(@$order['percents'], false, true); // 30tr
                }
                $order['so_diem_can_mua_moi'] += $order['so_diem_can_mua']*$perCtv;
                $stepCtv = true;
                Orders::where('_id', $idC)->update(['no_run_hang_ngay' => true]);
            }
            elseif (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_DAILY) {
                /*
                 * Mốc 1: < 50tr: 65%
                 * Mốc 2: < 120tr: 70%
                 * Mốc 3: < 500tr: 75%
                 * */
                if ($customer['level'] == Customer::IS_DAILY.'_'.Customer::LEVEL.'2') {
                    if ($order['so_diem_can_mua'] < Orders::getIsDaiLy(@$order['percents'], true)){
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungDaiLy(@$order['percents'], true);
                    } else {
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungDaiLy(@$order['percents'],false, true);
                    }
                }elseif ($customer['level'] == Customer::IS_DAILY.'_'.Customer::LEVEL.'3') {
                    $perDaiLy = Orders::getPercentChietKhauCongTieuDungDaiLy(@$order['percents'],false, true);
                }else {
                    if($order['so_diem_can_mua'] < Orders::getIsDaiLy(@$order['percents'])) {
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungDaiLy(@$order['percents']);
                    }elseif ($order['so_diem_can_mua'] < Orders::getIsDaiLy(@$order['percents'], true)){
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungDaiLy(@$order['percents'], true);
                    } else {
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungDaiLy(@$order['percents'],false, true);
                    }
                }
                $order['so_diem_can_mua_moi'] += $order['so_diem_can_mua']*$perDaiLy;
                $stepDaiLy = true;
            }
            elseif (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_MPMART) {
                /*
                 * Mốc 1: < 50tr: 65%
                 * Mốc 2: < 120tr: 70%
                 * Mốc 3: < 500tr: 75%
                 * */
                if($customer['level'] == Customer::IS_MPMART.'_'.Customer::LEVEL.'2') {
                    if ($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'],true)){
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungMpMart(@$order['percents'],true);
                    } else {
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungMpMart(@$order['percents'],false, true);
                    }
                }elseif($customer['level'] == Customer::IS_MPMART.'_'.Customer::LEVEL.'3') {
                    $perDaiLy = Orders::getPercentChietKhauCongTieuDungMpMart(@$order['percents'],false, true);
                }else {
                    if($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'])) {
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungMpMart(@$order['percents']);
                    }elseif ($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'],true)){
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungMpMart(@$order['percents'],true);
                    } else {
                        $perDaiLy = Orders::getPercentChietKhauCongTieuDungMpMart(@$order['percents'],false, true);
                    }
                }
                $order['so_diem_can_mua_moi'] += $order['so_diem_can_mua']*$perDaiLy;
                $stepMpMart = true;
            }
            // endthêm case nhảy hoàn tiền của ctv vào tiêu dùng
            $objTransactionTieuDungToSave = [
                'created_by' => [],
                'created_at' => Helper::getMongoDate(),
                'status' => Transaction::STATUS_ACTIVE,
                'type_giaodich' => Transaction::DIEM_TIEUDUNG,
                'object' => Transaction::VITIEUDUNG,
                'diem_da_nhan' => $order['so_diem_can_mua_moi'],
                'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                'order_id' => $idC,
            ];
            if($stepCtv) {
                $objTransactionTieuDungToSave['chinh_sach_ctv_moi'] = true;
            }
            if($stepDaiLy) {
                $objTransactionTieuDungToSave['chinh_sach_daily_moi'] = true;
            }
            if($stepMpMart) {
                $objTransactionTieuDungToSave['chinh_sach_mpmart_moi'] = true;
            }
            Transaction::insertGetId($objTransactionTieuDungToSave);

            $viTieuDungOfCus = ViTieuDung::where('account', $customer['account'])->first();
            $money = (int)$order['so_diem_can_mua_moi'];
            if($viTieuDungOfCus) {
                // cập nhật
                $money += (int)$viTieuDungOfCus['total_money'];
                $objViTieuDungToSave = [
                    'total_money' => $money,
                    'updated_at' => Helper::getMongoDate(),
                ];
                ViTieuDung::find($viTieuDungOfCus['_id'])->update($objViTieuDungToSave);
                Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$viTieuDungOfCus['_id'],
                    'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDungOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                ], ViTieuDung::table_name, $viTieuDungOfCus->toArray(), ViTieuDung::find($viTieuDungOfCus['_id'])->toArray());
            }else {
                // thêm mới
                $objViTieuDungToSave = [
                    'account' => $customer['account'],
                    'total_money' => $money,
                    'status' => ViTieuDung::STATUS_ACTIVE,
                    'created_at' => Helper::getMongoDate(),
                ];
                $idVi = ViTieuDung::insertGetId($objViTieuDungToSave);
                $viTieuDung = ViTieuDung::find($idVi)->toArray();
                Logs::createLogNew([
                    'type' => Logs::TYPE_CREATE,
                    'object_id' => (string)$idVi,
                    'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDung['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                ], ViTieuDung::table_name, [], $viTieuDung);
            }

            // case nếu là đại lý thì chiết khấu nhảy 80%
            if(isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_DAILY) {
                if ($customer['level'] == Customer::IS_DAILY.'_'.Customer::LEVEL.'2') {
                    if($order['so_diem_can_mua'] < Orders::getIsDaiLy($order['percents'],true)) {
                        $perDaiLy = Orders::getPercentChietKhauDaiLy(@$order['percents'], true);
                    }else {
                        $perDaiLy = Orders::getPercentChietKhauDaiLy(@$order['percents'], false, true);
                    }
                }elseif ($customer['level'] == Customer::IS_DAILY.'_'.Customer::LEVEL.'3') {
                    $perDaiLy = Orders::getPercentChietKhauDaiLy(@$order['percents'], false, true);
                }else {
                    if($order['so_diem_can_mua'] < Orders::getIsDaiLy(@$order['percents'])) {
                        $perDaiLy = Orders::getPercentChietKhauDaiLy(@$order['percents']);
                    }elseif($order['so_diem_can_mua'] < Orders::getIsDaiLy($order['percents'],true)) {
                        $perDaiLy = Orders::getPercentChietKhauDaiLy(@$order['percents'], true);
                    }else {
                        $perDaiLy = Orders::getPercentChietKhauDaiLy(@$order['percents'], false, true);
                    }
                }
                $objTransactionChietKhauToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                    'object' => Transaction::VICHIETKHAU,
                    'diem_da_nhan' => $order['so_diem_can_mua']*$perDaiLy,
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'order_id' => $idC,
                ];
                if($stepDaiLy) {
                    $objTransactionChietKhauToSave['chinh_sach_daily_moi'] = true;
                }
                Transaction::insertGetId($objTransactionChietKhauToSave);

                $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
                $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
                if($viChietKhauOfCus) {
                    // cập nhật
                    $moneyCK += (int)$viChietKhauOfCus['total_money'];
                    $objViChietKhauToSave = [
                        'total_money' => $moneyCK,
                        'updated_at' => Helper::getMongoDate(),
                    ];
                    ViChietKhau::where('account', $customer['account'])->update($objViChietKhauToSave);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viChietKhauOfCus['_id'],
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhauOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                    ], ViChietKhau::table_name, $viChietKhauOfCus->toArray(), ViChietKhau::find($viChietKhauOfCus['_id'])->toArray());
                }else {
                    // thêm mới
                    $objViChietKhauToSave = [
                        'account' => $customer['account'],
                        'total_money' => $moneyCK,
                        'status' => ViChietKhau::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    $idViCK = ViChietKhau::insertGetId($objViChietKhauToSave);
                    $viChietKhau = ViChietKhau::find($idViCK)->toArray();
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$idViCK,
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhau['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                    ], ViChietKhau::table_name, [], $viChietKhau);
                }

                // case đơn > 20tr có nợ += 25% vào ví công nợ
                if(isset($order['debt']) && $order['debt'] == Orders::DEBT_YES) {
                    if (!isset($order['cong_no'])) {
                        $order['cong_no'] = $order['so_diem_duoc_nhan']*Orders::getPercentCongNoDebtYes(@$order['percents']);
                    }
                    $objTransactionCongNoToSave = [
                        'created_by' => [],
                        'created_at' => Helper::getMongoDate(),
                        'status' => Transaction::STATUS_ACTIVE,
                        'type_giaodich' => Transaction::DIEM_CONGNO,
                        'object' => Transaction::VICONGNO,
                        'diem_da_nhan' => $order['cong_no'],
                        'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                        'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                        'order_id' => $idC
                    ];
                    Transaction::insertGetId($objTransactionCongNoToSave);

                    $viCongNoOfCus = ViCongNo::where('account', $customer['account'])->first();
                    if($viCongNoOfCus) {
                        // cập nhật
                        $moneyCN = (int)$viCongNoOfCus['total_money'] + (int)$objTransactionCongNoToSave['diem_da_nhan'];
                        ViCongNo::where('account', $customer['account'])->update(['total_money' => $moneyCN, 'updated_at' => Helper::getMongoDate()]);
                        Logs::createLogNew([
                            'type' => Logs::TYPE_UPDATED,
                            'object_id' => (string)$viCongNoOfCus['_id'],
                            'note' => 'Ví công nợ của acc: ' . @$customer['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                        ], ViCongNo::table_name, $viCongNoOfCus->toArray(), ViCongNo::where('account', $customer['account'])->first()->toArray());

                    }else {
                        // thêm mới
                        $objViCongNoToSave = [
                            'account' => $customer['account'],
                            'total_money' => $objTransactionCongNoToSave['diem_da_nhan'],
                            'status' => ViCongNo::STATUS_ACTIVE,
                            'created_at' => Helper::getMongoDate(),
                        ];
                        $idViCongNo = ViCongNo::insertGetId($objViCongNoToSave);
                        Logs::createLogNew([
                            'type' => Logs::TYPE_CREATE,
                            'object_id' => (string)$idViCongNo,
                            'note' => 'Ví công nợ của acc: ' . @$customer['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                        ], ViCongNo::table_name, [], ViCongNo::find($idViCongNo)->toArray());
                    }
                }
            }
            elseif (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_CTV && 1 == 2) {
                $objTransactionChietKhauToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                    'object' => Transaction::VICHIETKHAU,
                    'diem_da_nhan' => $order['so_diem_can_mua']*Orders::getPercentChietKhauCtv(@$order['percents']),
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'order_id' => $idC,
                ];
                Transaction::insertGetId($objTransactionChietKhauToSave);

                $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
                $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
                if($viChietKhauOfCus) {
                    // cập nhật
                    $moneyCK += (int)$viChietKhauOfCus['total_money'];
                    $objViChietKhauToSave = [
                        'total_money' => $moneyCK,
                        'updated_at' => Helper::getMongoDate(),
                    ];
                    ViChietKhau::where('account', $customer['account'])->update($objViChietKhauToSave);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viChietKhauOfCus['_id'],
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhauOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                    ], ViChietKhau::table_name, $viChietKhauOfCus->toArray(), ViChietKhau::find($viChietKhauOfCus['_id'])->toArray());
                }else {
                    // thêm mới
                    $objViChietKhauToSave = [
                        'account' => $customer['account'],
                        'total_money' => $moneyCK,
                        'status' => ViChietKhau::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    $idViCK = ViChietKhau::insertGetId($objViChietKhauToSave);
                    $viChietKhau = ViChietKhau::find($idViCK)->toArray();
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$idViCK,
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhau['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                    ], ViChietKhau::table_name, [], $viChietKhau);
                }
            }
            elseif (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_MPMART) {
                if($customer['level'] == Customer::IS_MPMART.'_'.Customer::LEVEL.'2') {
                    if ($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'], true)){
                        $perMpMart = Orders::getPercentChietKhauMpMart(@$order['percents'], true);
                    }else {
                        $perMpMart = Orders::getPercentChietKhauMpMart(@$order['percents'], false, true);
                    }
                }elseif($customer['level'] == Customer::IS_MPMART.'_'.Customer::LEVEL.'3') {
                    $perMpMart = Orders::getPercentChietKhauMpMart(@$order['percents'], false, true);
                }else {
                    if($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'])) {
                        $perMpMart = Orders::getPercentChietKhauMpMart(@$order['percents']);
                    }elseif ($order['so_diem_can_mua'] < Orders::getIsMPMart(@$order['percents'], true)){
                        $perMpMart = Orders::getPercentChietKhauMpMart(@$order['percents'], true);
                    }else {
                        $perMpMart = Orders::getPercentChietKhauMpMart(@$order['percents'], false, true);
                    }
                }
                $objTransactionChietKhauToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                    'object' => Transaction::VICHIETKHAU,
                    'diem_da_nhan' => $order['so_diem_can_mua']*$perMpMart,
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'order_id' => $idC,
                ];
                if($stepMpMart) {
                    $objTransactionChietKhauToSave['chinh_sach_mpmart_moi'] = true;
                }
                Transaction::insertGetId($objTransactionChietKhauToSave);

                $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
                $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
                if($viChietKhauOfCus) {
                    // cập nhật
                    $moneyCK += (int)$viChietKhauOfCus['total_money'];
                    $objViChietKhauToSave = [
                        'total_money' => $moneyCK,
                        'updated_at' => Helper::getMongoDate(),
                    ];
                    ViChietKhau::where('account', $customer['account'])->update($objViChietKhauToSave);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viChietKhauOfCus['_id'],
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhauOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                    ], ViChietKhau::table_name, $viChietKhauOfCus->toArray(), ViChietKhau::find($viChietKhauOfCus['_id'])->toArray());
                }else {
                    // thêm mới
                    $objViChietKhauToSave = [
                        'account' => $customer['account'],
                        'total_money' => $moneyCK,
                        'status' => ViChietKhau::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    $idViCK = ViChietKhau::insertGetId($objViChietKhauToSave);
                    $viChietKhau = ViChietKhau::find($idViCK)->toArray();
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$idViCK,
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhau['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$idC
                    ], ViChietKhau::table_name, [], $viChietKhau);
                }
            }

            /*// case nhảy 80% vào ví chiết khấu
            $objTransactionChietKhauToSave = [
                'created_by' => [],
                'created_at' => Helper::getMongoDate(),
                'status' => Transaction::STATUS_ACTIVE,
                'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                'object' => Transaction::VICHIETKHAU,
                'diem_da_nhan' => $order['so_diem_can_mua']*Orders::getPercentChietKhau(),
                'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                'order_id' => $idC,
            ];
            Transaction::insertGetId($objTransactionChietKhauToSave);

            $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
            $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
            if($viChietKhauOfCus) {
                // cập nhật
                $moneyCK += (int)$viChietKhauOfCus['total_money'];
                $objViChietKhauToSave = [
                    'total_money' => $moneyCK,
                    'updated_at' => Helper::getMongoDate(),
                ];
                $viChietKhauOfCus->update($objViChietKhauToSave);
            }else {
                // thêm mới
                $objViChietKhauToSave = [
                    'account' => $customer['account'],
                    'total_money' => $moneyCK,
                    'status' => ViChietKhau::STATUS_ACTIVE,
                    'created_at' => Helper::getMongoDate(),
                ];
                ViChietKhau::insert($objViChietKhauToSave);
            }

            Logs::createLog([
                'type' => Logs::TYPE_UPDATED,
                'data_object' => $objViChietKhauToSave,
                'note' => "Khách hàng " . $customer['account'] . ' đã được cập nhật '.$moneyCK.' MPG vào ví chiết khấu ' . Member::getCurentAccount()
            ], Logs::OBJECT_VICHIETKHAU);*/


            // case 5-4-3-2- cho hoa hồng
            //$parentCus = Customer::where('ma_gioi_thieu', $customer['parent_id'])->first();



            #region case đơn < 20tr ko nợ + 6% vào ví công nợ @todo @kayn tạm comment nếu sau nó mở

            /*if($order['so_diem_can_mua'] < Orders::getMinDaiLy()) {
                $objTransactionCongNoToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CONGNO,
                    'object' => Transaction::VICONGNO,
                    'diem_da_nhan' => $order['so_diem_duoc_nhan']*Orders::getPercentCongNoPhiVanChuyen(),
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'order_id' => $idC,
                ];
                Transaction::insertGetId($objTransactionCongNoToSave);

                $viCongNoOfCus = ViCongNo::where('account', $customer['account'])->first();
                if($viCongNoOfCus) {
                    // cập nhật
                    $moneyCN = (int)$viCongNoOfCus['total_money'] + (int)$objTransactionCongNoToSave['diem_da_nhan'];
                    $viCongNoOfCus->update(['total_money' => $moneyCN, 'updated_at' => Helper::getMongoDate()]);
                    Logs::createLog([
                        'type' => Logs::TYPE_UPDATED,
                        'data_object' => $objTransactionCongNoToSave,
                        'note' => "Ví công nợ của " . $customer['account'] . ' được thêm ' . $objTransactionCongNoToSave['diem_da_nhan'] .' MPG'
                    ], Logs::OBJECT_CONGNO);
                }else {
                    // thêm mới
                    $objViCongNoToSave = [
                        'account' => $customer['account'],
                        'total_money' => $objTransactionCongNoToSave['diem_da_nhan'],
                        'status' => ViCongNo::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    ViCongNo::insert($objViCongNoToSave);
                    Logs::createLog([
                        'type' => Logs::TYPE_CREATE,
                        'data_object' => $objTransactionCongNoToSave,
                        'note' => "Ví công nợ của " . $customer['account'] . ' được thêm ' . $objTransactionCongNoToSave['diem_da_nhan'] .' MPG'
                    ], Logs::OBJECT_CONGNO);
                }

            }*/
            #endregion


            // case trả tiền hoa hồng 5-4-3-2-1

            // danh sách dòng họ gần nhất
            if($customer['chuc_danh'] == Customer::IS_MPMART) {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemMpMartHoaHongForF(@$order['percents']));
            }else {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemHoaHongForF(@$order['percents']));
            }
            Orders::calcLevelTheoTongDoanhThu($order['so_diem_can_mua'], $customer);
            /*if(!isset($order['mpmart'])) {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemHoaHongForF());
            }else {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemMpMartHoaHongForF());
            }*/
        }

        return eView::getInstance()->getJsonSuccess('Cập nhật trạng thái thành công!');
    }

    function _update_hoahong($obj, $customer, $vitieudung = false, $vihoahong = false, $listPercentHoaHong) {
        $temp = [];
        $giaPha = Customer::buildTreeNguoc('', $temp, $customer['account'],Customer::floor); // gia phả dòng họ
        if($giaPha) {
            foreach ($giaPha as $k => $g) {
                $saveHoaHong = [
                    'account' => $g['account'],
                    'total_money' => $obj['so_diem_duoc_nhan']*$listPercentHoaHong[$k],
                    'created_at' => Helper::getMongoDate(),
                    'status' => BaseModel::STATUS_ACTIVE,
                ];
                $saveTransactionHoaHong = [
                    'account' => $g['account'],
                    'diem_da_nhan' => $obj['so_diem_duoc_nhan']*$listPercentHoaHong[$k],
                    'tai_khoan_nguon' => Customer::getTaiKhoanToSaveDb($customer),
                    'tai_khoan_nhan' => Customer::getTaiKhoanToSaveDb($g),
                    'type_giaodich' => Transaction::DIEM_HOAHONG,
                    'created_at' => Helper::getMongoDate(),
                    'status' => BaseModel::STATUS_ACTIVE,
                    'order_id' => $obj['_id'],
                ];

                // kiểm tra xem ví của tk này đã được tạo hay chưa.
                $viHoaHongOfCus = ViHoaHong::where('account', $g['account'])->first();
                if($viHoaHongOfCus) {
                    $moneyHH = (int)$viHoaHongOfCus['total_money'] + (int)$saveTransactionHoaHong['diem_da_nhan'];
                    ViHoaHong::where('account', $g['account'])->update(['total_money' => $moneyHH, 'updated_at' => Helper::getMongoDate()]);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viHoaHongOfCus['_id'],
                        'note' => 'Ví hoa hồng của acc: ' . @$viHoaHongOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViHoaHong::table_name, $viHoaHongOfCus->toArray(), ViHoaHong::find($viHoaHongOfCus['_id'])->toArray());
                }else {
                    $id = ViHoaHong::insertGetId($saveHoaHong);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_CREATE,
                        'object_id' => (string)$id,
                        'note' => 'Ví hoa hồng của acc: ' . @$viHoaHongOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViHoaHong::table_name, [], ViHoaHong::find($id)->toArray());
                }
                Transaction::insert($saveTransactionHoaHong);

            }
        }
    }

    function get_member_inactive() {
        $code = Request::capture()->input('code', []);
        $code = strip_tags(trim($code));
        if(!isset($code) || $code == '') {
            return eView::getInstance()->getJsonError('Mã kích hoạt không được bỏ trống');
        }

        $memberInactive = Member::getMemberByMaTaiKhoanKichHoat($code);
        if (!$memberInactive) {
            return eView::getInstance()->getJsonError('Mã kích hoạt không tồn tại');
        }
        $memberInactive = $memberInactive->toArray();
        $memberInactive['created_at'] = Helper::showMongoDate($memberInactive['created_at'], 'd/m/Y H:i:s');
        $OrderInactiveOfMember = Orders::getByMaTaiKhoanKichHoat($code, $memberInactive['account']);
        if (!$OrderInactiveOfMember) {
            return eView::getInstance()->getJsonError('Không tìm thấy đơn hàng nào');
        }
        $OrderInactiveOfMember = $OrderInactiveOfMember->toArray();
        $OrderInactiveOfMember['created_at'] = Helper::showMongoDate($OrderInactiveOfMember['created_at'], 'd/m/Y H:i:s');
        $OrderInactiveOfMember['account'] = Helper::showContent($OrderInactiveOfMember['tai_khoan_nhan']['account']);
        $OrderInactiveOfMember['name'] = Helper::showContent($OrderInactiveOfMember['tai_khoan_nhan']['name']);
        $OrderInactiveOfMember['so_diem_can_mua'] = Helper::formatMoney($OrderInactiveOfMember['so_diem_can_mua']);
        unset($OrderInactiveOfMember['tai_khoan_nhan'], $OrderInactiveOfMember['percents']);
        return eView::getInstance()->getJsonSuccess('Lấy dữ liệu thành công', ['member' => $memberInactive, 'orderByMember' => $OrderInactiveOfMember]);
    }

}