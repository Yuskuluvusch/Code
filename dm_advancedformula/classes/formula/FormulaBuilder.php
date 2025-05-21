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

if (!class_exists('FormulaBuilder')) {
    class FormulaBuilder
    {
        const UNLIMITED_ARGS = -1;
        
        private $formula;
        private $result = 0.00;
        private $adapter;
		private $extras;
        private $regex_group = array('\(','\)','\d','\/','\*','\.',';','-','\+','\%','"(.*?)"');
        private $return_type = 'float';
        /**
         * Contient les fonctions mathématiques disponible de la classe
         * Format :
         *      'key' correspond au mot clé visuel utilisé dans le champ de formule en FO
         *          'syntax' permet au client de voir comment cela s'écrit dans le champ
         *          'desc' une description de la fonction
         *          'method' la méthode php correspondante, peut-être une méthode de la
         *                   classe pour d'autres fonctions mathématique plus complexe
         *          'arguments' le nombre d'argument à passer, chaque argument est de type NOMBRE
         *                      => 0 : aucun argument
         *                      => X : X arguments à passer, X étant une valeur de type INT
         *                      => -1 : Infinité d'arguments possible
         * /!\ A l'ajout d'une nouvelle fonction, il faut implémenter cette méthode dans
         * la classe abstraite FormulaBaseFunctions puis dans les différents adapters si besoin
         * @var array 
         */
        protected static $functions = array();
        
        public $error;
        
        public function __construct($formula = '')
        {
            self::$functions = array(
                'COS' => array(
                    'syntax' => $this->l('COS(number)'),
                    'name' => $this->l('Cosine'),
                    'desc' => $this->l('Returns the cosine of an angle'),
                    'method' => 'cos',
                    'arguments' => 1
                ),
                'SIN' => array(
                    'syntax' => $this->l('SIN(number)'),
                    'name' => $this->l('Sine'),
                    'desc' => $this->l('Returns the sine of an angle'),
                    'method' => 'sin',
                    'arguments' => 1
                ),
                'POW' => array(
                    'syntax' => $this->l('POW(number;power)'),
                    'name' => $this->l('Power'),
                    'desc' => $this->l('Returns the value of the number raised to a power'),
                    'method' => 'pow',
                    'arguments' => 2
                ),
                'SQRT' => array(
                    'syntax' => $this->l('SQRT(number)'),
                    'name' => $this->l('Square root'),
                    'desc' => $this->l('Returns the square root of the number'),
                    'method' => 'sqrt',
                    'arguments' => 1
                ),
                'PI' => array(
                    'syntax' => $this->l('PI()'),
                    'name' => $this->l('PI number'),
                    'desc' => $this->l('Returns an approximation of pi. No argument is required.'),
                    'method' => 'pi',
                    'arguments' => 0
                ),
                'SUM_OPTION_QTY' => array(
                    'syntax' => $this->l('SUM_OPTION_QTY(ID Step; ID Option)'),
                    'name' => $this->l('Sum of quantity of an option '),
                    'desc' => $this->l('Returns the sum of quantity of an option'),
                    'method' => 'getOptionQty',
                    'arguments' => 2
                ),
                'MAX_VALUE_SELECTED' => array(
                    'syntax' => $this->l('MAX_VALUE_SELECTED(ID Step)'),
                    'name' => $this->l('Max Value of the option selected'),
                    'desc' => $this->l('Returns the max value of the option selected only for unique option in step.'),
                    'method' => 'getMaxValueOfSelectedOption',
                    'arguments' => 1
                ),
                
                'MAX_VALUE' => array(
                    'syntax' => $this->l('MAX_VALUE(ID Step; ID Option)'),
                    'name' => $this->l('max value of option '),
                    'desc' => $this->l('Returns the max value of option'),
                    'method' => 'getMaxValue',
                    'arguments' => 2
                ),
                'OPTION' => array(
                    'syntax' => $this->l('OPTION(ID Step;ID Option)'),
                    'name' => $this->l('Option\'s value written'),
                    'desc' => $this->l('Returns the value written by the customer in the textfield of an option. You can choose an option from a specific step.'),
                    'method' => 'getOptionValue',
                    'arguments' => 2
                ),
                'STEP_OPT_VALUE_SELECTED' => array(
                    'syntax' => $this->l('STEP_OPT_VALUE_SELECTED(ID Step)'),
                    'name' => $this->l('Value of the option selected'),
                    'desc' => $this->l('Returns the value of the option selected only for unique option in step.'),
                    'method' => 'getFirstSelectedValueOption',
                    'arguments' => 1
                ),
                'STEP_OPT_POSITION_SELECTED' => array(
                    'syntax' => $this->l('STEP_OPT_POSITION_SELECTED(ID Step)'),
                    'name' => $this->l('Option\'s position selected'),
                    'desc' => $this->l('Returns the value position by the customer. If more than one selected it give you the first one finded.'),
                    'method' => 'getFirstSelectedPositionOption',
                    'arguments' => 1
                ),
                'STEP_PRICE' => array(
                    'syntax' => $this->l('STEP_PRICE(ID Step)'),
                    'name' => $this->l('Price of a step'),
                    'desc' => $this->l('Returns the price of a selected step.'),
                    'method' => 'getStepPrice',
                    'arguments' => 1
                ),
                'AVG' => array(
                    'syntax' => $this->l('AVG(number1;number2;...)'),
                    'name' => $this->l('Average'),
                    'desc' => $this->l('Returns the average of arguments'),
                    'method' => 'getAverage',
                    'arguments' => self::UNLIMITED_ARGS
                ),
                'IS_BETWEEN' => array(
                    'syntax' => $this->l('IS_BETWEEN(value;min;max)'),
                    'name' => $this->l('Is between two value'),
                    'desc' => $this->l('Returns 1 if it\'s true and 0 if false'),
                    'method' => 'isBetween',
                    'arguments' => 3
                ),
                'ROUND_HALF_UP' => array(
                    'syntax' => $this->l('ROUND_HALF_UP(value;precision)'),
                    'name' => $this->l('Round up a number with half'),
                    'desc' => $this->l('Returns a Round up a number with half'),
                    'method' => 'getRoundHalfUp',
                    'arguments' => 2
                ),  
                'ROUND_UP' => array(
                    'syntax' => $this->l('ROUND_UP(value;precision)'),
                    'name' => $this->l('Round up a number'),
                    'desc' => $this->l('Returns a Round up a number'),
                    'method' => 'getRoundUp',
                    'arguments' => 2
                ),  
                'PAIR' => array(
                    'syntax' => $this->l('PAIR(value)'),
                    'name' => $this->l('Get the next pair value'),
                    'desc' => $this->l('Return the pair value. If impair given return the next pair.'),
                    'method' => 'getPair',
                    'arguments' => 1
                ),
                'CONCAT' => array(
                    'syntax' => $this->l('CONCAT(value1;value2;...)'),
                    'name' => $this->l('Concatenates multiple strings'),
                    'desc' => $this->l('Return the string value.'),
                    'method' => 'getConcat',
                    'arguments' => self::UNLIMITED_ARGS
                ),
                'BASE_PRICE' => array(
                    'syntax' => $this->l('BASE_PRICE()'),
                    'name' => $this->l('Product base price'),
                    'desc' => $this->l('Returns the base price of the configurated product.'),
                    'method' => 'getBasePrice',
                    'arguments' => 0
                ),
                'SUM_FEATURE' => array(
                    'syntax' => $this->l('SUM_FEATURE(ID Step;ID Feature)'),
                    'name' => $this->l('Sum of selected feature in step product'),
                    'desc' => $this->l('Returns the sum of feature selected in a step product'),
                    'method' => 'getSumFeature',
                    'arguments' => 2
                ),
                'SUM_QTY' => array(
                    'syntax' => $this->l('SUM_QTY(ID Step)'),
                    'name' => $this->l('Sum of quantity of option selected in a step'),
                    'desc' => $this->l('Returns the sum of quantity of option selected in a step'),
                    'method' => 'getSumQty',
                    'arguments' => 1
                ),
                'PRODUCT_PROPERTY' => array(
                    'syntax' => $this->l('PRODUCT_PROPERTY(ID Product;Property Name)'),
                    'name' => $this->l('Get a property of a product in a step'),
                    'desc' => $this->l('Return the property value of a product in a step'),
                    'method' => 'getProductProperty',
                    'arguments' => 2
                )
                
            );
			
            include_once(dirname(__FILE__).'/ExtraFormula.php');
            $this->extras = new ExtraFormula();
            self::$functions = array_merge(self::$functions,$this->extras->getFunctions());
            
            $formula = trim($formula);
            if (!Validate::isString($formula) || !Validate::isCleanHtml($formula)) {
                throw new Exception(Configurator::ERROR_FORMULA);
            }
            
            $this->formula = $this->clean($formula);
        }
        
        /**
         * Nettoyage de la formule contre toute injection PhP que l'on ne souhaite pas
         * @param String $formula
         * @return String
         */
        private function clean($formula)
        {
            $matches = array();
            $regex = $this->getSecurityRegex();
            $formula = str_replace(',', '.', str_replace(' ', '', $formula));
            $res = preg_match_all($regex, $formula, $matches);
			
            if ($res === false) {
                throw new Exception(Configurator::ERROR_FORMULA);
            }
            if ($res) {
                return implode('', $matches[0]);
            }
            return '';
        }
        
        /**
         * Evalue et convertie une formule en un code PhP exécutable
         * @param String $formula
         * @return String
         */
        private function convert($formula)
        {
            foreach (self::$functions as $fName => $function) {
                if (strpos($formula, $fName) === false) {
                    continue;
                }
                $method = $function['method'];
				
                $can_call_adapter = Tools::isCallable(array($this->adapter, $method));
                $can_call_extras = false;
                foreach ($this->extras->getClasses() as $class) {
                    $can_call_extras = ($can_call_extras) ? $can_call_extras : Tools::isCallable(array($class, $method));
                }

                if (!Tools::isCallable($method) && !$can_call_adapter && !$can_call_extras) {
                    // Exception si method n'est pas une fonction php ou méthode d'un adapter
                    throw new Exception(Configurator::ERROR_FORMULA_METHOD);
                }
                if ($can_call_adapter) {
                    $method = '('.$this->return_type.')$this->adapter->'.$method;
                } else if ($can_call_extras) {
					
                    $method = '('.$this->return_type.')$this->extras->getClass("'.$function['class'].'")->'.$method;
                }
                $formula = str_replace($fName, $method, $formula);
            }
			
            return 'return ('.str_replace(';', ',', $formula).');';
        }
        
        /**
         * Construit et retourne une regex permettant
         * de sécuriser evalde toute injection php
         */
        private function getSecurityRegex()
        {
            $regex = '/(';
            foreach (array_keys(self::$functions) as $k => $function_name) {
                if ($k > 0) {
                    $regex .= ')|(';
                }
                $regex .= $function_name.'\(';
            }
            $regex .= ')|(';
            foreach ($this->regex_group as $k => $reg) {
                if ($k > 0) {
                    $regex .= ')|(';
                }
                $regex .= $reg;
            }
            $regex .= ')+/';
            return $regex;
        }
        
        protected function l($string)
        {
            $string = str_replace('\'', '\\\'', $string);
            return Translate::getModuleTranslation('Configurator', $string, __CLASS__);
        }
        
        /**
         * Permet de gérer un adapter pour appliquer les calculs
         * @param FormulaBaseFunctions $adapter
         */
        public function setAdapter(FormulaBaseFunctions $adapter)
        {
            $this->adapter = $adapter;
            ExtraFormula::setDetail($adapter->getDetail());
        }
        
        public function setReturnType($return_type)
        {
            $this->return_type = $return_type;
        }
        
        public function getFormula()
        {
            return $this->formula;
        }
        
        public function getResult()
        {
            return $this->result;
        }
        
        public function getError()
        {
            return $this->error;
        }
        
        public function getAdapterErrors()
        {
            return $this->adapter->getErrors();
        }
        
        public function getFunctions()
        {
            asort(self::$functions);
            return self::$functions;
        }
        
        /**
         * Exécute une formule
         */
        public function exec()
        {
            if (empty($this->formula)) {
                return true;
            }
            
            try {
                // Vérification adapter configuré
                if (is_null($this->adapter)) {
                    throw new Exception(Configurator::ERROR_FORMULA_ADAPTER);
                }
                $formula = $this->convert($this->formula);
                /**
                 * La formule a été traitée et sécurisée
                 * par la méthode clean dans le constructeur
                 * @see FormulaBuilder::clean
                 */
                $result = @eval($formula);
                $last_error = error_get_last();
                if (!is_null($last_error) && strpos($last_error['file'], 'eval()') !== false) {
                    throw new Exception($last_error['message']);
                }
                // Pas !$result car peut retourner 0 comme résultat
                if (is_null($result) || $result === false) {
                    return false;
                }
                $this->result = $this->castResult($result);
            } catch (Exception $exc) {
                $this->error = $exc->getMessage();
                return false;
            }
            
            return true;
        }
        
        private function castResult($result)
        {
            switch ($this->return_type) {
                case 'string':
                    return (string)$result;
                    break;
                case 'int':
                    return (int)$result;
                    break;
                default:
                    return (float)$result;
                    break;
            }
        }
    }
}
