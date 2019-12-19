@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ mix('/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
<!-- Datatables Header -->
<ul class="breadcrumb breadcrumb-top">
    <li>Dashboard</li>
</ul>
<!-- END Datatables Header -->

<div class="block full">
    <div id="page-content">
        <!-- Mini Top Stats Row -->
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <!-- Widget -->
                <a href="{{ route('webhooks.index') }}" class="widget widget-hover-effect1">
                    <div class="widget-simple">
                        <div class="widget-icon pull-left themed-background-spring animation-fadeIn">
                            <i class="fa fa-desktop"></i>
                        </div>
                        <h3 class="widget-content text-right animation-pullDown">
                             <strong>{{ $countData['webhook'] }}</strong> Webhooks<br>
                        </h3>
                    </div>
                </a>
                <!-- END Widget -->
            </div>
            <div class="col-sm-6 col-lg-3">
                <!-- Widget -->
                <a href="/history" class="widget widget-hover-effect1">
                    <div class="widget-simple">
                        <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                            <i class="fa fa-history"></i>
                        </div>
                        <h3 class="widget-content text-right animation-pullDown">
                             <strong>{{ $countData['payloadHistory'] }}</strong> Payload Histories<br>
                        </h3>
                    </div>
                </a>
                <!-- END Widget -->
            </div>
            <div class="col-sm-6 col-lg-3">
                <!-- Widget -->
                <a href="/history" class="widget widget-hover-effect1">
                    <div class="widget-simple">
                        <div class="widget-icon pull-left themed-background-muted animation-fadeIn">
                            <i class="fa fa-history"></i>
                        </div>
                        <h3 class="widget-content text-right animation-pullDown">
                             <strong>{{ $countData['messageHistory'] }}</strong> Message Histories<br>
                        </h3>
                    </div>
                </a>
                <!-- END Widget -->
            </div>
            <div class="col-sm-6 col-lg-3">
                <!-- Widget -->
                <a href="{{ route('bots.index') }}" class="widget widget-hover-effect1">
                    <div class="widget-simple">
                        <div class="widget-icon pull-left themed-background-amethyst animation-fadeIn">
                            <i class="fa fa-reddit"></i>
                        </div>
                        <h3 class="widget-content text-right animation-pullDown">
                             <strong>{{ $countData['bot'] }}</strong> Bots<br>
                        </h3>
                    </div>
                </a>
                <!-- END Widget -->
            </div>
        </div>
    </div>
</div><br/>
<div class="block full">
    <div id="page-content">
        <div class="row">

            <form method="GET" action="{{ route('dashboard.index') }}">
                <div class="col-sm-6 col-lg-3">
                    <input type="text" id="date-ranger" name="daterange" class="form-control input-datepicker" value="{{ request('daterange') ?? '' }}" />
                </div>
                <button class="btn btn-md btn-search"><i class="fa fa-bar-chart-o"></i> Statistic</button>
            </form>

        </div><br/>

        <div class="row">
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-advanced widget-advanced-alt">
                        <!-- Widget Header -->
                        <div class="widget-header text-center themed-background">
                            <h3 class="widget-content-light text-left pull-left animation-pullDown">
                                <strong>Payload Histories</strong><br>
                            </h3>
                            <!-- Flot Charts (initialized in js/pages/index.js), for more examples you can check out http://www.flotcharts.org/ -->

                            <div id="payload-histories-chart" class="chart" style="margin-top: 50px; padding: 0px; position: relative;"><canvas class="flot-base" width="713" height="450" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 571px; height: 360px;"></canvas><canvas class="flot-overlay" width="713" height="450" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 571px; height: 360px;"></canvas></div>
                        </div>
                        <!-- END Widget Header -->

                        <!-- Widget Main -->
                        <div class="widget-main">
                            <div class="row text-center">
                                <div class="col-xs-6">
                                    <h3 class="animation-hatch">
                                        <strong>{{ $payloadHistory['successCases'] }}</strong><br>
                                        <div class="rectangle-successful"></div><small>Successful Cases</small>
                                    </h3>
                                </div>
                                <div class="col-xs-6">
                                    <h3 class="animation-hatch">
                                        <strong>{{ $payloadHistory['failedCases'] }}</strong><br>
                                        <div class="rectangle-failed"></div><small>Failed Cases</small>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <!-- END Widget Main -->
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-advanced widget-advanced-alt">
                        <!-- Widget Header -->
                        <div class="widget-header text-center themed-background">
                            <h3 class="widget-content-light text-left pull-left animation-pullDown">
                                <strong>Message Histories</strong><br>
                            </h3>
                            <div id="message-histories-chart" class="chart" style="margin-top: 50px; padding: 0px; position: relative;"><canvas class="flot-base" width="713" height="450" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 571px; height: 360px;"></canvas><canvas class="flot-overlay" width="713" height="450" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 571px; height: 360px;"></canvas></div>
                        </div>
                        <!-- END Widget Header -->

                        <!-- Widget Main -->
                        <div class="widget-main">
                            <div class="row text-center">
                                <div class="col-xs-6">
                                    <h3 class="animation-hatch">
                                        <strong>{{ $messageHistory['successCases'] }}</strong><br>
                                        <div class="rectangle-successful"></div><small>Successful Cases</small>
                                    </h3>
                                </div>
                                <div class="col-xs-6">
                                    <h3 class="animation-hatch">
                                        <strong>{{ $messageHistory['failedCases'] }}</strong><br>
                                        <div class="rectangle-failed"></div><small>Failed Cases</small>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <!-- END Widget Main -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    var payloadHistory = {!! json_encode($payloadHistory) !!};
    var messageHistory = {!! json_encode($messageHistory) !!};
</script>
<script src="{{ asset('js/chart.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
