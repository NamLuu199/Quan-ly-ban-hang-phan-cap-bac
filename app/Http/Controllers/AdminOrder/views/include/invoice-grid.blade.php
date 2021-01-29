@extends($THEME_EXTEND)

@section('CONTENT_REGION')
    <div class="col-md-6">
        <div class="panel invoice-grid">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h6 class="text-semibold no-margin-top">Rebecca Manes</h6>
                        <ul class="list list-unstyled">
                            <li>Invoice #: &nbsp;0027</li>
                            <li>Issued on: <span class="text-semibold">2015/02/24</span></li>
                        </ul>
                    </div>

                    <div class="col-sm-6">
                        <h6 class="text-semibold text-right no-margin-top">$5,100</h6>
                        <ul class="list list-unstyled text-right">
                            <li>Method: <span class="text-semibold">Paypal</span></li>
                            <li class="dropdown">
                                Status: &nbsp;
                                <a href="#" class="label bg-success-400 dropdown-toggle" data-toggle="dropdown">Paid <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="#"><i class="icon-alert"></i> Overdue</a></li>
                                    <li><a href="#"><i class="icon-alarm"></i> Pending</a></li>
                                    <li class="active"><a href="#"><i class="icon-checkmark3"></i> Paid</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#"><i class="icon-spinner2 spinner"></i> On hold</a></li>
                                    <li><a href="#"><i class="icon-cross2"></i> Canceled</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="panel-footer">
                <ul>
                    <li><span class="status-mark border-success position-left"></span> Due: <span class="text-semibold">2015/03/24</span></li>
                </ul>

                <ul class="pull-right">
                    <li><a href="#" data-toggle="modal" data-target="#invoice"><i class="icon-eye8"></i></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i> <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="#"><i class="icon-printer"></i> Print invoice</a></li>
                            <li><a href="#"><i class="icon-file-download"></i> Download invoice</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><i class="icon-file-plus"></i> Edit invoice</a></li>
                            <li><a href="#"><i class="icon-cross2"></i> Remove invoice</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@stop