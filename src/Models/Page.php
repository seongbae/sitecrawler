<?php
namespace seongbae\SiteCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
	protected $fillable = [
        'uri',
        'title',
        'description'
    ];

    public function website() {
        return $this->belongsTo('seongbae\SiteCrawler\Models\Website');
    }

}