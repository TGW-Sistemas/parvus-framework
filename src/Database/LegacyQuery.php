<?php

namespace Parvus\Database;

/**
 * Mantém o comportamento das conexões do Illuminate 4.2 (Capsule):
 * - linhas retornadas como array associativo (PDO::FETCH_ASSOC), não stdClass
 * - QueryBuilder compatível (orderBy/limit)
 */
trait LegacyQuery
{

    protected $fetchMode = \PDO::FETCH_ASSOC;

    /**
     * @return QueryBuilder
     */
    public function query()
    {

        return new QueryBuilder($this, $this->getQueryGrammar(), $this->getPostProcessor());

    }

}
