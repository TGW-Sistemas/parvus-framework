<?php

namespace Parvus\Database;

use Illuminate\Contracts\Database\Query\Expression;

/**
 * No 4.2, o operador podia ser uma expressão (DB::raw('='), DB::raw('IN')) e era usado literalmente.
 * No 13, um operador que não é string é tratado como valor ("col = =" / "col = IN").
 */
trait LegacyOperators
{

    /**
     * Converte o operador DB::raw(...) para string
     * @param $operator
     * @return mixed
     */
    protected function legacyOperator($operator)
    {

        return $operator instanceof Expression ? (string) $operator->getValue($this->getGrammar()) : $operator;

    }

    /**
     * Aceita também "in" e "not in" como operador (ex.: DB::raw('IN'), DB::raw('(1,2)'))
     * @param $operator
     * @return bool
     */
    protected function invalidOperator($operator)
    {

        if (is_string($operator) && in_array(mb_strtolower(trim($operator), 'UTF-8'), array('in', 'not in'), true)) {

            return false;

        }

        return parent::invalidOperator($operator);

    }

    /**
     * Mantém a quantidade de argumentos: where('col', $valor) tem outro significado no Illuminate
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {

        $args = func_get_args();

        if (count($args) >= 3) {

            $args[1] = $this->legacyOperator($operator);

        }

        return parent::where(...$args);

    }

    /**
     * Usado também pelo ->on() dos joins
     * @return $this
     */
    public function whereColumn($first, $operator = null, $second = null, $boolean = 'and')
    {

        $args = func_get_args();

        if (count($args) >= 3) {

            $args[1] = $this->legacyOperator($operator);

        }

        return parent::whereColumn(...$args);

    }

}
