@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/wizards/stepy.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/validation/validate.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/wizard_stepy.js') !!}


@stop
@section('BREADCRUMB_REGION')

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Kích hoạt thành viên </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('kich-hoat-thanh-vien')}}">Kích hoạt thành viên</a></li>
        </ul>
    </div>

@stop

@section('CONTENT_REGION')
    <form class="form-horizontal" id="postInputForm">
        @csrf
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">Kích hoạt thành viên</h5>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="reload"></a></li>
                                <li><a data-action="close"></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col">
                            @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Mã tài khoản kích hoạt','key'=>'ma_tai_khoan_nhan_kich_hoat'],
                                    'note'=>['label'=>'*','class'=>'text-danger']])
                            <div class="form-group">
                                <label>Ví thanh toán</label>
                                <select name="obj[type_vi_thanh_toan]" id="vi-thanh-toan" data-placeholder="Chưa lựa chọn" class="select required">
                                    @php($lsVi = \App\Http\Models\BaseModel::getListViChuyenDiem())
                                    @foreach($lsVi as $vi)
                                        <option value="{{ $vi['id'] }}">{{ $vi['text'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="media-left">
                                <div class="thumb">
                                    <a href="#">
                                        <i class="icon-user-plus"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="media-body">
                                <h6 class="media-heading">Tài khoản: <a href="javascript:void(0);" id="account-member"></a></h6>
                                <h6 class="media-heading">Họ tên: <a href="javascript:void(0);" id="name-member"></a></h6>
                                <ul class="list-inline list-inline-separate text-muted">
                                    <li><i class="icon-book-play position-left"></i> Ngày tạo</li>
                                    <li id="created-member"></li>
                                </ul>
                            </div>
                            <div class="media-left">
                                <div class="thumb">
                                    <a href="#">
                                        <i class="icon-stack-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="media-body">
                                <h6 class="media-heading">Mã đơn hàng: <a href="javascript:void(0);" id="order-id"></a></h6>
                                <h6 class="media-heading">Tài khoản nhận: <a href="javascript:void(0);" id="order-account-member"></a></h6>
                                <h6 class="media-heading">Họ tên người nhận: <a href="javascript:void(0);" id="order-name-member"></a></h6>
                                <h6 class="media-heading">Số điểm cần mua: <a href="javascript:void(0);" class="text-danger text-bold" id="order-so-diem-can-mua"></a></h6>
                                <ul class="list-inline list-inline-separate text-muted">
                                    <li><i class="icon-book-play position-left"></i> Ngày tạo</li>
                                    <li id="order-created-member"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" id="btnInputForm" class="btn btn-success">Xác nhận giao dịch <i class="icon-arrow-right14 position-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop
@push('JS_BOTTOM_REGION')
    <script type="text/javascript">
        MNG_MEMBER.URL_ACTION = '/admin/kich-hoat-thanh-vien/';

        (function () {
            $('#btnInputForm').click(function () {
                return MNG_MEMBER.save('#postInputForm');
            })
            $('#obj-ma_tai_khoan_nhan_kich_hoat').on('change', function () {
                getMemberInactive($(this))
            })
        })();

        function getMemberInactive(obj) {
            var type = jQuery(obj).val();
            if (!type) {
                return false;
            }
            var link = '{!! admin_link('kich-hoat-thanh-vien/get-member-inactive?code=') !!}'+type;
            _GET_URL(link, {
                callback: function (json) {
                    if(json.status == 1) {
                        if (typeof json.data !== undefined) {
                            let member = json.data.member;
                            let order = json.data.orderByMember;
                            initDom(member, order)
                        }
                    }else {
                        alert(json.msg)
                        initDom([], [])
                    }

                }
            })
        }

        function initDom(member, order) {
            $('#created-member').text(member['created_at']??'')
            $('#account-member').text(member['account']??'')
            $('#name-member').text(member['fullname']??'')
            $('#order-id').text(order['_id']??'')
            $('#order-created-member').text(order['created_at']??'')
            $('#order-account-member').text(order['account']??'')
            $('#order-name-member').text(order['name']??'')
            $('#order-so-diem-can-mua').text(order['so_diem_can_mua']??'')
        }
    </script>
@endpush
