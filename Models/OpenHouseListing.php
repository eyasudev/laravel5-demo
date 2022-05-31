<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OpenHouseListing
 * @package App
 */
class OpenHouseListing extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'openhouse_listings';

    /**
     * @var array
     */
    protected $dates = ['deleted_at', 'submitted_time'];

    /**
     * @return mixed
     */
    public static function active_listing_query()
    {
        return OpenHouseListing::where("status", 1);
    }

    /**
     * @return mixed
     */
    public static function active_listings()
    {
        return OpenHouseListing::select('openhouse_listings.*')
            ->join('users', 'users.id', '=', 'openhouse_listings.created_by')
            ->where("openhouse_listings.status", 1);
    }

    /**
     * @return mixed
     */
    public static function get_listing_select()
    {
        return OpenHouseListing::withTrashed()->select("id", "address", "city", "state", "zip",
            "sub_area", "listing_date", 'latitude', 'longitude')->where('address', "<>", "");
    }

    /**
     * @param $lid
     * @return mixed
     */
    public static function get_register_slot_agent_list($lid)
    {
        return OpenHouseListingSlot::join('users', 'openhouse_listings_slots.registered_user_id', '=', 'users.id')
            ->where('openhouse_listings_slots.OHListingID', '=', $lid)
            ->where('openhouse_listings_slots.status', '=', 1)
            ->where('openhouse_listings_slots.registered_user_id', '!=', 0)
            ->groupBy('users.name')->pluck('users.name')->toArray();
    }

    /**
     * @param $lid
     * @return mixed
     */
    public static function OH_listing_where($lid)
    {
        return OpenHouseListing::where('id', '=', $lid);
    }

    /**
     * @param $lid
     * @return mixed
     */
    public static function check_allow_agents($lid)
    {
        return OpenHouseListing::where('id', '=', $lid)->where('allow_other_agents', '=', 1)->count();
    }

    /**
     * @return mixed
     */
    public function get_active_listings()
    {
        return OpenHouseListing::where("status", 1)->orderBy("listing_date", "DESC")->get();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * @return mixed
     */
    public function slots()
    {
        return $this->hasMany('App\OpenHouseListingSlot', 'OHListingID', 'id');
    }

    /**
     * @param $query
     * @param $lid
     * @param $currentdate
     * @return mixed
     */
    public function scopeGet_register_slot_agent_emails($query, $lid, $currentdate)
    {
        return $query->select(
            'openhouse_listings_slots.id',
            'openhouse_listings_slots.slot_start',
            'openhouse_listings_slots.slot_end', 'users.email')
            ->leftJoin('openhouse_listings_slots', 'openhouse_listings_slots.OHListingID', '=', 'openhouse_listings.id')
            ->leftJoin('users', 'openhouse_listings_slots.registered_user_id', '=', 'users.id')
            ->where('openhouse_listings.id', '=', $lid)
            ->where(\DB::raw('DATE_FORMAT(openhouse_listings_slots.slot_end, "%Y-%m-%d %H:%i:%s")'), '>=', $currentdate)
            ->where('openhouse_listings_slots.deleted_at', '=', null)
            ->where('openhouse_listings_slots.registered_user_id', '<>', 0)
            ->where('openhouse_listings_slots.status', '=', '1')
            ->get();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGetagent($query)
    {
        return $query->leftjoin('users', 'openhouse_listings.listing_agent_email', 'users.email');
    }

    /**
     * @return mixed
     */
    public function scopeListingfeedback()
    {
        return $this->hasMany('App\OpenhouseSlotFeedback', 'listing_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeListingAgentsemail($query)
    {
        return $query->where('status', '=', 1)->groupBy('listing_agent_email')->pluck('listing_agent_email')->toArray();
    }

    /**
     * @param $query
     * @param $id
     * @param $currentdate
     * @return mixed
     */
    public function scopeGet_slots_information_for_cancel_emails($query, $id, $currentdate)
    {
        return $query->select(
            'openhouse_listings.listing_agent_email',
            'openhouse_listings.address',
            'openhouse_listings_slots.slot_start',
            'openhouse_listings_slots.slot_end', 'users.email')
            ->leftJoin('openhouse_listings_slots', 'openhouse_listings_slots.OHListingID', '=', 'openhouse_listings.id')
            ->leftJoin('users', 'openhouse_listings_slots.registered_user_id', '=', 'users.id')
            ->where('openhouse_listings.id', '=', $id)
            ->where(\DB::raw('DATE_FORMAT(openhouse_listings_slots.slot_end, "%Y-%m-%d %H:%i:%s")'), '>=', $currentdate)
            ->where('openhouse_listings_slots.registered_user_id', '<>', 0)
            ->where('openhouse_listings_slots.status', '=', '1')
            ->where('openhouse_listings_slots.deleted_at', '=', null)
            ->where('openhouse_listings.deleted_at', '<>', null)
            ->withTrashed()
            ->get();
    }

    /**
     * @return mixed
     */
    public function fs_slot()
    {
        return $this->hasMany(\App\FsScheduleSlots::class, 'listing_id', 'id');
    }

    /**
     * @return mixed
     */
    public function agent()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }

    /**
     * @return mixed
     */
    public function tc()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to', 'id');
    }

    /**
     * @return mixed
     */
    public function stager()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to_stager', 'id');
    }

    /**
     * @return mixed
     */
    public function log()
    {
        return $this->hasMany(\App\Modules\Fieldservices\Models\FsListingsLogs::class, 'listing_id', 'id');
    }

    /**
     * @return mixed
     */
    public function signuploads()
    {
        $instance = $this->hasMany(\App\Modules\Fieldservices\Models\FsListingsRolePhotos::class, 'listing_id', 'id');
        $instance->where('role', '=', 'Signs');

        return $instance;
    }

    /**
     * @return mixed
     */
    public function stageruploads()
    {
        $instance = $this->hasMany(\App\Modules\Fieldservices\Models\FsListingsRoleReports::class, 'listing_id', 'id');
        $instance->where('role', '=', 'Stager');

        return $instance;
    }

    /**
     * @return mixed
     */
    public function appraiseruploads()
    {
        $instance = $this->hasMany(\App\Modules\Fieldservices\Models\FsListingsRoleReports::class, 'listing_id', 'id');
        $instance->where('role', '=', 'Appraiser');

        return $instance;
    }

    /**
     * @return mixed
     */
    public function moveruploads()
    {
        $instance = $this->hasMany(\App\Modules\Fieldservices\Models\FsListingsRolePhotos::class, 'listing_id', 'id');
        $instance->where('role', '=', 'Mover');

        return $instance;
    }

    /**
     * @return mixed
     */
    public function cleaneruploads()
    {
        $instance = $this->hasMany(\App\Modules\Fieldservices\Models\FsListingsRolePhotos::class, 'listing_id', 'id');
        $instance->where('role', '=', 'Cleaner');

        return $instance;
    }

    /**
     * @return mixed
     */
    public function photouploads()
    {
        $instance = $this->hasMany(\App\Modules\Fieldservices\Models\FsListingsRolePhotos::class, 'listing_id', 'id');
        $instance->where('role', '=', 'Photographer');

        return $instance;
    }
}
