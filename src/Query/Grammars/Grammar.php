<?php
namespace Unisharp\Cassandra\Query\Grammars;

use \Illuminate\Database\Query\Grammars\Grammar as BaseGrammar;
use Unisharp\Cassandra\Query\Builder;

class Grammar extends BaseGrammar
{
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'unions',
        'lock',
        'allows'
    ];

    public function compileAllows(Builder $builder, array $allows)
    {
        if (count($allows) > 0) {
            return 'allow '. implode(' ', $allows);
        }

        return '';
    }
}
