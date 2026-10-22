<?php

namespace Parvus\Database;

/**
 * Query builder compatível com o comportamento do Illuminate 4.2
 */
class QueryBuilder extends \Illuminate\Database\Query\Builder
{

    /**
     * No 4.2, qualquer direção diferente de "asc" (inclusive NULL ou '') virava "desc".
     * No 13, uma direção inválida lança InvalidArgumentException.
     * @param $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {

        if (!$direction instanceof \UnitEnum) {

            $direction = mb_strtolower((string) $direction, 'UTF-8') == 'asc' ? 'asc' : 'desc';

        }

        return parent::orderBy($column, $direction);

    }

    /**
     * No 4.2, o limite só era aplicado quando maior que zero.
     * No 13, limit(0) ou limit('') gera "LIMIT 0" e não retorna registros.
     * @param $value
     * @return $this
     */
    public function limit($value)
    {

        if (!is_numeric($value) || $value <= 0) {

            return $this;

        }

        return parent::limit($value);

    }

}
