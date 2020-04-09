<?php

declare(strict_types = 1);

namespace Test;

use PHPUnit\Framework\TestCase;
use Search\PriceSearch;

class PriceSearchTest extends TestCase
{
    private $params = [
        'pe_category' => 3,
        'fregto' => 2019,
        'fregfrom' => 2019,
    ];

    public function testIfCanGetAveragePrice()
    {
        $priceSearch = new PriceSearch('bmw', '320', 20, $this->params, $async=false);
        $averagePrice = $priceSearch->getAveragePrice(false);

        echo "\nAverage Price: " . number_format($averagePrice, 2) . "\n";

        $this->assertTrue($averagePrice > 10.000);
    }
}
