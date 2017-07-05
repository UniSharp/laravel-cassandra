<?php
namespace Unisharp\Cassandra;

use Illuminate\Database\ConnectionInterface;
use Closure;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\QueryException;

class Connection extends \Illuminate\Database\Connection implements ConnectionInterface
{
    protected $keyspace;

    /**
     * It use to communicate with cassandra
     */
    protected $session;

    protected $config;
    protected $tablePrefix;
    protected $queryGrammar;
    protected $postProcessor;

    public function __construct($cluster, $keyspace = '', $tablePrefix = '', array $config = [])
    {
        $this->keyspace = $keyspace;
        $this->tablePrefix = $tablePrefix;
        $this->config = $config;
        $this->session = $cluster->connect($keyspace);

        $this->useDefaultQueryGrammar();

        $this->useDefaultPostProcessor();
    }

    public function getQueryGrammar()
    {
        return $this->queryGrammar;
    }

    public function setQueryGrammar(Grammar $grammar)
    {
        $this->queryGrammar = $grammar;
        return $this;
    }

    public function getPostProcessor()
    {
        return $this->postProcessor;
    }

    public function setPostProcessor(Processor $processor)
    {
        $this->postProcessor = $processor;
        return $this;
    }

    public function useDefaultQueryGrammar()
    {
        $this->queryGrammar = $this->getDefaultQueryGrammar();
    }

    public function getDefaultQueryGrammar()
    {
        $this->queryGrammar = new \Unisharp\Cassandra\Query\Grammars\Grammar();
    }

    public function useDefaultPostProcessor()
    {
        $this->postProcessor = $this->getDefaultPostProcessor();
    }

    protected function getDefaultPostProcessor()
    {
        return new \Unisharp\Cassandra\Query\Processors\Processor();
    }
}
