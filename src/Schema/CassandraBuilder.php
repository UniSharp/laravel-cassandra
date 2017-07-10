<?php
namespace Unisharp\Cassandra\Schema\Grammars;

use Illuminate\Database\Schema\Builder;

class CassandraBuilder extends Builder
{

    public function hasTable($table)
    {
        $table = $this->connection->getTablePrefix().$table;
        return $this->connection->select(
            $this->grammar->compileTableExists(),
            [$this->connection->getDatabaseName(), $table]
        )->count();
    }
}
