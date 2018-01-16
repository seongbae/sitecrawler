<?php

namespace seongbae\SiteCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class CrawledPage extends Model
{
    protected $fillable = [
        'scheme',
        'host',
        'path',
        'title',
        'description',
        'html',
        'status'
    ];

    public function website() {
        return $this->belongsTo('seongbae\KeywordRank\Models\Website');
    }

    public function rankings() {
        return $this->hasMany('seongbae\KeywordRank\Models\Ranking');
    }
}
