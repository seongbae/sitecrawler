<?php

namespace seongbae\SiteCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'url',
        'user_id'
    ];

    public function keywords() 
    {
        return $this->hasMany('seongbae\SiteCrawler\Models\Page');
    }
}
