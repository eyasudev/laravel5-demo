<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AjaxCrud
 * @package App
 */
class AjaxCrud extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'image', 'email', 'address1', 'address2', 'phone'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userProfile()
    {
        return $this->hasOne('App\UserProfile' , 'user_id');
    }
}