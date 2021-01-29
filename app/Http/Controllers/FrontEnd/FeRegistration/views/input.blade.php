@extends('frontend')

@section('CSS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css') !!}
@endsection

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/magiczoom.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js') !!}
@endsection

@section('CONTENT_REGION')
    <main class="page-account pb-5">
        <breadcrumb></breadcrumb>
        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-12 col-md-3 col-xl-2">
                    <div class="sidebar-profile">
                        <div class="user d-flex align-items-center">
                            <div class="image">
                                <img src="https://via.placeholder.com/100" alt="">
                            </div>
                            <span class="name">
                                    Minhphucgroup
                                </span>
                        </div>
                        <div class="link-profile">
                            <a href="#" class="d-flex link-1">
                                <i class="fa fa-user" aria-hidden="true"></i>
                                Tài khoản của tôi
                            </a>
                            <a href="#" class="link-2">Hồ sơ thành viên</a>
                            <a href="#" class="link-2">Địa chỉ nhận hàng</a>
                            <a href="#" class="link-2">Đổi mật khẩu</a>

                            <a href="#" class="d-flex link-1">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                Đơn mua
                            </a>

                            <a href="#" class="d-flex link-1">
                                <i class="fa fa-bell-o" aria-hidden="true"></i>
                                Thông báo
                            </a>
                            <a href="#" class="link-2">Cập nhật đơn hàng</a>
                            <a href="#" class="link-2">Khuyến mại</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-9 col-xl-10">
                    <div class="profile-box bg-white shadow py-3 px-4">
                        <h4 class="fs-20 text-theme mb-0 font-weight-bold pt-1">HỒ SƠ THÀNH VIÊN</h4>
                        <p class="fs-18 mb-0 text-707070">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                    </div>
                    <div class="profile-box bg-white shadow  py-3 pt-5 px-4 mt-4 rounded-10">
                        <h4 class="fs-20 font-weight-bold mb-4 pb-3">THÔNG TIN THÀNH VIÊN</h4>
                        <form action="">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Họ và tên
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Ngày sinh
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Điện thoại
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Email
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Mã giới thiệu
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Địa chỉ
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Ảnh đại diện
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6 d-md-flex align-items-center box-upload">
                                    <div class="upload d-flex align-items-center justify-content-center my-2 my-md-0">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                        <span class="fs-18 text-707070">Cập nhật ảnh</span>
                                    </div>
                                    <p class="fs-18 text-707070 font-italic note">
                                        Dung lượng file tối đa 1 MB Định dạng:.JPEG, .PNG
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="profile-box bg-white shadow  py-3 pt-5 px-4 mt-4 rounded-10">
                        <h4 class="fs-20 font-weight-bold mb-4 pb-3">THẺ CĂN CƯỚC / CMTND</h4>
                        <form action="">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Thẻ CCCD / CMTND
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Số
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Ngày cấp
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Nới cấp
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="profile-box bg-white shadow  py-3 pt-5 px-4 mt-4 rounded-10">
                        <h4 class="fs-20 font-weight-bold mb-4 pb-3">NGÂN HÀNG</h4>
                        <form action="">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Ngân hàng
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Số tài khoản
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Chủ tài khoản
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Chi nhánh
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="profile-box bg-white shadow  py-3 pt-5 px-4 mt-4 rounded-10">
                        <h4 class="fs-20 font-weight-bold mb-4 pb-3">THÔNG TIN KHÁC</h4>
                        <form action="">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Văn phòng
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Số điểm MPG
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Chi nhánh
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6">
                                    <input type="text" class="input-text">
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4 col-xl-2">
                                    <label for="" class="text-left mb-0">
                                        Google capcha
                                    </label>
                                </div>
                                <div class="col-md-8 col-xl-6 capcha">
                                    <img src="images/img-capcha.png" alt="">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="text-center pt-3 pb-3 mt-5 profile-box">
                        <button class="btn-green btn-reg">XÁC NHẬN</button>
                        <button class="btn-green btn-reg btn-cancel">XÁC NHẬN</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@stop
@section('JS_BOTTOM_REGION')
    <script type="text/javascript">
        $('[data-fancybox="gallery"]').fancybox({});
        if (window.matchMedia && window.matchMedia('(max-width: 979px)').matches && MagicZoom !== undefined) {
            MagicZoom.options['zoom-position'] = 'inner';
            MagicZoom.refresh();
        } else if (window.matchMedia && window.matchMedia('(min-width: 980px)').matches && MagicZoom !== undefined) {
            MagicZoom.options['zoom-position'] = 'right';
            MagicZoom.options = {
                'zoom-width': 380,
                'zoom-height': 380
            }
            MagicZoom.refresh();
        }
        $(window).bind('load', function(){
            $('#menu').mmenu();

        });

    </script>
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('mpg-tmp/giaodienmuahang/js/cart/pdetail.js') !!}
@endsection