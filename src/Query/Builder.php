<?php

namespace Unisharp\Cassandra\Query;

use Illuminate\Database\Query\Builder as QueryBuilder;

class Builder extends QueryBuilder
{
    public $allows = [];
    public function delete($id = null)
    {
        if (!is_null($id) && !is_null($this->from)) {
            $this->where('id', '=', $id);
        }

        return $this->connection->delete(
            $this->grammar->compileDelete($this),
            $this->getBindings()
        );
    }

    public function allowFiltering()
    {
        $this->allows[] = 'filtering';
        return $this;
    }
}
