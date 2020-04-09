PriceSearch (sync vs. async php)
====
a way to perform http requests in parallel with php

##### Search for cars based on given params (autoscout example)
    $params = [
        'pe_category' => 3, // "Faires Angebot"
        'fregto' => 2019, // "Erstzulassung ab"
        'fregfrom' => 2019, // "Erstzulassung bis"
        ...
    ];

eg. sync search:
    $priceSearch = new PriceSearch('bmw', '320', 5, $this->params, $async=false);

eg. async search:
    $priceSearch = new PriceSearch('bmw', '320', 5, $this->params, $async=true);

    
There are usually 10 cars on one page, if you need more, you can set $pages param (default=1)

##### Average price calculation     
    PriceSearch::getAveragePrice()

##### Install dependencies
    composer install

##### Run test (modify async flag in test as needed)
    vendor/bin/phpunit tests/PriceSearchTest.php
