<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfile
 * @package App
 */
class UserProfile extends Model
{
    protected $table = 'user_profile';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'image', 'email', 'address1', 'address2', 'phone'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\AjaxCrud','user_id');
    }
}