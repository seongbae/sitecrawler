<?php 
namespace seongbae\SiteCrawler\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Storage;
use Goutte\Client;
use seongbae\SiteCrawler\Models\CrawledPage;
use Mews\Purifier\Purifier;

class SiteCrawler
{
	protected $client;
    protected $config;
    protected $crawledUrls = array();
    protected $skipUrls = ['/'];
    protected $scheme;
    protected $host;
    protected $originalURL;

    public function __construct($config)
    {
        $this->config = $config;
        $this->client = new Client();
        $this->client->setHeader('user-agent', $this->config['user_agent']);
    }

    public function crawl($url, $nofollow=false)
    {
    	$match = "/^(http|https):\/\//";

        if (!preg_match($match, $url))
            $url = "http://".$url;

        $this->originalURL = $url;

        $parsedURL = parse_url($url);

    	if (!isset($this->host)) {
    		$this->scheme = isset($parsedURL['scheme']) ? $parsedURL['scheme'] . '://' : ''; 
    		$this->host = isset($parsedURL['host']) ? $parsedURL['host'] : ''; 

    		print('scheme='.$this->scheme."\n");
    		print('host='.$this->host."\n");
    	}
        
        $this->crawlURL($url, $nofollow);
    }	

    private function crawlURL($url, $nofollow=false) 
    {
    	
    	$parsedURL = parse_url($url);
    	$path = isset($parsedURL['path']) ? $parsedURL['path'] : ''; 

    	// Skip on page links
    	if (preg_match('/^#/', $url)) {
    		return;
    	}

    	// Skip any blacklisted URLs
    	if (in_array($url, $this->skipUrls)) {
    		return;
    	} 

    	// if url already exists, what to do?

    	if (!in_array($url, $this->crawledUrls) && 
    		!in_array($url.'/', $this->crawledUrls) &&
    		!in_array(rtrim($url, '/'), $this->crawledUrls))
    	{
    		print("crawling: ".$url."\n");
	       	$uri = "";
	        $title = "";
	        $description = "";

	        $crawler = $this->client->request('GET', $url);
	       	$title = $crawler->filterXpath('//title')->text();
	       	$metaitems = $crawler->filter('meta')->each(function($node) {
			    return [
			        'name' => $node->attr('name'),
			        'content' => $node->attr('content'),
			    ];
			});

			foreach ($metaitems as $metaitem) {
				if ($metaitem['name'] == 'description')
				{
					$description = $metaitem['content'];
					break;
				}
			}
	        
	        $page = new CrawledPage();
	        $page->url = $url;
	        $page->scheme = $this->scheme;
	        $page->host = $this->host;
	        $page->path = isset($parsedURL['path']) ? $parsedURL['path'] : '';
	        $page->title = $title;
	        $page->description = $description;
	        $page->html = clean($crawler->html());
	        $page->status = $this->client->getResponse()->getStatus();
	        $page->save();

	        $this->crawledUrls[] = $url;

	        if (!$nofollow) {
	        	$links = $crawler->filter('a')->each(function ($node) {
				            $href  = $node->attr('href');
						    $title = $node->attr('title');
						    $text  = $node->text();

						    return compact('href', 'title', 'text');
				        });

		        foreach ($links as $link) {
		        	$linkurl = $link['href'];
		        	sleep(1);

		        	if (preg_match('/^\//',$linkurl)) // relative link found
		        		$linkurl = $this->scheme .$this->host.$linkurl;

		        	if (!in_array($linkurl, $this->crawledUrls) &&
		        		strpos($linkurl, $this->host) !== false) {
		        		print("new link found: ".$linkurl."\n");
		        		$this->crawl($linkurl, $nofollow);
		        	}
		        }
	        }
	        
	    } else {
	    	print($url. " is already crawled or skipping...\n");
	    }
       
    }

}