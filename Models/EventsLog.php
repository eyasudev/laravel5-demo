<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class EventsLog
 * @package App
 */
class EventsLog extends Model
{
    /**
     * @var string
     */
    protected $table = 'events_log';

    /**
     * @param $data
     * @param bool $id
     */
    public static function tracking($data, $id = false)
    {
        if (!$id) {
            if (Auth::check()) {
                $id = Auth::user()->id;
                DB::table('events_log')->insert([
                    "user_id"       => $id,
                    "user_ip"       => \Request::ip(),
                    "page"          => $data['page'],
                    "event"         => $data['event'],
                    "details"       => $data['details'],
                    "created_at"    => date('Y-m-d H:i:s'),
                    "updated_at"    => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * this function to check what fields are updated to what and return an array of updated fields
     *
     * @param $model
     * @return array
     */
    public static function updated_values($model)
    {
        $original = $model->getOriginal();
        $update_values_array = [];

        foreach ($model->getDirty() as $key => $value) {
            if ($key != 'updated_at' && $key != 'remember_token') {
                $update_values_array[$key] = $original[$key] . ' updated to ' . $value;
            }

            if ($key == 'remember_token') {
                $update_values_array['remember_token'] = '' . ' updated to ' . $value;
            }
        }

        return $update_values_array;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGetevents($query)
    {
        return $query->select('users.name',
            'events_log.id',
            'events_log.user_ip',
            'events_log.user_id',
            'events_log.event',
            'roles.name as role',
            'events_log.details',
            'events_log.page',
            'events_log.created_at')
            ->join('users', 'users.id', '=', 'events_log.user_id')
            ->join('role_user', 'role_user.user_id', 'events_log.user_id')
            ->join('roles', 'roles.id', 'role_user.role_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGetRoles($query)
    {
        return $query->select('roles.name')
            ->join('users', 'users.id', '=', 'events_log.user_id')
            ->join('role_user', 'role_user.user_id', 'events_log.user_id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->groupBy("roles.name")
            ->pluck("roles.name")->toArray();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGetusers($query)
    {
        return $query->join('users', 'users.id', '=', 'events_log.user_id')
            ->orderBy('users.name', 'asc')
            ->distinct()
            ->get(['users.name', 'users.id']);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGetpages($query)
    {
        return $query->distinct()->get(['page']);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGetevent($query)
    {
        return $query->distinct()->remove_old_events()->get(['event']);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRemove_old_events($query)
    {
        return $query->whereNotIn('events_log.event', [
            'Open House Sign-up is Open!',
            'Open House Sign-up is Closed!',
            'Open House Sign-up is Closed',
            'Open House Sign-up is Open',
            'Slot Reassigned Notification',
            'Slot Booked Notification'
        ]);
    }
}
