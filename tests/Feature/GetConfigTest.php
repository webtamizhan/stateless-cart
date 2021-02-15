<?php


namespace Webtamizhan\StatelessCart\Tests\Feature;

use Webtamizhan\StatelessCart\Tests\TestCase;

class GetConfigTest extends TestCase
{
    /**
     * @test
     */
    public function getDatabaseConfiguration()
    {
        $config = config('stateless-cart.database.connection');

        $this->assertEquals(null, $config);
    }

    /**
     * @test
     */
    public function getDatabaseTableName()
    {
        $config = config('stateless-cart.database.table');

        ray($config);
        $this->assertNotEmpty($config);
    }
}
