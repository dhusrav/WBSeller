<?php

namespace Dakword\WBSeller\Tests\ApiClient;

use Dakword\WBSeller\API\Endpoint\Finance;
use Dakword\WBSeller\Tests\ApiClient\TestCase;
use DateTime;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Dakword\WBSeller\API\Endpoint\Finance
 */
class FinanceTest extends TestCase
{
    private function Finance(): Finance
    {
        $this->skipIfNoKeyAPI();
        return $this->API()->Finance();
    }

    public function test_Class()
    {
        $this->assertInstanceOf(Finance::class, $this->API()->Finance());
    }

    /**
     * @covers ::ping()
     */
    public function test_ping()
    {
        $result = $this->Finance()->ping();
        $this->assertEquals('OK', $result->Status);
    }

    /**
     * @covers ::balance()
     */
    public function test_balance()
    {
        $result = $this->Finance()->balance();

        $this->assertIsObject($result);
        $this->assertObjectHasAttribute('currency', $result);
        $this->assertObjectHasAttribute('current', $result);
        $this->assertObjectHasAttribute('for_withdraw', $result);
    }

    /**
     * @covers ::salesReportsList()
     */
    public function test_salesReportsList()
    {
        $result = $this->Finance()->salesReportsList(
            new DateTime('-30 days'),
            new DateTime(),
            10
        );
        $this->assertIsArray($result);

        $this->expectException(InvalidArgumentException::class);
        $this->Finance()->salesReportsList(new DateTime('-30 days'), new DateTime(), 1001);
    }

    /**
     * @covers ::salesReportsDetailed()
     */
    public function test_salesReportsDetailed()
    {
        $result = $this->Finance()->salesReportsDetailed(
            new DateTime('-7 days'),
            new DateTime(),
            100,
            0,
            'weekly',
            ['rrdId', 'nmId', 'forPay']
        );
        $this->assertIsArray($result);

        $this->expectException(InvalidArgumentException::class);
        $this->Finance()->salesReportsDetailed(
            new DateTime('-7 days'),
            new DateTime(),
            100_001
        );
    }

    /**
     * @covers ::salesReportsDetailedById()
     */
    public function test_salesReportsDetailedById()
    {
        $list = $this->Finance()->salesReportsList(
            new DateTime('-60 days'),
            new DateTime(),
            1
        );
        if (!$list) {
            $this->markTestSkipped('No sales reports in account');
        }

        $result = $this->Finance()->salesReportsDetailedById($list[0]->reportId, 100);
        $this->assertIsArray($result);

        $this->expectException(InvalidArgumentException::class);
        $this->Finance()->salesReportsDetailedById(1, 100_001);
    }

    /**
     * @covers ::acquiringList()
     */
    public function test_acquiringList()
    {
        $result = $this->Finance()->acquiringList(
            new DateTime('-30 days'),
            new DateTime(),
            10
        );
        $this->assertIsArray($result);

        $this->expectException(InvalidArgumentException::class);
        $this->Finance()->acquiringList(new DateTime('-30 days'), new DateTime(), 1001);
    }

    /**
     * @covers ::acquiringDetailed()
     */
    public function test_acquiringDetailed()
    {
        $result = $this->Finance()->acquiringDetailed(
            new DateTime('-7 days'),
            new DateTime(),
            100
        );
        $this->assertIsArray($result);

        $this->expectException(InvalidArgumentException::class);
        $this->Finance()->acquiringDetailed(
            new DateTime('-7 days'),
            new DateTime(),
            100_001
        );
    }

    /**
     * @covers ::acquiringDetailedById()
     */
    public function test_acquiringDetailedById()
    {
        $list = $this->Finance()->acquiringList(
            new DateTime('-60 days'),
            new DateTime(),
            1
        );
        if (!$list) {
            $this->markTestSkipped('No acquiring reports in account');
        }

        $result = $this->Finance()->acquiringDetailedById($list[0]->reportId, 100);
        $this->assertIsArray($result);

        $this->expectException(InvalidArgumentException::class);
        $this->Finance()->acquiringDetailedById(1, 100_001);
    }
}
