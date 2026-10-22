<?php

namespace Parvus\Database;

/**
 * Query builder compatível com o comportamento do Illuminate 4.2
 */
class QueryBuilder extends \Illuminate\Database\Query\Builder
{

    use LegacyOperators;

    /**
     * Joins também com os operadores do 4.2 (DB::raw('='), DB::raw('IN'))
     * @param \Illuminate\Database\Query\Builder $parentQuery
     * @param string $type
     * @param string $table
     * @return JoinClause
     */
    protected function newJoinClause(\Illuminate\Database\Query\Builder $parentQuery, $type, $table)
    {

        return new JoinClause($parentQuery, $type, $table);

    }

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
     * No 4.2, get() retornava um array; no 13 retorna uma Collection.
     * Mantém o array para o código da aplicação e a Collection para o próprio Illuminate
     * (Eloquent, first(), chunk(), ...), que depende dela.
     * @param array|string $columns
     * @return array|\Illuminate\Support\Collection
     */
    public function get($columns = ['*'])
    {

        $result = parent::get($columns);

        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? '';

        if (stripos(str_replace('\\', '/', $caller), '/illuminate/') !== false) {

            return $result;

        }

        return $result->all();

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
