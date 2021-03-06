<?php
namespace Unisharp\Cassandra;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Unisharp\Cassandra\Schema\Grammars\CassandraBuilder;


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
        $this->useDefaultSchemaGrammar();
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
        return new Query\Grammars\Grammar();
    }

    public function useDefaultPostProcessor()
    {
        $this->postProcessor = $this->getDefaultPostProcessor();
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getPdoForSelect($useReadPdo = true)
    {
        return $this->getSession();
    }

    protected function getDefaultPostProcessor()
    {
        return new Query\Processors\Processor();
    }

    public function useDefaultSchemaGrammar()
    {
        $this->schemaGrammar = $this->getDefaultSchemaGrammar();
    }

    public function query()
    {
        return new Query\Builder(
            $this,
            $this->getQueryGrammar(),
            $this->getPostProcessor()
        );
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            // For select statements, we'll simply execute the query and return an array
            // of the database result set. Each element in the array will be a single
            // row from the database table, and will either be an array or objects.
            $statement = $this->getSession()->prepare($query);

            return $this->getSession()->execute($statement, $this->getExecuteOptions($bindings));
        });
    }

    public function statement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return true;
            }

            $statement = $this->getSession()->prepare($query);

            return $this->getSession()->execute($statement, $this->getExecuteOptions($bindings));
        });
    }

    public function affectingStatement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return 0;
            }


            $statement = $this->getSession()->prepare($query);

            $this->getSession()->execute($statement, $this->getExecuteOptions($bindings));

            return 1;
        });
    }

    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new CassandraBuilder($this);
    }

    public function getExecuteOptions(array $bindings)
    {
        return ['arguments' => $this->prepareBindings($bindings)];
    }

    public function getKeyspaceName()
    {
        return $this->keyspace;
    }

    public function getDatabaseName()
    {
        return $this->getKeyspaceName();
    }


    protected function reconnectIfMissingConnection()
    {
        if (is_null($this->getSession())) {
            $this->reconnect();
        }
    }

    protected function getDefaultSchemaGrammar()
    {
        return new Schema\Grammars\Grammar();
    }
}
