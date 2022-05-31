<?php

namespace App\Modules\Events\Http\Controllers;

use App\EventsLog;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

/**
 * Class EventsController
 * @package App\Modules\Events\Http\Controllers
 */
class EventsController extends Controller
{
    /**
     * EventsController constructor.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $filters = [];
        $events_types = DB::table("events_log")
            ->select(DB::RAW("count(events_log.id) as event_count"),"event")
            ->join('users', 'users.id', '=', 'events_log.user_id')
            ->join('role_user','role_user.user_id','events_log.user_id')
            ->join('roles','roles.id','role_user.role_id');

        if (isset($request->users_filter) && count($request->users_filter)) {
            $events_types->whereIn("events_log.user_id", $request->users_filter);
            $filters["users_filter"] = $request->users_filter;
        }

        if (isset($request->search_keyword) && count($request->search_keyword)) {
            $events_types->where("details",'LIKE', '%'.$request->search_keyword.'%');
            $filters["search_keyword"] = $request->search_keyword;
        }

        if (isset($request->date_range) && count($request->date_range)) {
            $date_range = explode('-', $request->date_range);
            $date_range = [date("Y-m-d", strtotime(trim($date_range[0]))), date("Y-m-d", strtotime(trim($date_range[1])))];
            $events_types->whereBetween("events_log.created_at", $date_range);
            $filters["date_range"] = $request->date_range;
        }

        if (isset($request->roles_filter) && count($request->roles_filter)) {
            $events_types->whereIn("roles.name", $request->roles_filter);
            $filters["roles_filter"] = $request->roles_filter;
        }

        $events_types->groupBy("event");
        $events_types           = $events_types->pluck('event_count', 'event')->toArray();
        $organized_event_array  = [];

        foreach ($events_types as $event => $count) {
            $data = EventsLog::getevents();
            $data->take(5);
            $data->where("event", $event);

            if (isset($request->users_filter) && count($request->users_filter)) {
                $data->whereIn("events_log.user_id", $request->users_filter);
            }

            if (isset($request->search_keyword) && count($request->search_keyword)) {
                $data->where("details", 'LIKE', '%'.$request->search_keyword.'%');
            }

            if (isset($request->date_range) && count($request->date_range)) {
                $date_range = explode('-', $request->date_range);
                $date_range = [date("Y-m-d", strtotime(trim($date_range[0]))), date("Y-m-d", strtotime(trim($date_range[1])))];
                $data->whereBetween("events_log.created_at", $date_range);
            }

            if (isset($request->roles_filter) && count($request->roles_filter)) {
                $data->whereIn("roles.name", $request->roles_filter);
            }

            $data->orderBy("id", "DESC");
            $data->groupBy("events_log.id");
            $data                                   = $data->get();
            $organized_event_array[$event]          = $data;
            $organized_event_array[$event]["count"] = $count;
        }

        $simple_events = ['Logout', 'Login'];
        $pages  = [
            'Openhouses'                        => 'Open Houses - Listings',
            'Openhouseslisting Slot'            => 'Open Houses - Listing Slots',
            'Pending Feedback'                  => 'Open Houses - Pending Feedback',
            'MyFeedback'                        => 'Open Houses - My Feedback',
            'Field Services'                    => 'Open Houses - Field Services',
            'Openhouseslisting Advertisement'   => 'Open Houses - Marketing & Audits',
            'FileImports'                       => 'Reports - Excel Import',
            'ADRreports'                        => 'Reports - ADR Report',
            'otherreports'                      => 'Reports - Other Report',
            'mls'                               => 'Settings - MLS',
            'mlsproviders'                      => 'Settings - MLS Providers',
            'Users'                             => 'Settings - User Management',
            'config'                            => 'Settings - Config',
            'events'                            => 'Settings - Events',
            'sitesettings'                      => 'Settings - Site Settings',
            'cronjobs'                          => 'Settings - Cron Jobs',
            'AgentTier'                         => 'Agent - Tier Change'
        ];

        $users = EventsLog::getusers();
        $roles = EventsLog::getRoles();

        return view("events::event_logs",[
            'organized_event_array' => $organized_event_array,
            'simple_events'         => $simple_events,
            'pages'                 => $pages,
            'users'                 => $users,
            'roles'                 => $roles,
            'filters'               => $filters
        ]);

    }

    /**
     * @param $event_id
     * @return mixed
     */
    public function get_spec_event($event_id)
    {
        $event          = EventsLog::getevents()->where("events_log.id", $event_id)->first();
        $json_detail    = json_decode($event->details, true);

        return view("events::view_spec_event", [
            'event'         => $event,
            'json_detail'   => $json_detail
        ]);
    }

    /**
     * @param $category
     * @return mixed
     */
    public function get_spec_event_cat($category)
    {
        $events = EventsLog::getevents()
            ->take(1000)
            ->where("event", $category)
            ->orderBy("events_log.id", "DESC")
            ->groupBy("events_log.id")
            ->get();

        return view("events::view_spec_event_cat", [
            'events' => $events
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function get_events_async(Request $request)
    {
        $events = EventsLog::getevents()->take(100)->orderBy("id", "DESC")->groupBy("events_log.id")->get();
        $status = [
            'Delete'                        => 'alert-danger',
            'Open House Cancellation'       => 'alert-danger',
            'Open House Sign-up is Closed'  => 'alert-danger',
            'Open House Sign-up is Open'    => 'alert-primary',
            'removed user from slot'        => 'alert-danger',
            'Role Updated'                  => 'alert-warning',
            'unregistered a slot'           => 'alert-warning',
            'Update'                        => 'alert-warning',
            'Updated'                       => 'alert-warning',
        ];

        return view("events::events_chunks", [
            'events' => $events,
            'status' => $status
        ]);

    }

    /**
     *  This function use to display events log based on the filters apply by user.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function display_events(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            // add column names you want to filter
            $aColumns   = ['events_log.id', 'users.name', 'events_log.page', "events_log.details"];
            $sOrderType = "";

            if ($request->has('iSortCol_0')) {
                $sOrderColumn   = "";
                $sOrderType     = "";
                $iSortingCols = intval($request->input('iSortingCols'));

                for ($i = 0; $i < $iSortingCols; $i++) {
                    if ($request->input('bSortable_' . intval($request->input('iSortCol_' . $i))) == "true") {
                        $sOrderColumn   = $aColumns[intval($request->input('iSortCol_' . $i))];
                        $sOrderType     = $request->input('sSortDir_' . $i) === 'asc' ? 'asc' : 'desc';
                    }
                }

                if ($sOrderColumn == "") {
                    $sOrderType = "";
                }
            }
            $iDisplayStart  = $request->input('iDisplayStart', -1);
            $iDisplayLength = $request->input('iDisplayLength', -1);

            if ($request->get('byorder')) {
                if ($request->get('byorder') != 'All') {
                    $sOrderType = $request->get('byorder');
                }
            }
            // get events
            $events = EventsLog::getevents()->remove_old_events()->orderBy('events_log.id', $sOrderType);
            // Add filters in where clause.
            if ($request->has('sSearch') && $request->input('sSearch') != "") {
                $counter = count($aColumns);
                $events->where(function ($sql) use ($aColumns, $i, $request, $counter) {

                    for ($i = 0; $i < $counter; $i++) {
                        $sql->orWhere($aColumns[$i], 'LIKE', "%" . $request->input('sSearch') . "%");
                    }
                });
            }

            if ($request->get('page') && $request->get('page') != 'All') {
                $pages  = explode(',', $request->get('page'));
                $events = $events->whereIn('events_log.page', $pages);
            }

            if ($request->get('user') && $request->get('user') != 'All') {
                $users  = explode(',', $request->get('user'));
                $events = $events->whereIn('users.name', $users);
            }

            if ($request->get('event') && $request->get('event') != 'All') {
                $event  = explode(',', $request->get('event'));
                $events = $events->whereIn('events_log.event', $event);
            }

            if ($request->get('role') && $request->get('role') != 'All') {
                $role   = explode(',', $request->get('role'));
                $events = $events->whereIn('roles.name', $role);
            }

            if ($request->get('startdate') != '' && $request->get('enddate') != '') {
                $events = $events->whereDate('events_log.created_at', '>=', $request->get('startdate'));
                $events = $events->whereDate('events_log.created_at', '<=', $request->get('enddate'));
            }

            $output = [
                "iTotalRecords"         => $iDisplayLength,
                "iTotalDisplayRecords"  => $events->groupBy('events_log.id',
                                            'users.name', 'events_log.page',
                                            'events_log.user_ip',
                                            "events_log.event",
                                            "events_log.details",
                                            "events_log.created_at")
                                            ->get()
                                            ->count(),
                "aaData"                => []
            ];

            if ($iDisplayStart != "-1" && $iDisplayLength != '-1') {
                $sOffcet    = intval($iDisplayStart);
                $sLimit     = intval($iDisplayLength);
                $events->take($sLimit);
                $events->skip($sOffcet);
            }

            $events = $events->groupBy('events_log.id')
                ->orderBy("events_log.id", "DESC")
                ->get();

            foreach ($events as $result) {
                $rowValues[0]       = $result->id;
                $rowValues[1]       = $this->date_wrapper($result->created_at);
                $rowValues[4]       = $result->name . '(' . $result->user_ip . ')';
                $rowValues[2]       = $result->page;
                $view               = \View::make('events::event_part')->with(['d' => $result]);
                $contents           = $view->render();
                $rowValues[3]       = $contents;
                $rowValues[5]       = "";
                $output['aaData'][] = $rowValues;
            }

            return response()->json($output);
        }
    }

    private function date_wrapper($date)
    {
        return '<div class="alert alert-info no-border">
                    '.date('m/d/Y h:i:s A', strtotime($date)).'
                </div>';
    }
}
