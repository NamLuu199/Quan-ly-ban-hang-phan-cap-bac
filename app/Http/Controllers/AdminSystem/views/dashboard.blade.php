@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setCssLink('backend-ui/assets/js/plugins/ui/fullcalendar.min.css') !!}

    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/fullcalendar.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/fullcalendar/lang/vi.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/visualization/d3/d3_tooltip.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/daterangepicker.js') !!}
    <style type="text/css">
        .panel-white > .panel-heading {
            padding: 9px 15px;
        }

        .panel-white > .panel-heading h3 {
            font-size: 18px;
        }

    </style>
@stop

@section('CONTENT_REGION')
    <div class="row">
        <div class="col-12 col-sm-12 col-md-10 col-lg-10">
            <div class="row">
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-teal-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$vichietkhau['total_money']) }}</h3>
                            Ví chiết khấu
                            <div class="text-muted text-size-small">---</div>
                        </div>
                        <div id="vi-chietkhau"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-blue-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$vitichluy['total_money']) }}</h3>
                            Ví tích luỹ
                            <div class="text-muted text-size-small">---</div>
                        </div>
                        <div id="vi-tichluy"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-pink-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$vicongno['total_money']) }}</h3>
                            Ví công nợ
                            <div class="text-muted text-size-small">---</div>
                        </div>
                        <div id="vi-congno"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-success-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$vihoahong['total_money']) }}</h3>
                            Ví hoa hồng
                            <div class="text-muted text-size-small">---</div>
                        </div>
                        <div id="vi-hoahong"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                @if(isset($khodiem))
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-info-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$khodiem['total_money']) }}</h3>
                            Kho điểm
                            <div class="text-muted text-size-small">
                                @if(isset($khodiem['so_diem_treo_gio']) && $khodiem['so_diem_treo_gio'] > 0)Số điểm đang trao đổi: {{ \App\Elibs\Helper::formatMoney(@$khodiem['so_diem_treo_gio']) }}
                                @else
                                    ---
                                @endif
                            </div>
                        </div>
                        <div id="vi-khodiem"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-info-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$khodiemsile['total_money']) }}</h3>
                            Kho điểm sỉ
                            <div class="text-muted text-size-small">
                                @if(isset($khodiemsile['so_diem_treo_gio']) && $khodiemsile['so_diem_treo_gio'] > 0)Số điểm đang trao đổi: {{ \App\Elibs\Helper::formatMoney(@$khodiemsile['so_diem_treo_gio']) }}
                                @else
                                    ---
                                @endif
                            </div>
                        </div>
                        <div id="vi-khodiemsile"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                @endif
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-blue-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$vitieudung['total_money']) }}</h3>
                            Ví tiêu dùng
                            <div class="text-muted text-size-small">
                                @if(isset($vitieudung['so_diem_treo_gio']) && $vitieudung['so_diem_treo_gio'] > 0)Số điểm đang trao đổi: {{ \App\Elibs\Helper::formatMoney(@$vitieudung['so_diem_treo_gio']) }}
                                @else
                                    ---
                                @endif
                            </div>
                        </div>
                        <div id="vi-tieudung"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                <div class="col-sm-4 col-md-2">
                    <!-- Today's revenue -->
                    <div class="panel bg-blue-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$vitieudungsile['total_money']) }}</h3>
                            Ví tiêu dùng sỉ
                            <div class="text-muted text-size-small">
                                @if(isset($vitieudungsile['so_diem_treo_gio']) && $vitieudungsile['so_diem_treo_gio'] > 0)Số điểm đang trao đổi: {{ \App\Elibs\Helper::formatMoney(@$vitieudungsile['so_diem_treo_gio']) }}
                                @else
                                    ---
                                @endif
                            </div>
                        </div>
                        <div id="vi-tieudungsile"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                <div class="col-md-12">
                    <!-- Today's revenue -->
                    <div class="panel bg-warning-400">
                        <div class="panel-body">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="reload"></a></li>
                                </ul>
                            </div>

                            <h3 class="no-margin">{{ \App\Elibs\Helper::formatMoney(@$tongdoanhthu['total_money']) }}</h3>
                            Tổng doanh thu 3 tháng gần đây
                            @if(@$tongdoanhthu)
                                @foreach(@$tongdoanhthu['group_doanhthu_theo_thang'] as $month)
                                    <div class="font-size-sm opacity-75">Tháng {{ $month['month'] }}: {{ \App\Elibs\Helper::formatMoney(@$month['total_money']) }}</div>
                                @endforeach
                            @endif
                        </div>
                        <div id="vi-tongdoanhthu"></div>
                    </div>
                    <!-- /today's revenue -->
                </div>
                <div class="col-lg-12">

                    <!-- Traffic layer -->
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h5 class="panel-title">Địa chỉ Công Ty Cố Phần Tập Đoàn Truyền Thông Minh Phúc - MinhPhucGroup</h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                    <li><a data-action="reload"></a></li>
                                    <li><a data-action="close"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d931.3675272210836!2d105.76345292921854!3d20.973784551375786!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31345321437a3e9b%3A0x751aed7c4ccbe5f5!2zQ8O0bmcgdHkgY-G7lSBwaOG6p24gdOG6rXAgxJFvw6BuIHRydXnhu4FuIHRow7RuZyBNaW5oIFBow7pj!5e0!3m2!1sen!2s!4v1594653352020!5m2!1sen!2s" width="100%" height="560px" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                        </div>
                    </div>
                    <!-- /traffic layer -->

                </div>

            </div>
            <div class="row">

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-2 col-lg-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="fb-page" data-href="https://www.facebook.com/MPhangtieudungfugo/" data-tabs="timeline,messages" data-width="500" data-height="810px" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/MPhangtieudungfugo/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/MPhangtieudungfugo/">MP_Hàng Tiêu Dùng FuGo</a></blockquote></div>
                    <div id="fb-root"></div>
                    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v7.0&appId=829801480881172&autoLogAppEvents=1" nonce="eUUHzojF"></script>
                </div>
            </div>
        </div>
    </div>
@stop

@section('JS_BOTTOM_REGION')
    <script type="text/javascript">
        $(['#vi-chietkhau', '#vi-tichluy','#vi-congno', '#vi-hoahong', '#vi-tieudung', '#vi-khodiem', '#vi-khodiemsile', '#vi-tieudungsile', '#vi-tongdoanhthu']).each(function (i, e) {
            dailyRevenue(e, 50); // initialize chart
        })


        // Chart setup
        function dailyRevenue(element, height) {


            // Basic setup
            // ------------------------------

            // Add data set
            var dataset = [
                {
                    "date": "04/13/14",
                    "alpha": "60"
                }, {
                    "date": "04/14/14",
                    "alpha": "35"
                }, {
                    "date": "04/15/14",
                    "alpha": "65"
                }, {
                    "date": "04/16/14",
                    "alpha": "50"
                }, {
                    "date": "04/17/14",
                    "alpha": "65"
                }, {
                    "date": "04/18/14",
                    "alpha": "20"
                }, {
                    "date": "04/19/14",
                    "alpha": "60"
                }
            ];

            // Main variables
            var d3Container = d3.select(element),
                margin = {top: 0, right: 0, bottom: 0, left: 0},
                width = d3Container.node().getBoundingClientRect().width - margin.left - margin.right,
                height = height - margin.top - margin.bottom,
                padding = 20;

            // Format date
            var parseDate = d3.time.format("%m/%d/%y").parse,
                formatDate = d3.time.format("%a, %B %e");



            // Add tooltip
            // ------------------------------

            var tooltip = d3.tip()
                .attr('class', 'd3-tip')
                .html(function (d) {
                    return "<ul class='list-unstyled mb-5'>" +
                        "<li>" + "<div class='text-size-base mt-5 mb-5'><i class='icon-check2 position-left'></i>" + formatDate(d.date) + "</div>" + "</li>" +
                        "<li>" + "Sales: &nbsp;" + "<span class='text-semibold pull-right'>" + d.alpha + "</span>" + "</li>" +
                        "<li>" + "Revenue: &nbsp; " + "<span class='text-semibold pull-right'>" + "$" + (d.alpha * 25).toFixed(2) + "</span>" + "</li>" +
                        "</ul>";
                });



            // Create chart
            // ------------------------------

            // Add svg element
            var container = d3Container.append('svg');

            // Add SVG group
            var svg = container
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
                .call(tooltip);



            // Load data
            // ------------------------------

            dataset.forEach(function (d) {
                d.date = parseDate(d.date);
                d.alpha = +d.alpha;
            });



            // Construct scales
            // ------------------------------

            // Horizontal
            var x = d3.time.scale()
                .range([padding, width - padding]);

            // Vertical
            var y = d3.scale.linear()
                .range([height, 5]);



            // Set input domains
            // ------------------------------

            // Horizontal
            x.domain(d3.extent(dataset, function (d) {
                return d.date;
            }));

            // Vertical
            y.domain([0, d3.max(dataset, function (d) {
                return Math.max(d.alpha);
            })]);



            // Construct chart layout
            // ------------------------------

            // Line
            var line = d3.svg.line()
                .x(function(d) {
                    return x(d.date);
                })
                .y(function(d) {
                    return y(d.alpha)
                });



            //
            // Append chart elements
            //

            // Add mask for animation
            // ------------------------------

            // Add clip path
            var clip = svg.append("defs")
                .append("clipPath")
                .attr("id", "clip-line-small");

            // Add clip shape
            var clipRect = clip.append("rect")
                .attr('class', 'clip')
                .attr("width", 0)
                .attr("height", height);

            // Animate mask
            clipRect
                .transition()
                .duration(1000)
                .ease('linear')
                .attr("width", width);



            // Line
            // ------------------------------

            // Path
            var path = svg.append('path')
                .attr({
                    'd': line(dataset),
                    "clip-path": "url(#clip-line-small)",
                    'class': 'd3-line d3-line-medium'
                })
                .style('stroke', '#fff');

            // Animate path
            svg.select('.line-tickets')
                .transition()
                .duration(1000)
                .ease('linear');



            // Add vertical guide lines
            // ------------------------------

            // Bind data
            var guide = svg.append('g')
                .selectAll('.d3-line-guides-group')
                .data(dataset);

            // Append lines
            guide
                .enter()
                .append('line')
                .attr('class', 'd3-line-guides')
                .attr('x1', function (d, i) {
                    return x(d.date);
                })
                .attr('y1', function (d, i) {
                    return height;
                })
                .attr('x2', function (d, i) {
                    return x(d.date);
                })
                .attr('y2', function (d, i) {
                    return height;
                })
                .style('stroke', 'rgba(255,255,255,0.3)')
                .style('stroke-dasharray', '4,2')
                .style('shape-rendering', 'crispEdges');

            // Animate guide lines
            guide
                .transition()
                .duration(1000)
                .delay(function(d, i) { return i * 150; })
                .attr('y2', function (d, i) {
                    return y(d.alpha);
                });



            // Alpha app points
            // ------------------------------

            // Add points
            var points = svg.insert('g')
                .selectAll('.d3-line-circle')
                .data(dataset)
                .enter()
                .append('circle')
                .attr('class', 'd3-line-circle d3-line-circle-medium')
                .attr("cx", line.x())
                .attr("cy", line.y())
                .attr("r", 3)
                .style('stroke', '#fff')
                .style('fill', '#29B6F6');



            // Animate points on page load
            points
                .style('opacity', 0)
                .transition()
                .duration(250)
                .ease('linear')
                .delay(1000)
                .style('opacity', 1);


            // Add user interaction
            points
                .on("mouseover", function (d) {
                    tooltip.offset([-10, 0]).show(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 4);
                })

                // Hide tooltip
                .on("mouseout", function (d) {
                    tooltip.hide(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 3);
                });

            // Change tooltip direction of first point
            d3.select(points[0][0])
                .on("mouseover", function (d) {
                    tooltip.offset([0, 10]).direction('e').show(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 4);
                })
                .on("mouseout", function (d) {
                    tooltip.direction('n').hide(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 3);
                });

            // Change tooltip direction of last point
            d3.select(points[0][points.size() - 1])
                .on("mouseover", function (d) {
                    tooltip.offset([0, -10]).direction('w').show(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 4);
                })
                .on("mouseout", function (d) {
                    tooltip.direction('n').hide(d);

                    // Animate circle radius
                    d3.select(this).transition().duration(250).attr('r', 3);
                })



            // Resize chart
            // ------------------------------

            // Call function on window resize
            $(window).on('resize', revenueResize);

            // Call function on sidebar width change
            $('.sidebar-control').on('click', revenueResize);

            // Resize function
            //
            // Since D3 doesn't support SVG resize by default,
            // we need to manually specify parts of the graph that need to
            // be updated on window resize
            function revenueResize() {

                // Layout variables
                width = d3Container.node().getBoundingClientRect().width - margin.left - margin.right;


                // Layout
                // -------------------------

                // Main svg width
                container.attr("width", width + margin.left + margin.right);

                // Width of appended group
                svg.attr("width", width + margin.left + margin.right);

                // Horizontal range
                x.range([padding, width - padding]);


                // Chart elements
                // -------------------------

                // Mask
                clipRect.attr("width", width);

                // Line path
                svg.selectAll('.d3-line').attr("d", line(dataset));

                // Circles
                svg.selectAll('.d3-line-circle').attr("cx", line.x());

                // Guide lines
                svg.selectAll('.d3-line-guides')
                    .attr('x1', function (d, i) {
                        return x(d.date);
                    })
                    .attr('x2', function (d, i) {
                        return x(d.date);
                    });
            }
        }


    </script>
@stop