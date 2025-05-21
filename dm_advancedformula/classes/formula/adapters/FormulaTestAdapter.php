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

if (!class_exists('FormulaTestAdapter')) {
    require_once(dirname(__DIR__).'/FormulaBaseFunctions.php');
    /**
     * Adapter permettant de tester les formules
     * en retournant des valeurs aléatoires pour certaines
     * méthodes demandant trop de contraintes que l'on ne peut
     * respecter dans le cas d'un test de validité de la formule
     */
    class FormulaTestAdapter extends FormulaBaseFunctions
    {

        public function getStepPrice($id_step) {
            unset($id_step);
            return rand(1, 100);
        }
        
        public function getSumFeature($id_step, $id_feature) {
            unset($id_step);
            unset($id_feature);
            return rand(1, 100);
        }
        
        public function getSumQty($id_step) {
            unset($id_step);
            return rand(1, 100);
        }

        public function getOptionQty($id_step, $id_option) {
            unset($id_step);
            unset($id_option);
            return rand(1, 100);
        }

        public function getProductProperty($id_product, $property){
            unset($id_product);
            unset($property);
            return rand(1,100);
        }

        public function getFirstSelectedValueOption($from_id_step) {
            unset($from_id_step);
            return rand(0, 10);
        }
        
        public function getMaxValueOfSelectedOption($from_id_step) {
            unset($from_id_step);
            return rand(0, 10);
        }

        public function getFirstSelectedPositionOption($from_id_step) {
            unset($from_id_step);
            return rand(0, 10);
        }

        public function getOptionValue($id_step, $id_option) {
            unset($id_step);
            unset($id_option);
            return rand(1, 1000);
        }
        
        public function getMaxvalue($id_step, $id_option){
            unset($id_step);
            unset($id_option);
            return rand(1, 1000);
        }

        public function getBasePrice()
        {
            return rand(1, 100);
        }

    }

}
