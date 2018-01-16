<?php
namespace seongbae\SiteCrawler\Models;

use Illuminate\Database\Eloquent\Model;

class Metafield extends Model
{
    protected $fillable = [
        'keyword',
        'url',
        'user_id',
        'website_id'
    ];

    public function website() {
        return $this->belongsTo('seongbae\KeywordRank\Models\Website');
    }

    public function rankings() {
        return $this->hasMany('seongbae\KeywordRank\Models\Ranking');
    }
}