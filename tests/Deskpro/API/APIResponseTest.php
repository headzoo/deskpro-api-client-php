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
use Deskpro\API\APIResponse;
use Deskpro\API\APIResponseInterface;

/**
 * @coversDefaultClass \DeskPRO\API\APIResponse
 */
class APIResponseTest extends TestCase
{
    /**
     * @var APIResponseInterface
     */
    public $fixture;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var array
     */
    public $meta = [];

    /**
     * @var array
     */
    public $linked = [];

    /**
     * Run before each test
     */
    public function setUp()
    {
        $this->data   = ['test data 1', 'test data 2'];
        $this->meta   = ['test meta'];
        $this->linked = ['test linked'];
        $this->fixture = new APIResponse($this->data, $this->meta, $this->linked);
    }
    
    /**
     * @covers ::getData
     * @covers ::getMeta
     * @covers ::getLinked
     */
    public function testDataMetaLinked()
    {
        $this->assertEquals($this->fixture->getData(), $this->data);
        $this->assertEquals($this->fixture->getMeta(), $this->meta);
        $this->assertEquals($this->fixture->getLinked(), $this->linked);
    }

    /**
     * @covers ::count
     */
    public function testCount()
    {
        $this->assertEquals(count($this->fixture), count($this->data));
    }

    /**
     * @covers ::offsetSet
     * @covers ::offsetExists
     * @covers ::offsetUnset
     * @covers ::offsetGet
     */
    public function testArrayAccess()
    {
        $this->fixture[] = 'test data 3';
        $this->assertEquals($this->fixture[0], $this->data[0]);
        $this->assertEquals($this->fixture[1], $this->data[1]);
        $this->assertEquals($this->fixture[2], 'test data 3');
        $this->assertTrue(isset($this->fixture[0]));
        $this->assertTrue(isset($this->fixture[1]));
        $this->assertTrue(isset($this->fixture[2]));
        $this->assertFalse(isset($this->fixture[3]));
        
        unset($this->fixture[2]);
        $this->assertFalse(isset($this->fixture[2]));
    }

    /**
     * @covers ::rewind
     * @covers ::current
     * @covers ::key
     * @covers ::next
     * @covers ::valid
     */
    public function testIterator()
    {
        $values = [];
        foreach($this->fixture as $value) {
            $values[] = $value;
        }
        
        $this->assertEquals($values, $this->data);
        
        reset($this->fixture);
        $values = [];
        foreach($this->fixture as $value) {
            $values[] = $value;
        }

        $this->assertEquals($values, $this->data);
    }
}