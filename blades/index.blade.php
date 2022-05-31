@extends('layouts.app')
@section('breadcrum')

    <div class="page-header page-header-default">
        <div class="breadcrumb-line">
            {!! Breadcrumbs::render('openhouse_listing_view') !!}
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
    @if($feedbacks > 0)
        <div class="alert bg-warning alert-styled-right">
            <button type="button" class="close" data-dismiss="alert">
                <span>×</span><span class="sr-only">Close</span>
            </button>
            <span class="text-semibold">Warning!</span> You have {{ $feedbacks }} pending feedback, please
            <a style="text-decoration: underline;" href="/openhouses/pendingfeedbacks" class="alert-link">Click here</a>
        </div>
    @endif
    @if($islive)
        <div class="alert openhouse_red_box">
            <span class="text-semibold">Open House sign up is open! Please click one or more available boxes below to reserve time slots.</span>
        </div>
    @endif
    <div class="panel panel-flat">
        <div class="panel-body table-responsive" id="openhouse_listing_view">
            <div class="row">
                <div class="col-md-1 col-sm-1 col-xs-1 webview Signed text-center"><b>City</b></div>
                <div class="col-md-3 col-sm-3 col-xs-5 Address text-left"><b>Address</b></div>
                <div class="col-md-2 col-sm-2 col-xs-2 webview Neighborhood text-center"><b>Neighborhood</b></div>
                <div class="col-md-2 col-sm-2 col-xs-2 webview Date text-center"><b>Listing Date</b></div>
                <div class="col-md-2 col-sm-2 col-xs-3 Price text-right"><b>Price</b></div>
                <div class="col-md-1 col-sm-1 col-xs-1 webview Vacancy text-center"><b>Vacancy</b></div>
                <div class="col-md-1 col-sm-1 col-xs-3 mobile_timeslot text-center"><b>Time Slots</b></div>
            </div>
            <hr>
            @if(count($listings) > 0)
                @foreach($listings as $list)
                    <div class="row drop_down_slots drop_down_slots_{{ $list->id }} mainrow" id="mainrow_{{$list->id}}"
                         title="{{($islive && isset($availability[$list->id]) && $list->allow_other_agents==1) ? 'Click To Collpase' :'Click to Expand' }}"
                         lid="{{$list->id}}" style="cursor: pointer">
                        <div class="col-md-1 col-sm-1 col-xs-1 webview Signed text-center" id="Signed_{{$list->id}}">
                            <div class="media-middle">
                                <a href="#" class="btn bg-custom btn-rounded btn-icon btn-xs legitRipple custom-btn-rounded">
                                    <span class="letter-icon">{{ ($list->city) ? substr($list->city, 0, 3) : ""}}</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-5 Address text-left" id="Address_{{$list->id}}">
                            <div class="media-left">
                                <div>
                                    <a href="javascript:void(0)" class="text-default text-semibold ">{{ $list->address or '' }}</a>
                                </div>
                                <div class="text-muted text-size-small">
                                    <span class="status-mark position-left webview"></span>
                                    {{ $list->city or '' }}, {{ $list->state or '' }} {{ $list->zip or '' }}
                                </div>
                                <div class="webview">{{ $list->listing_agent_name or '' }}</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-2 webview Neighborhood text-center" id="Neighborhood_{{$list->id}}">
                            <span class="text-muted">{{ $list->sub_area or '' }}</span>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-2 webview Date text-center" id="Date_{{$list->id}}">
                            @if(isset($list->active_mls_date) && $list->active_mls_date != '0000-00-00 00:00:00' )
                                <span>{{ date("m/d/Y", strtotime($list->active_mls_date)) }}</span>
                            @else
                                <span>TBD</span>
                            @endif
                            @if($list->is_New)
                                <label class="new_listing">New</label>
                            @endif
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-3 Price text-right" id="Price_{{$list->id}}">
                            <strong class="text-semibold">
                                @if(is_numeric($list->price))
                                    ${{ number_format($list->price,2) }} @else {{ $list->price or '' }}
                                @endif
                            </strong>
                        </div>
                        <div class="col-md-1 col-sm-1 col-xs-1 webview col-sm-1 Vacancy text-center" id="Vacancy_{{$list->id}}">
                            @if($list->vacancy)
                                <span class="label span-vacancy">Vacant</span>
                            @else
                                <span class="label span-vacancy">Occupied</span>
                            @endif
                        </div>
                        <div class="col-md-1 col-sm-1 col-xs-3 mobiletimeslot TimeSlot text-center drop_down_slots_{{ $list->id }}"
                             id="TimeSlot_{{$list->id}}" lid="{{ $list->id }}" style="cursor: pointer;">
                            @if(isset($availability[$list->id]))
                                @if($availability[$list->id] > 0)

                                    @if($islive && $list->allow_other_agents==1)
                                        <a href="javascript:void(0)" class=" drop_down_slots_{{ $list->id }}"
                                           lid="{{ $list->id }}">
                                        <span class="label bg-green-custom">
                                            {{ $availability[$list->id] or 0 }} / {{ $total_slots[$list->id] or 0 }}
                                        </span>
                                        </a>
                                    @else
                                        <a href="javascript:void(0)" class=" drop_down_slots_{{ $list->id }}"
                                           lid="{{ $list->id }}">
                                            <span class="label bg-danger">
                                                @if($list->allow_other_agents==0) 0 /
                                                0 @else {{ $availability[$list->id] or 0 }}
                                                / {{ $total_slots[$list->id] or 0 }} @endif
                                            </span>
                                        </a>
                                    @endif
                                @else
                                    <a href="javascript:void(0)" class=" drop_down_slots_{{ $list->id }}"
                                       lid="{{ $list->id }}">
                                        <span class="label bg-danger">
                                            0 / {{ $total_slots[$list->id] or 0 }}
                                        </span>
                                    </a>
                                @endif
                            @else
                                <a href="javascript:void(0)" class=" drop_down_slots_{{ $list->id }}"
                                   lid="{{ $list->id }}">
                                    <span class="label bg-danger">
                                        0 / {{ $total_slots[$list->id] or 0 }}
                                    </span>
                                </a>
                            @endif
                        </div>
                        <div class="col-xs-1 ShowMore" onclick="opendetailmodal('{{ $list->id }}', this)" id="{{$list->id}}"
                             style="display: none;">...
                        </div>
                    </div>
                    <div align="center" class="@if($islive) active @endif border-double"
                         @if($islive && isset($availability[$list->id]) && $list->allow_other_agents==1)
                         style=""
                         @else
                         style="display:none"
                         @endif
                         id="drop_down_slots_{{ $list->id }}">
                        {!! $oh->get_slots_async($list->id) !!}
                    </div>
                    </hr>
                @endforeach
            @endif
        </div>
    </div>
    <div class="modal fade table-responsive" id="new_openhouse_slot_create" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true" align="center">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="text-align: left">
                <div class="modal-header">
                    <ul class="nav navbar-nav navbar-left">
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Edit Open House Slot</h4>
                </div>
                <div class="modal-body" style="min-height:400px">
                    <div class="form-group">
                        <table id="avaibility_slot_table">
                            <tr>
                                <td class="text-bold">Address</td>
                                <td><a href="#" id="availability_address_placeholder"> ... </a></td>
                            </tr>
                            <tr>
                                <td class="text-bold" style="white-space: nowrap;">Time Slots</td>
                                <td id="time_slots_modal"></td>
                            </tr>
                        </table>
                        <div class="form-group" id="availability_days_placeholder">
                            <progress max="200"></progress>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="new_listing_create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" align="center">
        <div class="modal-dialog modal-lg">
            <form action="javascript:void(0)" method="post" id="storelistingform" autocomplete="false">
                <div class="modal-content" style="text-align: left">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <ul class="nav navbar-nav navbar-left">

                        </ul>
                        <a type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
                        <h4 class="modal-title" id="myModalLabel"> Create a New Listing</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="listing_id" value="" id="listing_id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">

                                    <label>REColorado MLS</label>
                                    <input type="number" name="recolorado_mls" autocomplete="false"
                                           class="form-control recolorado_mls_autocomplete" placeholder="REColorado MLS number..."/>

                                    <span class="help-block">
                                        <strong id="recolorado_mls" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>IRES MLS</label>
                                    <input type="number" name="mls_listing_id" class="form-control" placeholder="IRES MLS number..."/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="blank-label">&nbsp;</label>
                                    <input type="text" name="address" class="form-control typeahead" placeholder="Street address..." autocomplete="false"/>
                                    <span class="help-block">
                                        <strong id="address" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="City" class="form-control" placeholder="City..." autocomplete="false"/>
                                    <span class="help-block">
                                        <strong id="City" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="zip" class="form-control" placeholder="Zip Code..." autocomplete="false"/>
                                    <span class="help-block">
                                        <strong id="zip" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="sub_area" class="form-control" placeholder="Sub Area..." autocomplete="false"/>
                                    <span class="help-block">
                                        <strong id="sub_area" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                                    <input type="text" class="form-control pickadate" name="active_mls_date"
                                           placeholder="Date...">
                                </div>
                                <span class="help-block">
                                    <strong id="active_mls_date" class="savelisting alert alert-danger"></strong>
                                </span>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="price" class="form-control" placeholder="Price..." autocomplete="false"/>
                                    <span class="help-block">
                                        <strong id="price" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="listing_agent_name" autocomplete="false"
                                           class="form-control agents_autocomplete" placeholder="Listing Agent Name..." value="{{\Auth::user()->hasRole(['Administrative Coordinator'])?\Auth::user()->name:''}}" />
                                    <span class="help-block">
                                        <strong id="listing_agent_name" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="listing_agent_email" class="form-control" readonly placeholder="Listing agent email..." value="{{\Auth::user()->hasRole(['Administrative Coordinator'])?\Auth::user()->email:''}}"/>
                                    <span class="help-block">
                                        <strong id="listing_agent_email" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" id="client_email_txt" name="client_email" data-role="tagsinput"
                                           class="form-control" placeholder="Client email..." autocomplete="false" />
                                    <span class="help-block">
                                        <strong id="client_email" class="savelisting alert alert-danger"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="full-width-label">
                                        <input type="checkbox" name="is_New" checked="checked"/>
                                        This is a NEW listing
                                    </label>
                                    <label class="full-width-label">
                                        <input type="checkbox" name="other_agents_host" checked="checked"/>
                                        Other Agents can host open houses at this listing
                                    </label>
                                    <label class="full-width-label">
                                        <input type="checkbox" name="double_booking"/>
                                        2 Agents are required (double slots) at each showing
                                    </label>
                                    <label class="full-width-label">
                                        <input type="checkbox" name="contract_signed" checked="checked"/>
                                        There is a sign post in place
                                    </label>
                                    <label class="full-width-label">
                                        <input type="checkbox" name="vacancy_status" checked="checked"/>
                                        The house is vacant
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn pull-left btn border-slate text-slate-800 btn-flat legitRipple custom-review-btn-class-border" data-dismiss="modal">Cancel</a>
                        <a class="btn pull-left custom-update-button btn-flat" id="storelisting">Create</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{--Open House edit model--}}
    <div class="modal fade" id="EditModalOpenHouses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" align="center">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="text-align: left">
                <form action="javascript:void(0)" method="post" id="updatelisting_form">
                    <input type="hidden" name="listid" value="" id="edit_open_house_listid">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <ul class="nav navbar-nav navbar-left">
                        </ul>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"> Update Listing</h4>
                    </div>
                    <div id="Edit_openhouse_row" class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <a class="btn pull-left btn border-slate text-slate-800 btn-flat legitRipple custom-review-btn-class-border" data-dismiss="modal">Cancel</a>
                        <a class="btn pull-left custom-update-button btn-flat" id="updatelisting">Update</a>
                        <a href="#" class="btn btn-flat custom-update-button pull-left left delete_openhouse_detail">Delete</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--Open House Detail Model--}}
    <div id="OpenHouseListingDetails" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table class="table">
                            <tr>
                                <td>City</td>
                                <td class="text-left" id="Signed_details"></td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td id="Address_details"></td>
                            </tr>
                            <tr>
                                <td>Neighborhood</td>
                                <td id="Neighborhood_details"></td>
                            </tr>
                            <tr>
                                <td>Listing Date</td>
                                <td id="Date_details"></td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td id="Price_details"></td>
                            </tr>
                            <tr>
                                <td>Vacancy</td>
                                <td id="Vacancy_details"></td>
                            </tr>
                            <tr>
                                <td>Time Slots</td>
                                <td id="TimeSlot_details"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
@section("module_info")
    {{ Module::getManifest('openhouses')["name"] }} v.{{ Module::getManifest('openhouses')["version"] }}
@endsection
@endsection
@section("custom_css")
    <link href="{{asset('css/openhouse.css')}}" rel="stylesheet" type="text/css">
@endsection
@section('custom_javascript')
    <script type="text/javascript" src="{{asset('theme/assets/js/plugins/notifications/sweet_alert.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('theme/assets/js/plugins/notifications/pnotify.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap-tagsinput.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('theme/assets/js/plugins/forms/styling/switch.min.js')}}"></script>
    <script type="text/javascript"
            src="{{asset('theme/assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/theme/assets/js/plugins/notifications/bootbox.min.js') }}"></script>
    <script src="{{asset('js/bootstrap3-typeahead.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('js/reconnecting-websocket.min.js')}}"></script>
    <script>
        var moduleurl           = '{{ e(url('/openhouses')) }}';
        var openhouse_status    = '{{$islive}}';
        var _token              = '{{ csrf_token() }}';
    </script>
    <script type="text/javascript" src="{{asset('js/OpenHouses.js'.'?v='.$_SERVER["REQUEST_TIME"])}}"></script>
@endsection