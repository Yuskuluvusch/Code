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

if (!class_exists('FormulaStepAdapter')) {
    require_once(dirname(__DIR__).'/FormulaBaseFunctions.php');
    require_once(dirname(__DIR__).'/FormulaBuilder.php');
    /**
     * Adapter permettant d'appliquer certaines méthodes
     * de la formule pour une étape particulière
     */
    class FormulaStepAdapter extends FormulaBaseFunctions
    {
        protected $current_id_step;
        protected $detail;
        
        public function __construct($id_step, array $detail)
        {
            $this->current_id_step = (int)$id_step;
            $this->detail = $detail;
        }
        
        public function getDetail() {
            return $this->detail;
        }

        public function getFirstSelectedPositionOption($from_id_step) {
            $value = 0;
            if (is_null($from_id_step)) {
                $from_id_step = $this->current_id_step;
            }
            
            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$from_id_step) {
                    continue;
                }
                
                $position = 0;
                foreach ($step_detail['options'] as $option_detail) {
                    $position++;
                    if ((bool)$option_detail['selected'] === true) {
                        $value = $position;
                        break 2;
                    }
                }
            }
            
            return (float)$value;
        }
        
        public function getStepPrice($id_step) {
            if(isset($this->detail[(int)$id_step]) && isset($this->detail[(int)$id_step]['total_step_amount'])) {
                return (float)$this->detail[(int)$id_step]['total_step_amount'];
            }
            
            return 0.0;
        }
        
        public function getSumFeature($id_step, $id_feature) {
            $value = 0;
            if (is_null($id_step)) {
                $id_step = $this->current_id_step;
            }
            
            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$id_step) {
                    continue;
                }
                
                $step = ConfiguratorStepFactory::newObject((int)$id_step);
                if(Validate::isLoadedObject($step) && $step->type === ConfiguratorStepAbstract::TYPE_STEP_PRODUCTS) {
                    foreach ($step_detail['options'] as $option_detail) {
                        if ((bool)$option_detail['selected'] === true) {
                            $id_product = (int)$option_detail['id_option'];
                           
                            $features_list = Product::getFeaturesStatic($id_product);
                            foreach ($features_list as $feature_detail) {
                                if((int)$id_feature === (int)$feature_detail['id_feature']) {
                                    $feature_value = new FeatureValue((int)$feature_detail['id_feature_value'], (int)Context::getContext()->language->id);
                                    if (isset($option_detail['qty']) && (int)$option_detail['qty'] > 0) {
                                        $value += ((int)$feature_value->value * (int)$option_detail['qty']);
                                    } else {
                                        $value += (int)$feature_value->value;
                                    }
                                }
                            }
                        }
                    }
                }
                
                break;
            }
            
            return (float)$value;
        }
        
        public function getSumQty($id_step) {
            $value = 0;
            if (is_null($id_step)) {
                $id_step = $this->current_id_step;
            }
            
            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$id_step) {
                    continue;
                }
                
                $step = ConfiguratorStepFactory::newObject((int)$id_step);
                if(Validate::isLoadedObject($step)) {
                    foreach ($step_detail['options'] as $option_detail) {
                        if ((bool)$option_detail['selected'] === true && isset($option_detail['qty']) && (int)$option_detail['qty'] > 0) {
                            $value += (int)$option_detail['qty'];
                        }
                    }
                }
                break;
            }
            
            return (float)$value;
        }

        public function getOptionQty($from_id_step, $id_option)
        {
            $value = 0;
            if (is_null($from_id_step)) {
                $from_id_step = $this->current_id_step;
            }

            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$from_id_step) {
                    continue;
                }
                foreach ($step_detail['options'] as $option_detail) {
                    if ( (int)$option_detail['id'] === (int)$id_option && (bool)$option_detail['selected'] === true && isset($option_detail['qty']) && (int)$option_detail['qty'] > 0 ) {
                        $value = (int)$option_detail['qty'] ;
                    }
                }
            }

            return (float)$value;
        }


        public function getProductProperty($id_product, $property){

            $product = new Product((int)$id_product);

            if(Validate::isLoadedObject($product) && property_exists($product, $property)){
                return $product->{$property};
            }

            return 0.0;

        }
        
        public function getFirstSelectedValueOption($from_id_step) {
            $value = 0;
            if (is_null($from_id_step)) {
                $from_id_step = $this->current_id_step;
            }
            
            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$from_id_step) {
                    continue;
                }
                
                foreach ($step_detail['options'] as $option_detail) {
                    if ((bool)$option_detail['selected'] === true) {
                        $value = $option_detail['name'];
                        break 2;
                    }
                }
            }
            
            return (float)$value;
        }
        
        public function getMaxValueOfSelectedOption($from_id_step) {
            $value = 0;
            if (is_null($from_id_step)) {
                $from_id_step = $this->current_id_step;
            }
            
            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$from_id_step) {
                    continue;
                }
                
                foreach ($step_detail['options'] as $option_detail) {
                    if ((bool)$option_detail['selected'] === true) {
                        $value = $option_detail['max'];
                        break 2;
                    }
                }
            }
            
            return (float)$value;
        }
        
        public function getOptionValue($from_id_step, $id)
        {
            $value = null;
            if (is_null($from_id_step)) {
                $from_id_step = $this->current_id_step;
            }
            
            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$from_id_step) {
                    continue;
                }
                foreach ($step_detail['options'] as $option_detail) {
                    if ((int)$option_detail['id'] === (int)$id) {
                        $value = str_replace(',', '.', $option_detail['value']);
                        break 2;
                    }
                }
            }

            if ((int)$from_id_step !== (int)$this->current_id_step && !is_numeric($value)) {
                $this->errors[] = (int)$option_detail['id'];
                throw new Exception(Configurator::ERROR_FORMULA_VALUE_PREVIOUS);
            } elseif (empty($value)) {
                throw new Exception(Configurator::ERROR_FORMULA_VALUE_EMPTY);
            } elseif (!is_numeric($value)) {
                $this->errors[] = (int)$option_detail['id'];
                throw new Exception(Configurator::ERROR_FORMULA_VALUE);
            }
            
            return (float)$value;
        }
        
        public function getMaxValue($from_id_step, $id){
            $value = null;
            if (is_null($from_id_step)) {
                $from_id_step = $this->current_id_step;
            }
            
            foreach ($this->detail as $step_detail) {
                if ((int)$step_detail['id'] !== (int)$from_id_step) {
                }
                foreach ($step_detail['options'] as $option_detail) {
                    if ((int)$option_detail['id'] === (int)$id) {
                        $value = str_replace(',', '.', $option_detail['max']);
                    }
                }
            }
            
            return (float)$value;
            
        }

        public function getBasePrice($tax = false)
        {
            $configurator_step = ConfiguratorStepFactory::newObject($this->current_id_step);
            $configurator = new ConfiguratorModel($configurator_step->id_configurator);
            $product = new Product($configurator->id_product);
            return $product->getPrice($tax);
        }
    }
}
