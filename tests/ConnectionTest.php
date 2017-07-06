<?php

namespace Tests;

use Illuminate\Database\Capsule\Manager;
use PHPUnit\Framework\TestCase;
use Unisharp\Cassandra\Connection;
use Mockery;
use Cassandra\Cluster;
use Cassandra\Session;
use Cassandra\PrepareStetement;

class ConnectionTest extends TestCase
{
    public $session;
    public $connection;
    public function setUp()
    {
        $cluster = Mockery::mock(Cluster::class);
        $cluster->shouldReceive('connect')->andReturn($this->session = Mockery::mock(Session::class));
        $this->connection =  new Connection($cluster);
    }

    public function testSelect()
    {
        $this->assertEquals('select * from "user"', $this->connection->table('user')->toSql());
    }

    public function testAllowFiltering()
    {
        $this->assertEquals(
            'select * from "user" allow filtering',
            $this->connection->table('user')->allowFiltering()->toSql()
        );
    }

    public function testInsert()
    {
        $this->session->shouldReceive('prepare')->andReturn(Mockery::mock(PrepareStetement::class));
        $this->session->shouldReceive('execute')->andReturn(true);
        $this->assertTrue(
            $this->connection->table('user')->insert(['id' => 'abc'])
        );
    }
}
