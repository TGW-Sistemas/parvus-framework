<?php
/**
 * @param int $prBaseValue
 * @param int $prFinalValue
 * @param int $prDecimal
 * @param null $prLabelIfZero
 * @param null $prSuffix
 * @return null|string
 */
function ruleOf3($prBaseValue = 0, $prFinalValue = 0, $prDecimal = 2, $prLabelIfZero = NULL, $prSuffix = NULL)
{

	/** PHP 8 throws TypeError on non-numeric strings ('' as 0, like PHP 7) */
	$prBaseValue = (float) $prBaseValue;
	$prFinalValue = (float) $prFinalValue;

	/** PHP 8 throws DivisionByZeroError */
	$value = $prBaseValue != 0 ? (($prFinalValue * 100) / $prBaseValue) : 0;

	if ($value <= 0 && $prLabelIfZero != NULL) {

		return $prLabelIfZero;

	}

	return number_format($value, $prDecimal, ',', '.') . $prSuffix;

}
