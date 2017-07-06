<?php
namespace Unisharp\Cassandra\Eloquent;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new \Unisharp\Cassandra\Query\Builder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }
}
