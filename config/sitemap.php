<?php

return [

    /*
     * These profiles determine whether a url should be crawled of not.
     */
    'crawler_profile' => Spatie\Sitemap\CrawlerProfile::class,

    /*
     * Here you can specify which URLs should be excluded from the sitemap.
     * You can use regex patterns here.
     */
    'ignore_crawling' => [
        // '#^/admin#',
        // '#^/login#',
    ],

    /*
     * The sitemap generator can execute JavaScript on pages to discover
     * links that are dynamically added. This requires Puppeteer.
     */
    'execute_javascript' => false,

    /*
     * The path to the nitro binary (used for executing JavaScript).
     */
    'node_binary' => env('NODE_BINARY', '/usr/local/bin/node'),

    /*
     * The path to the path to the crawl script. If left null, a default script
     * provided by the package will be used.
     */
    'crawl_script' => null,

    /*
     * The number of concurrent requests executed by the crawler.
     */
    'concurrency' => 10,

    /*
     * The multi-wallet constraints or choices for specific page tags.
     */
    'tags' => [
        'id' => null,
        'change_frequency' => \Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_DAILY,
        'priority' => 0.8,
    ],

];
