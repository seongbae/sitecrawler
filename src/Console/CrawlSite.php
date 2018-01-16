<?php

namespace seongbae\SiteCrawler\Console;

use Illuminate\Console\Command;
use seongbae\SiteCrawler\Services\SiteCrawler;
use Illuminate\Support\Facades\Config;

class CrawlSite extends Command 
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crawl {url} {--nofollow}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawls website and extracts meta information';

    protected $crawler;
    
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $crawler = new SiteCrawler(Config::get('sitecrawler'));
        $url = $this->argument('url');

        $match = "/^(http|https):\/\//";
        if (!preg_match($match, $url))
            $url = "http://".$url;
        
        $crawler->crawl($url, $this->option('nofollow'));
    }
}