<?php 
namespace seongbae\SiteCrawler;

use Illuminate\Support\ServiceProvider;
use seongbae\SiteCrawler\Services\SiteCrawler;
use seongbae\SiteCrawler\Console\CrawlSite;

class SiteCrawlerServiceProvider extends ServiceProvider 
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        \App::bind('crawl', function()
        {
            return new SiteCrawler;
        });

        $configPath = __DIR__ . '/../config/sitecrawler.php';
        $this->mergeConfigFrom($configPath, 'sitecrawler');
        $this->publishes([
             $configPath => config_path('sitecrawler.php')
        ], 'config');

        $this->initCommand('crawlsite', CrawlSite::class);
    }

    private function initCommand($name, $class)
    {
        $this->app->singleton("command.{$name}", function($app) use ($class) {
            return new $class($app);
        });

        $this->commands("command.{$name}");
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerHelpers();

        $this->loadMigrations();
    }

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        
    }

    protected function loadMigrations()
    {
        $migrationPath = __DIR__.'/../database/migrations';

        $this->publishes([
            $migrationPath => base_path('database/migrations'),
        ], 'migrations');

        $this->loadMigrationsFrom($migrationPath);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        // return [
  //           'keywordrank'
  //       ];
    }
}