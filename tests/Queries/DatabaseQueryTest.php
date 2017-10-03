<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Tests;

use Carbon\Carbon;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;

/**
 * Class DatabaseQueryTest.
 */
class DatabaseQueryTest extends AbstractUnitTestCase
{
    /**
     * @var DatabaseQuery
     */
    protected $query_object;

    /**
     * Test basic methods execution.
     *
     * @return void
     */
    public function testBasicMethods()
    {
        $this->assertInstanceOf(Carbon::class, $this->query_object->getWhen());
        $this->assertStringStartsWith('query_hash', $this->query_object->getUniqueHash());

        $this->assertFalse($this->query_object->isValid()); // Must be invalid while not configured
    }

    /**
     * Test setter and getter for 'when' value.
     *
     * @return void
     */
    public function testWhenMethods()
    {
        $carbon = Carbon::now()->subHours(12);
        $this->query_object->setWhen($carbon);
        $this->assertEquals($carbon, $this->query_object->getWhen());

        $when_as_string = '2015-10-29 00:00:00';
        $this->query_object->setWhen($when_as_string);
        $this->assertEquals(Carbon::parse($when_as_string), $this->query_object->getWhen());

        $when_as_timestamp = 1507019168;
        $this->query_object->setWhen($when_as_timestamp);
        $this->assertEquals(Carbon::createFromTimestamp($when_as_timestamp), $this->query_object->getWhen());
    }

    /**
     * Test 'configure' method.
     *
     * @return void
     */
    public function testConfigureMethod()
    {
        $now = Carbon::now();

        $this->query_object->configure([
            'sql'             => 'select * from "fuck_you" where a = ? and b = ?',
            'bindings'        => ['bleat', 666],
            'duration'        => 3.14,
            'connection_name' => 'faked_connection_name',
            'datetime'        => $now,
        ]);

        $this->assertEquals(
            'select * from "fuck_you" where a = ? and b = ?',
            $this->query_object->getQueryContent()
        );
        $this->assertEquals(['bleat', 666], $this->query_object->getBindings());
        $this->assertEquals(3.14, $this->query_object->getDuration());
        $this->assertEquals('faked_connection_name', $this->query_object->getConnectionName());
        $this->assertEquals($now, $this->query_object->getWhen());

        $this->assertTrue($this->query_object->isValid());
    }

    /**
     * Test object to array conversion methods.
     */
    public function testToArrayConversation()
    {
        $now = Carbon::now();

        $this->query_object = new DatabaseQuery([
            'sql'             => 'select * from "fuck_you_dude" where a = ? and b = ?',
            'bindings'        => ['bleat', 666],
            'duration'        => 3.14,
            'connection_name' => 'faked_connection_name',
            'when'            => $now,
        ]);

        $as_array = $this->query_object->toArray();
        $this->assertEquals('select * from "fuck_you_dude" where a = ? and b = ?', $as_array['query_content']);
        $this->assertEquals('faked_connection_name', $as_array['connection_name']);
        $this->assertEquals(['bleat', 666], $as_array['bindings']);
        $this->assertEquals($now, $as_array['when']);
        $this->assertEquals(3.14, $as_array['duration']);

        foreach ($this->query_object->toPrintableArray() as $item) {
            $this->assertTrue(is_scalar($item));
        }
    }

    /**
     * Test to json string conversion.
     */
    public function testToJson()
    {
        $this->assertJson($this->query_object->toJson());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->query_object = new DatabaseQuery;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->query_object);

        parent::tearDown();
    }
}
















