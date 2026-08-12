<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddToSitemap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var
     */
    public $currentUrl;
    /**
     * @var null
     */
    private $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($currentUrl, $date = null)
    {
        $this->currentUrl = $currentUrl;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = storage_path() . '/sitemap.xml';
        if ( ! file_exists($file)) {
            file_put_contents($file,
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>');
        }
        $xml = new \SimpleXMLElement(file_get_contents($file));
        $this->currentUrl = str_replace('http://', 'https://', $this->currentUrl);
        foreach ($xml->url as $url) {
            if ($url->loc == $this->currentUrl) {
                return;
            }
        }
        $child = $xml->addChild('url');
        $child->addChild('loc', htmlspecialchars($this->currentUrl));
        $time = time();
        if ($this->date) {
            $time = strtotime($this->date);
        }
        $child->addChild('lastmod', date('Y-m-dTH:i:s', $time) . '+00:00');
        $child->addChild('priority', '1.00');
        file_put_contents($file, $xml->asXML());
    }
}
