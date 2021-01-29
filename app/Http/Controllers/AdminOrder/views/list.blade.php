@extends($THEME_EXTEND)

@section('BREADCRUMB_REGION')
    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Quản lý danh sách đơn hàng</span></h5>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class=""><a href="{{admin_link('don-hang-cua-toi')}}">Danh sách đơn hàng của tôi</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading d-flex">
            <div class="col-md-3">
                <h3 class="panel-title"><strong>Quản lý {{@$title_module??'đơn mua điểm MPG'}}</strong></h3>
                (Tìm thấy : {{$listObj->total()}} {{@$title_module??'đơn mua điểm MPG'}})
            </div>
            @isset($moneyFilter)
                <div class="col-md-6">
                    <div class="d-flex">
                        @foreach($moneyFilter as $t => $filter)
                            <ul class="ml-4">
                                @foreach($filter as $i)
                                    <li> @if($t == 'danh_sach_so_luong_don_theo_tung_trang_thai')Tổng @elseif($t == 'danh_sach_tien_don_theo_tung_trang_thai') Tổng tiền @endif số [Đơn {{ \App\Http\Models\Orders::getStatus(@$i['_id'])['text'] }}]: <span class="text-danger text-bold">{{ \App\Elibs\Helper::formatMoney(@$i['total']) }}</span></li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            @endisset
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_status" id="" class="form-control">
                                    <option value="0">Tất cả trạng thái</option>
                                    @foreach(App\Http\Models\Orders::getListStatus($q_status, App\Http\Models\Orders::ORDER_BUY_MPG) as $status)
                                        <option @if(isset($status['checked'])) selected="selected"
                                                @endif value="{{ $status['id'] }}">{{ $status['text'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- <div class="form-group no-margin">
                             <div class="content-group">
                                 <input name="actived_at" value="{{app('request')->input('q')}}" type="text"
                                        class="form-control input-sm" placeholder="Tìm kiếm từ khóads">
                                 <input name="q" value="{{app('request')->input('q')}}" type="text"
                                        class="form-control input-sm" placeholder="Tìm kiếm từ khóasd">
                             </div>
                         </div>--}}
                        <div class="input-group content-group">
                            <div class="has-feedback has-feedback-left">
                                <input name="q" value="{{app('request')->input('q')}}" type="text"
                                       class="form-control input-sm" placeholder="Tìm kiếm từ khóa">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>

                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Lọc</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @php
            $nameRoute = \Request::route()->getName();
        @endphp
        @if($nameRoute == 'AdminOrderMpg')
            @include('views.include.list-table-muadiem')
        @elseif($nameRoute == 'AdminPurchaseOrder')
            @include('views.include.list-table-don-hang-cua-toi')
        @endif

        <div class="panel-body">
            @if(!$listObj->count())
                <div class="alert alert-danger alert-styled-left alert-bordered">
                    Không tìm thấy dữ liệu nào ở trang này. (Hãy kiểm tra lại các điều kiện tìm kiếm hoặc
                    phân trang...)
                </div>
            @endif
            <div class="text-center pagination-rounded-all">{{ $listObj->render() }}</div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
            if (jqxhr.status == 200) {
                $(document).unbind('click.fb-start');
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3
                });
            }
        });
    </script>
@stop

@section('JS_BOTTOM_REGION')

@stop
