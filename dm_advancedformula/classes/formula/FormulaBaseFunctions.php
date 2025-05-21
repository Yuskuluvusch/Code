<?php
/**
 * 2007-2012 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    DMConcept <support@dmconcept.fr>
 * @copyright 2015 DMConcept
 * @license   http://opensource.org/licenses/afl-3.0.phpAcademic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!class_exists('FormulaBaseFunctions')) {
    /**
     * Classe abstraite définissant les méthodes de calcul
     * définies dans le FormulaBuilder
     */
    abstract class FormulaBaseFunctions
    {
        const FORCE_UP  = 1;
        const HALF_UP   = 2;

        protected $errors = array();

        abstract public function getOptionValue($id_step, $id_option);
        abstract public function getFirstSelectedPositionOption($id_step);
        abstract public function getFirstSelectedValueOption($id_step);
        abstract public function getStepPrice($id_step);
        abstract public function getSumFeature($id_step, $id_feature);
        abstract public function getSumQty($id_step);
        abstract  public function getOptionQty($from_id_step, $id);
        abstract public function getProductProperty($id_product, $property);
        abstract public function getMaxValue($from_id_step, $id);
        abstract public function getMaxValueOfSelectedOption($id_step);

        abstract public function getBasePrice();

        public function getAverage()
        {
            $args = func_get_args();
            $total = count($args);
            return array_sum($args) / $total;
        }

        public function isBetween($value, $min, $max)
        {
            return (int)($value > $min && $value <= $max);
        }

        public function getRoundHalfUp($value, $precision = 0)
        {
            return $this->getRound($value, $precision, FormulaBaseFunctions::HALF_UP);
        }

        public function getRoundUp($value, $precision = 0)
        {
            return $this->getRound($value, $precision, FormulaBaseFunctions::FORCE_UP);
        }

        public function getRound($value, $precision = 0, $mode = null)
        {
            switch ($mode) {
                case FormulaBaseFunctions::HALF_UP:
                    return (float) round($value, $precision);
                default:
                    return (float)ceil($value); // FORCE_UP
            }
        }

        public function getPair($value)
        {
            if ((int)$value % 2 === 0) {
                return (int) $value;
            } elseif ($value > 0) {
                return ((int)$value + 1);
            } else {
                return ((int)$value - 1);
            }
        }

        public function getConcat()
        {
            $args = func_get_args();
            $return = '';
            foreach ($args as $arg) {
                $return .= (string)$arg;
            }
            return $return;
        }

        public function getErrors()
        {
            return $this->errors;
        }
    }
}
