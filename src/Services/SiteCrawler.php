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

    public function __construct()
    {
        //$this->config = $config;
        $this->client = new Client();
        $this->client->setHeader('user-agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
    }

    public function crawl($url) 
    {
    	$parsedURL = parse_url($url);

    	if (!isset($this->host)) {
    		$this->scheme = isset($parsedURL['scheme']) ? $parsedURL['scheme'] . '://' : ''; 
    		$this->host = isset($parsedURL['host']) ? $parsedURL['host'] : ''; 

    		print('scheme='.$this->scheme."\n");
    		print('host='.$this->host."\n");
    	}

    	$path = isset($parsedURL['path']) ? $parsedURL['path'] : ''; 

    	if (preg_match('/^#/', $url)) {
    		return;
    	}

    	if (in_array($url, $this->skipUrls)) {
    		return;
    	} //else if ($url == "/" || in_array($url)

    	if (!in_array($url, $this->crawledUrls) && 
    		!in_array($url.'/', $this->crawledUrls) &&
    		!in_array(rtrim($url, '/'), $this->crawledUrls))
    	{
    		print("crawling: ".$url."\n");
	       	$uri = "";
	        $title = "";
	        $description = "";

	        //print('1');
	        $crawler = $this->client->request('GET', $url);
	        //print('2');
	        //$uri = $this->client->getRequest()->getUri();
	        //print('3');
	        $title = $crawler->filterXpath('//title')->text();
	        //print('4');
	        // try {
	        // 	$description = $crawler->filterXpath('//meta[@name="description"]')->attr('content');
	        // } catch(Exception $e) { // I guess its InvalidArgumentException in this case
			    
			//}
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
	        $page->html = Purifier::clean($crawler->html());
	        $page->status = $this->client->getResponse()->getStatus();
	        $page->save();

	        //print('description:'.$description);
	        //print("uri=".$uri."\n");
	        //print("title=".$title."\n");
	        //print("description=".$description."\n");

	        // Store information begin

	        // Store information end

	        $this->crawledUrls[] = $url;

	        $links = $crawler->filter('a')->each(function ($node) {
			            $href  = $node->attr('href');
					    $title = $node->attr('title');
					    $text  = $node->text();

					    return compact('href', 'title', 'text');
			        });

	        //print_r($links);

	        foreach ($links as $link) {
	        	$linkurl = $link['href'];
	        	//print($linkurl."\n");
	        	sleep(1);

	        	if (preg_match('/^\//',$linkurl)) // relative link found
	        		$linkurl = $this->scheme .$this->host.$linkurl;

	        	if (!in_array($linkurl, $this->crawledUrls)) {
	        		print("new link found: ".$linkurl."\n");
	        		$this->crawl($linkurl);
	        	}
	        }
	    } else {
	    	print($url. " is already crawled or skipping...\n");
	    }
       
    }

}