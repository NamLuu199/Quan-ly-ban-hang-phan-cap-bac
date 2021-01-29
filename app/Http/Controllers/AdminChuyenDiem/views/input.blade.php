@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
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
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Yêu cầu rút tiền </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('rut-tien')}}">Yêu cầu chuyển điểm</a></li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <!-- Basic setup -->
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">Yêu cầu chuyển điểm các ví</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>

            <form class="stepy-validation" id="postInputForm">
                @csrf
                <fieldset title="1">
                    <legend class="text-semibold">Chọn thông tin</legend>
                    @if((!isset($request->vithanhtoan) || $request->vithanhtoan !== \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG))
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Today's revenue -->
                            <div class="panel bg-blue-400">
                                <div class="panel-body">

                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vitichluy['total_money']) }}</h3>
                                    Ví tích luỹ
                                    <div class="text-muted text-size-small">
                                        ---
                                    </div>
                                </div>
                                <div id="vi-tichluy"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                        <div class="col-md-6">
                            <!-- Today's revenue -->
                            <div class="panel bg-success-400">
                                <div class="panel-body">


                                    <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney($vihoahong['total_money']) }}</h3>
                                    Ví hoa hồng
                                    <div class="text-muted text-size-small">
                                        ---
                                    </div>
                                </div>
                                <div id="vi-hoahong"></div>
                            </div>
                            <!-- /today's revenue -->
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                @if(isset($request->vithanhtoan) && $request->vithanhtoan === \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG)
                                    <input type="hidden" name="obj[thanh_toan_chuyen_diem_cho_account_khac]" value="1">
                                @endif
                                <label>Ví thanh toán</label>
                                <select name="obj[type_vi_thanh_toan]" id="vi-thanh-toan" data-placeholder="Chưa lựa chọn" @if(isset($request->vithanhtoan) && $request->vithanhtoan === \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG) disabled @endif class="select required">
                                    @if(isset($request->vithanhtoan) && $request->vithanhtoan === \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG)
                                        @php($lsVi = \App\Http\Models\BaseModel::getListVi(\App\Http\Models\BaseModel::OBJECT_VITIEUDUNG))
                                        @foreach($lsVi as $vi)
                                            <option value="{{ $vi['id'] }}" @if(@$vi['id']['checked']) selected @endif>{{ $vi['text'] }}</option>
                                        @endforeach
                                    @else
                                        @php($lsVi = \App\Http\Models\BaseModel::getListViChuyenDiem())
                                        @foreach($lsVi as $vi)
                                            <option value="{{ $vi['id'] }}">{{ $vi['text'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ví nhận điểm</label>
                                <select name="obj[type_vi_nhan_diem]" id="vi-nhan-diem" data-placeholder="Select position" class="select required">
                                    <option selected value="OBJECT_VITIEUDUNG">Ví tiêu dùng</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Chủ tài khoản nhận điểm</label>
                                <input type="text" @if((!isset($request->vithanhtoan) || $request->vithanhtoan !== \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG))
                                    value="{{  \App\Http\Models\Member::getCurentAccount() }}"
                                       placeholder="{{ \App\Http\Models\Member::getCurentAccount() }}" disabled
                                       @else placeholder="Nhập tên tài khoản nhận điểm" @endif id="account-nhan" name="obj[tai_khoan_nhan]" class="text-bold form-control" >
                            </div>
                        </div>
                        @if((!isset($request->vithanhtoan) || $request->vithanhtoan !== \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG))
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Họ và tên</label>
                                <input type="text" value="{{ \App\Http\Models\Member::getCurrentFullName() }}" disabled class="text-bold form-control" placeholder="{{ \App\Http\Models\Member::getCurrentFullName() }}">
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Số điểm giao dịch</label>
                                <input type="text" id="so-diem-giao-dich" class="input-type-number-format form-control" placeholder="Nhập thông tin">
                                @if((!isset($request->vithanhtoan) || $request->vithanhtoan !== \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG))
                                <input type="text" style="display: none" min="50000" name="obj[so_diem_giao_dich]" max="{{ @$vitichluy['total_money'] }}" id="so-diem-giao-dich-hidden" class="required input-type-number form-control" placeholder="Nhập thông tin">
                                @else
                                    <input type="text" style="display: none" min="50000" name="obj[so_diem_giao_dich]" max="{{ @$vitieudung['total_money'] }}" id="so-diem-giao-dich-hidden" class="required input-type-number form-control" placeholder="Nhập thông tin">
                                @endif
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset title="2" class="d-none">
                    <legend class="text-semibold">Xác nhận gửi yêu cầu</legend>

                    <div class="row">
                        <div class="col-md-6 m-auto">
                            <div class="row">
                                <div class="col">
                                        <div class="upperCard">
                                            <h1>Thông tin chuyển điểm</h1>
                                            <p>Thời gian đặt chuyển <br /> {{ \App\Elibs\Helper::showMongoDate(\App\Elibs\Helper::getMongoDateTime(), 'd/m/Y H:i:s') }}</p>
                                        </div>
                                        <div class="lowerCard">
                                            <button id="button" type="button" class="mb-2">Thông tin chi tiết đơn chuyển điểm</button>
                                            <ul>
                                                <li class="rHeading"><span>Tài khoản nhận: <a href="javascript:void(0);" id="account-nhan-step2">
                                                            @if((!isset($request->vithanhtoan) || $request->vithanhtoan !== \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG))
                                                            {{ \App\Http\Models\Member::getCurentAccount() }}
                                                            @endif
                                                        </a></span>  <span></span></li>
                                                @if((!isset($request->vithanhtoan) || $request->vithanhtoan !== \App\Http\Models\BaseModel::OBJECT_VITIEUDUNG))
                                                <li class="rHeading"><span>Họ tên chủ tài khoản: <a href="javascript:void(0);">{{ \App\Http\Models\Member::getCurrentFullName() }}</a></span>  <span></span></li>
                                                @endif
                                                <li class="rHeading"><span>Giao dịch từ : <a href="javascript:void(0);" id="vi-thanh-toan-step2"></a> -> <a href="javascript:void(0);" id="vi-nhan-diem-step2"></a></span>  <span></span></li>
                                                <li class="rDescription">Tổng điểm giao dịch: <b class="text-danger" id="so-diem-giao-dich-step2"></b></li>
                                            </ul>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <button type="submit" id="btnInputForm" class="btn btn-info bg-teal-800 stepy-finish">Xác nhận giao dịch <i class="icon-check position-right"></i></button>
            </form>
        </div>
        <!-- /basic setup -->
    </div>

@stop
@push('JS_BOTTOM_REGION')

    <script type="text/javascript">
        MNG_POST.URL_ACTION = '/admin/chuyen-diem/';
        (function () {
            $('#btnInputForm').click(function () {
                return MNG_POST.save('#postInputForm');
            })
            var vithanhtoan = $('#vi-thanh-toan').val()
            var vinhandiem = $('#vi-nhan-diem').val()
            var sodiemgiaodich = $('#so-diem-giao-dich').val()

            if(vithanhtoan == 'OBJECT_VITICHLUY') {
                $('#vi-thanh-toan-step2').text('Ví tích lũy')
            }else if(vithanhtoan == 'OBJECT_VIHOAHONG') {
                $('#vi-thanh-toan-step2').text('Ví hoa hồng')
            }else if(vithanhtoan == 'OBJECT_VITIEUDUNG') {
                $('#vi-thanh-toan-step2').text('Ví tiêu dùng')
            }
            if(vinhandiem == 'OBJECT_VITIEUDUNG') {
                vinhandiem = 'Ví tiêu dùng'
            }else {
                vinhandiem = 'Ví không xác định'
            }
            $('#vi-nhan-diem-step2').text(vinhandiem)
            $('#so-diem-giao-dich-step2').text(sodiemgiaodich)
            $('#vi-thanh-toan').change(function () {
                var vithanhtoan = $(this).val()
                var vinhandiem = $('#vi-nhan-diem').val()
                if(vithanhtoan == 'OBJECT_VITICHLUY') {
                    $('#vi-thanh-toan-step2').text('Ví tích lũy')
                    $('#so-diem-giao-dich-hidden').attr('max', {{@$vitichluy['total_money']}})
                }else if(vithanhtoan == 'OBJECT_VIHOAHONG') {
                    $('#vi-thanh-toan-step2').text('Ví hoa hồng')
                    $('#so-diem-giao-dich-hidden').attr('max', {{@$vihoahong['total_money']}})
                }else if(vithanhtoan == 'OBJECT_VITIEUDUNG') {
                    $('#vi-thanh-toan-step2').text('Ví tiêu dùng')
                    $('#so-diem-giao-dich-hidden').attr('max', {{@$vihoahong['total_money']}})
                }
                $('#vi-nhan-diem-step2').text(vinhandiem)
            })
            $('#so-diem-giao-dich').change(function () {
                sodiemgiaodich = $(this).val()
                $('#so-diem-giao-dich-hidden').val(sodiemgiaodich.toString().replace(/\D/g, ''))
                sodiemgiaodich = number_format(sodiemgiaodich)
                $('#so-diem-giao-dich-step2').text(sodiemgiaodich)
                $('#account-nhan-step2').text($('#account-nhan').val())
            })
        })();
    </script>
@endpush
