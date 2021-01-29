<?php


namespace App\Http\Controllers\AdminWithdrawal;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\Customer;
use App\Http\Models\Member;
use App\Http\Models\Transaction;
use App\Http\Models\KichHoatTaiKhoan;
use Illuminate\Http\Request;

class MngOrderChuyenDiemKichHoatThanhVien extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->_list();
        }
    }

    public function _list()
    {
        HtmlHelper::getInstance()->setTitle('Quản lý lịch sử giao dịch chuyển điểm kích hoạt thành viên');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', KichHoatTaiKhoan::STATUS_PROCESS_DONE);

        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;

        $curentMemberId = Member::getCurentId();
        $where = [
            "created_by.id" => $curentMemberId
        ];


        if ($q_status) {
            $where['status'] = $q_status;
        }

        $listObj = KichHoatTaiKhoan::where($where);

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('tai_khoan_nhan.account', 'LIKE', '%' . $q . '%');
        }

        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $tpl['listObj'] = $listObj;



        return eView::getInstance()->setViewBackEnd(__DIR__, 'mng-kichhoatthanhvien.list', $tpl);
    }
}