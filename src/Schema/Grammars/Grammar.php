<?php
namespace Unisharp\Cassandra\Schema\Grammars;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Schema\Grammars\Grammar as BaseGrammar;
use Illuminate\Support\Fluent;

class Grammar extends BaseGrammar
{
    protected $modifiers = [
        'VirtualAs', 'StoredAs', 'Unsigned', 'Charset', 'Collate', 'Nullable',
        'Default', 'Increment', 'Comment', 'After', 'First', 'Primary',
    ];

    public function compileTableExists()
    {
        return "select columnfamily_name from system.schema_columnfamilies where keyspace_name=? and columnfamily_name=?";
    }

    public function compileCreate(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        $sql = $this->compileCreateTable(
            $blueprint,
            $command,
            $connection
        );

        $sql = $this->compileCreateEncoding(
            $sql,
            $connection,
            $blueprint
        );

        return $this->compileCreateEngine(
            $sql, $connection, $blueprint
        );
    }

    protected function compileCreateTable($blueprint, $command, $connection)
    {
        return sprintf('%s table %s (%s)',
            $blueprint->temporary ? 'create temporary' : 'create',
            $this->wrapTable($blueprint),
            implode(', ', $this->getColumns($blueprint))
        );
    }

    protected function compileCreateEncoding($sql, Connection $connection, Blueprint $blueprint)
    {
        // First we will set the character set if one has been set on either the create
        // blueprint itself or on the root configuration for the connection that the
        // table is being created on. We will add these to the create table query.
        if (isset($blueprint->charset)) {
            $sql .= ' default character set '.$blueprint->charset;
        } elseif (! is_null($charset = $connection->getConfig('charset'))) {
            $sql .= ' default character set '.$charset;
        }

        // Next we will add the collation to the create table statement if one has been
        // added to either this create table blueprint or the configuration for this
        // connection that the query is targeting. We'll add it to this SQL query.
        if (isset($blueprint->collation)) {
            $sql .= ' collate '.$blueprint->collation;
        } elseif (! is_null($collation = $connection->getConfig('collation'))) {
            $sql .= ' collate '.$collation;
        }

        return $sql;
    }

    protected function compileCreateEngine($sql, Connection $connection, Blueprint $blueprint)
    {
        if (isset($blueprint->engine)) {
            return $sql.' engine = '.$blueprint->engine;
        } elseif (! is_null($engine = $connection->getConfig('engine'))) {
            return $sql.' engine = '.$engine;
        }

        return $sql;
    }

    protected function typeInteger()
    {
        return 'int';
    }

    protected function typeString()
    {
        return 'varchar';
    }

    protected function modifyIncrement(Blueprint $blueprint, Fluent $column)
    {
        if ($column->autoIncrement) {
            return sprintf(' , primary key (%s)', $column->name);
        }

        return '';
    }

    protected function modifyPrimary(Blueprint $blueprint, Fluent $column)
    {
        if ($column->primary && !$column->autoIncrement) {
            return sprintf(' , primary key (%s)', $column->name);
        }

        return '';
    }

    protected function typeTimestamp()
    {
        return 'timestamp';
    }

    protected function typeUuid()
    {
        return 'uuid';
    }
}
