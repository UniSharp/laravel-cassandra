<?php
/**
 * Created by PhpStorm.
 * User: FreedomKnight
 * Date: 2017/7/10
 * Time: 下午1:01
 */

namespace Unisharp\Cassandra\Schema\Grammars;


class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    public function primary($columns, $name = null, $algorithm = null)
    {
        return $this->addColumn('primary', $columns, $name, $algorithm);
    }

}
