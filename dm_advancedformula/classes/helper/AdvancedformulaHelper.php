<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
require_once(dirname(__FILE__).'/../formula/adapters/FormulaStepAdapter.php');

require_once(dirname(__FILE__) . '/../formula/adapters/FormulaStepAdapter.php');
require_once(dirname(__FILE__) . '/../formula/FormulaBuilder.php');

class AdvancedformulaHelper extends Helper
{

    /**
	 * Retourne le prix d'une étape par rapport à la formule
	 * @param ConfiguratorStepAbstract $step
	 * @param array $detail
	 */
	public static function getPriceFormula($cart_detail_model, ConfiguratorStepAbstract $configurator_step, array $detail)
	{
		$formula = $configurator_step->formula;

		$price = (float) self::loadFormula($cart_detail_model, $configurator_step, $detail, $formula);

		return $price;
	}
	
	
	/**
	 * Retourne la surface d'une étape par rapport à la formule
	 * @param ConfiguratorStepAbstract $step
	 * @param array $detail
	 */
	public static function getSurfaceFormula($cart_detail_model, ConfiguratorStepAbstract $step, array $detail)
	{
		$formula = $step->formula_surface;

		$surface = (float) self::loadFormula($cart_detail_model, $step, $detail, $formula);

		return $surface;
	}
        
	/**
	 * Retourne le prix d'une option par rapport à la formule
	 * @param ConfiguratorStepAbstract $step
	 * @param array $detail
	 * @return int
	 */
	public static function getOptionPriceFormula($cart_detail_model, ConfiguratorStepAbstract $step, array $detail, $formula)
	{
		$price = (float) self::loadFormula($cart_detail_model, $step, $detail, $formula);

		return $price;
	}
	
	/**
	 * Retourne le résultat d'une valeur par défaut étape par rapport à la formule
	 * @param ConfiguratorStepAbstract $step
	 * @param array $detail
	 * @param string $formula
	 */
	public static function getDefaultValueFormula($cart_detail_model, ConfiguratorStepAbstract $step, array $detail, $formula)
	{

		$default_value = (float) self::loadFormula($cart_detail_model, $step, $detail, $formula);

		if($default_value === 0.0) {
			$default_value = false;
		}

		return $default_value;
	}

	public static function loadFormula($cart_detail_model, ConfiguratorStepAbstract $step, array $detail, $formula)
	{
		$adapter = new FormulaStepAdapter((int) $step->id, $detail);

		$formula_builder = new FormulaBuilder($formula);
		$formula_builder->setAdapter($adapter);

		if(!$formula_builder->exec()) {
			$cart_detail_model->steps_errors[$step->id] = $formula_builder->getError();
			$cart_detail_model->option_ids_errors = array_merge($cart_detail_model->option_ids_errors, $formula_builder->getAdapterErrors());
			return false;
		}

		return (float) $formula_builder->getResult();
	}
        
        public static function loadFormulasInText($cart_detail_model, ConfiguratorStepAbstract $step, array $detail, $text)
        {
            $formulas = array();
            preg_match_all('/{{(.*?)}}/', $text, $matches);
            $customFormulas = AdvancedFormulaCustom::findByStepId($step->id);
            foreach ($customFormulas as $customFormula) {
                foreach ($matches[1] as $match) {
                    if ($customFormula->variable === $match) {
                        $formulas[$customFormula->variable] = $customFormula->value;
                    }
                }
            }
            
            foreach ($formulas as $key => $formula) {
                $value = self::loadFormula($cart_detail_model, $step, $detail, $formula);
                $text = str_replace('{{' . $key . '}}', $value, $text);
            }
            
            return $text;
        }

}
