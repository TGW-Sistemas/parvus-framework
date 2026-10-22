<?php

namespace Parvus\Database;

/**
 * Join compatível com o comportamento do Illuminate 4.2 (operadores DB::raw no ->on() / ->where())
 */
class JoinClause extends \Illuminate\Database\Query\JoinClause
{

    use LegacyOperators;

}
