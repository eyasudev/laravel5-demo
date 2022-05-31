@extends('layouts.app')
@section('breadcrum')
    <div class="page-header page-header-default">
        <div class="breadcrumb-line">
            {!! Breadcrumbs::render('feature-content') !!}

            @if(\Auth::user()->hasRole(['Broker']))
                <ul class="breadcrumb-elements">
                    <li>
                        <span class="label custom-update-button mt-10 submit-listing"
                              onclick="window.location='{{ url('/fieldservices/Agent/MyListings#SubmitAListing') }}'">SUBMIT LISTING</span>
                    </li>
                </ul>
            @endif
        </div>
    </div>
@endsection
@section('content')
    @if(Session::has('success'))
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('success') }}
        </div>
    @endif
    <div class="panel panel-flat MyListings_Main">
        <div class="panel-body table-responsive custom-responsive mr-minus-10">
            <div class="row heading-table">
                <div class="col-md-1 col-sm-1 col-xs-1 webview Signed text-center">
                </div>
                <div class="col-md-3 col-sm-3 col-xs-3 webview Signed text-center">
                    <input type="text" name="searchlisting" id="searchlisting" class="form-control"
                           placeholder="Enter Address, Agent name..">
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8 webview Signed text-center">
                </div>
            </div>
            <div class="row heading-table">
                <div class="col-md-1 col-sm-1 col-xs-1 webview Signed text-center">
                    <b>City</b>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-5 Address text-left">
                    <b>Address</b>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-1 webview Neighborhood text-center">
                    <b sortby="meighbour" class="sorting sortable both" sort="desc">Neighborhood</b>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-2 listing_digest text-center">
                    <b>Digest</b>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-2 listing_agent text-center">
                    <b sortby="agent" class="sorting sortable both" sort="desc">Listing Agent</b>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-1 Price text-center">
                    <b sortby="price" class="sorting sortable both" sort="desc">Price</b>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-2 webview Date text-center">
                    <b sortby="marketdate" class="sorting  sortable asc" sort="asc">Market Date</b>
                </div>
            </div>
            <hr>
            <div class="ComingsoonListings" id="ComingsoonListings_feresh">
                @if(count($listings) > 0)
                    @foreach($listings as $list)
                        <div class="row mainrow" id="mainrow_{{$list->id}}" lid="{{$list->id}}" style="cursor: pointer">
                            <div class="col-md-1 col-sm-1 col-xs-1 webview Signed text-center">
                                <div class="media-middle">
                                    <a href="#"
                                       class="btn bg-custom btn-rounded btn-icon btn-xs legitRipple custom-btn-rounded pl-5">
                                        <span class="letter-icon city_icon_{{$list->id}}">{{ ($list->city) ? substr($list->city, 0, 3) : ""}}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-5 Address text-left">
                                <div class="media-left">
                                    <div>
                                        <a href="javascript:void(0)"
                                           class="text-default text-semibold address_{{$list->id}}">{{ $list->address or '' }}</a>
                                    </div>
                                    <div class="text-muted text-size-small">
                                        <span class="status-mark position-left webview"></span>
                                        <span class="city_{{$list->id}}">{{ $list->city or '' }}, {{ $list->state or '' }} {{ $list->zip or '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1 col-xs-1 webview Neighborhood text-center">
                                <span class="text-muted sub_area_{{$list->id}}">{{ $list->sub_area or '' }}</span>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-2 listing_agent webview  text-center">
                                <button class="btn btn-sm custom-view-button" type="button"
                                        onclick="open_modal('{{ encrypt($list->id) }}');event.stopPropagation();">View
                                </button>

                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-2 listing_agent webview  text-center">
                                <span class="text-muted listing_agent_{{$list->id}}">@if($list->listing_agent_name != ''){{ $list->listing_agent_name }} @else {{ $list->name or $list->email }} @endif</span>
                            </div>
                            <div class="col-md-1 col-sm-1 col-xs-1 Price text-center">
                                <strong class="text-semibold price_{{$list->id}}">
                                    @if(is_numeric($list->price))
                                        ${{ number_format($list->price,0) }} @else {{ $list->price or '' }} @endif
                                    @if(isset($list->listing_price_range)) -
                                    @if(is_numeric($list->listing_price_range))
                                        ${{ number_format($list->listing_price_range,0) }} @else {{ $list->listing_price_range or '' }} @endif @endif
                                </strong>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-2 webview Date text-center">
                                <span class="ml-15">@if($list->active_mls_date != '0000-00-00 00:00:00'){{ date('m/d/Y',strtotime($list->active_mls_date)) }} @elseif($list->active_mls_date_tbd)
                                        TBD @else none @endif</span>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div id="Report_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-semibold">Listing Submission Digest</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
@endsection
@section("custom_css")
    <link href="{{asset('css/mylistings.css')}}" rel="stylesheet" type="text/css">
    <style>
        @media (min-width: 1025px) {
            .modal-lg {
                width: 80%;
            }
        }
    </style>
@endsection
@section('custom_javascript')
    <script type="text/javascript">
        var moduleurl = '{{ url('/fieldservices') }}';
        var _token = '{{ csrf_token() }}';
        $(document).on("click", ".sorting", function (event) {
            event.preventDefault();
            var sortby = $(this).attr("sortby");
            var sort_order = $(this).attr("sort");

            $(".sortable").removeClass("asc desc");
            $(".sortable").addClass("both");

            if (sort_order == 'desc') {
                $(this).attr('sort', 'asc');
                $(this).removeClass("both");
                $(this).addClass("asc");
                sort_order = 'asc';

            } else {
                $(this).attr('sort', 'desc');
                $(this).removeClass("both");
                $(this).addClass("desc");
                sort_order = 'desc';
            }

            $.get(moduleurl + "/comingsoon_listing_ajax_refresh?sortby=" + sortby + "&sort_order=" + sort_order, function () {

            }).done(function (res) {
                $("#ComingsoonListings_feresh").html(res);
            });

        });

        $(document).on("keyup", "#searchlisting", function (event) {

            $(".sorting").each(function () {
                if ($(this).attr("sortby") == "marketdate") {
                    $(this).attr('sort', 'asc');
                    $(this).find('i').removeClass("icon-arrow-down5");
                    $(this).find('i').addClass("icon-arrow-up5");
                }
            });

            $.get(moduleurl + "/comingsoon_listing_ajax_refresh?search=" + $(this).val(), function () {
            }).done(function (res) {
                $("#ComingsoonListings_feresh").html(res);
            });
        });

        function open_modal(id) {
            $('#Report_modal').modal('show');
            $('#Report_modal .modal-body').html('<div id="spinner" style="text-align: center;"><i class="icon-spinner spinner" style="font-size: 50px;"></i> <br>Loading Report</div>');
            $.ajax({
                url: moduleurl + '/get-listing-report',
                data: {'id': id, '_token': _token},
                type: 'post',
                success: function (data) {
                    $('#Report_modal .modal-body').html(data);
                },
                error: function (data) {
                    $('#Report_modal').modal('hide');
                    $('#Report_modal .modal-body').html("");
                    new PNotify({
                        title: 'Error',
                        text: data.responseJSON.msg,
                        addclass: 'bg-danger'
                    });
                }
            });
        }
    </script>
@endsection