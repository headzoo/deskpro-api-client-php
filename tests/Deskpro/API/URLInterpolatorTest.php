<?php

/*
 * DeskPRO (r) has been developed by DeskPRO Ltd. https://www.deskpro.com/
 * a British company located in London, England.
 *
 * All source code and content Copyright (c) 2017, DeskPRO Ltd.
 *
 * The license agreement under which this software is released
 * can be found at https://www.deskpro.com/eula/
 *
 * By using this software, you acknowledge having read the license
 * and agree to be bound thereby.
 *
 * Please note that DeskPRO is not free software. We release the full
 * source code for our software because we trust our users to pay us for
 * the huge investment in time and energy that has gone into both creating
 * this software and supporting our customers. By providing the source code
 * we preserve our customers' ability to modify, audit and learn from our
 * work. We have been developing DeskPRO since 2001, please help us make it
 * another decade.
 *
 * Like the work you see? Think you could make it better? We are always
 * looking for great developers to join us: http://www.deskpro.com/jobs/
 *
 * ~ Thanks, Everyone at Team DeskPRO
 */

use PHPUnit\Framework\TestCase;
use Deskpro\API\URLInterpolator;
use Deskpro\API\URLInterpolatorInterface;

/**
 * @coversDefaultClass \DeskPRO\API\URLInterpolator
 */
class URLInterpolatorTest extends TestCase
{
    /**
     * @var URLInterpolatorInterface
     */
    public $fixture;

    /**
     * Run before each test
     */
    public function setUp()
    {
        $this->fixture = new URLInterpolator();
    }

    /**
     * @covers ::interpolate
     */
    public function testInterpolateNoParams()
    {
        $actual = $this->fixture->interpolate('/articles', []);
        $this->assertEquals('/articles', $actual);
    }

    /**
     * @covers ::interpolate
     */
    public function testInterpolateQueryParams()
    {
        $params = [
            'limit'  => 5,
            'offset' => 100
        ];
        $actual = $this->fixture->interpolate('/articles', $params);

        $this->assertEquals('/articles?limit=5&offset=100', $actual);
    }

    /**
     * @covers ::interpolate
     */
    public function testInterpolatePlaceholders()
    {
        $params = [
            'id' => 5,
            'parentId' => 100
        ];
        $actual = $this->fixture->interpolate('/articles/{parentId}/{id}', $params);

        $this->assertEquals('/articles/100/5', $actual);
    }

    /**
     * @covers ::interpolate
     */
    public function testInterpolateAll()
    {
        $params = [
            'id'       => 5,
            'parentId' => 101,
            'limit'    => 25,
            'offset'   => 100
        ];
        $actual = $this->fixture->interpolate('/articles/{parentId}/{id}', $params);

        $this->assertEquals('/articles/101/5?limit=25&offset=100', $actual);
    }
}