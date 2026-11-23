<?php

/*
|--------------------------------------------------------------------------
| Generic HTTP Clients & Headless Browsers
|--------------------------------------------------------------------------
|
| These are only consulted after known agents and markdown negotiation.
|
*/

return [
    ['id' => 'axios', 'name' => 'axios', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^axios/']],
    ['id' => 'curl', 'name' => 'curl', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^curl/']],
    ['id' => 'wget', 'name' => 'Wget', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Wget/']],
    ['id' => 'node', 'name' => 'Node.js', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^(node|undici|node-fetch)(/|$)']],
    ['id' => 'bun', 'name' => 'Bun', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Bun/']],
    ['id' => 'deno', 'name' => 'Deno', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Deno/']],
    ['id' => 'python-requests', 'name' => 'Python requests', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^python-requests/']],
    ['id' => 'python-httpx', 'name' => 'Python httpx', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^python-httpx/']],
    ['id' => 'python-urllib', 'name' => 'Python urllib', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Python-urllib/']],
    ['id' => 'python-aiohttp', 'name' => 'Python aiohttp', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Python/[\d.]+ aiohttp/']],
    ['id' => 'go-http', 'name' => 'Go net/http', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Go-http-client/']],
    ['id' => 'okhttp', 'name' => 'OkHttp', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^okhttp/']],
    ['id' => 'java', 'name' => 'Java', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^(Java/|Apache-HttpClient/)']],
    ['id' => 'guzzle', 'name' => 'Guzzle', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^GuzzleHttp/']],
    ['id' => 'reqwest', 'name' => 'Rust reqwest', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^reqwest/']],
    ['id' => 'lua-resty-http', 'name' => 'lua-resty-http', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^lua-resty-http/']],
    ['id' => 'colly', 'name' => 'Colly', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^colly\b']],
    ['id' => 'scrapy', 'name' => 'Scrapy', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['\bScrapy/']],
    ['id' => 'n8n', 'name' => 'n8n', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^n8n$']],
    ['id' => 'postman', 'name' => 'Postman', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^PostmanRuntime/']],
    ['id' => 'headless-chrome', 'name' => 'Headless Chrome', 'category' => 'bot', 'kind' => 'headless', 'patterns' => ['\bHeadlessChrome/']],
    ['id' => 'phantomjs', 'name' => 'PhantomJS', 'category' => 'bot', 'kind' => 'headless', 'patterns' => ['\bPhantomJS/']],
    ['id' => 'playwright', 'name' => 'Playwright', 'category' => 'bot', 'kind' => 'headless', 'patterns' => ['\bPlaywright/']],
    ['id' => 'powershell', 'name' => 'PowerShell', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['PowerShell/']],
    ['id' => 'ruby', 'name' => 'Ruby', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Ruby$']],
    ['id' => 'got', 'name' => 'got', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^got\b']],
    ['id' => 'libwww-perl', 'name' => 'libwww-perl', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['\blibwww-perl/']],
    ['id' => 'aria2', 'name' => 'aria2', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^aria2/']],
    ['id' => 'httrack', 'name' => 'HTTrack', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['\bHTTrack\b']],
    ['id' => 'crawl4ai', 'name' => 'Crawl4AI', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^crawl4ai']],
    ['id' => 'cfnetwork', 'name' => 'Apple CFNetwork', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['\bCFNetwork/']],
    ['id' => 'dalvik', 'name' => 'Android Dalvik', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['\bDalvik/']],
    ['id' => 'cloudfront', 'name' => 'Amazon CloudFront', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Amazon CloudFront$']],
    ['id' => 'vercel-edge', 'name' => 'Vercel Edge Functions', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Vercel Edge Functions$']],
    ['id' => 'google-cloud-tasks', 'name' => 'Google Cloud Scheduler and Tasks', 'category' => 'bot', 'kind' => 'http-client', 'patterns' => ['^Google-Cloud-(Scheduler|Tasks)$']],
];
