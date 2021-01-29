<?php
namespace App\Http\Controllers\FrontEnd\FeAjax;

use App\Elibs\eView;
use App\Elibs\Helper;
use App\Http\Controllers\Controller;
use App\Http\Models\Member;
use App\Http\Models\Product;
use Illuminate\Http\Request;

class FeAjax extends Controller {
    public function index($action = '') {
        $action = str_replace('-', '_', $action);
        if(method_exists($this, $action)){
            return $this->$action();
        }
        return $this->nothing();
    }

    function get_data_product() {
        $typeMuaBan = Request::capture()->input('type_muaban', Product::TYPE_BANLE);
        $id = Request::capture()->input('_id', 0);
        $amount = Request::capture()->input('amount', 0);
        $obj = Product::select(Product::$basicFiledsForList)->where([
            ['status', Product::STATUS_ACTIVE],
            ['_id', $id],
        ])->first();
        $currentMember = Member::getCurent();
        if(isset($currentMember['daily_sile_cu']) && $currentMember['daily_sile_cu'] == true) {
            $arrGiaBanSi = @$obj['gia_ban_si_cho_daily_cu'];
        }else {
            $arrGiaBanSi = @$obj['gia_ban_si'];
        }
        if($arrGiaBanSi && $typeMuaBan == Product::TYPE_BANSI) {
            //$amountOld = 0;
            foreach ($arrGiaBanSi as $k => $qt) {
                if (isset($qt['amount']) && $amount < $qt['amount']) {
                    $obj['finalPrice'] = Helper::formatMoney(@$qt['price']).'/'.@$obj['don_vi_tinh_si']['name'];
                    $obj['amount'] = @$obj['amount_ban_si'];
                    break;
                }
                //$amountOld = $qt['amount'];
            }
        }else {
            $obj['finalPrice'] = Helper::formatMoney(@$obj['finalPrice']).'/'.@$obj['don_vi_tinh_le']['name'];
        }
        $tpl = $obj;
        return eView::getInstance()->getJsonSuccess('Lấy dữ liệu thành công', $tpl);
    }

    public function nothing(){
        return "Nothing...";
    }

}
