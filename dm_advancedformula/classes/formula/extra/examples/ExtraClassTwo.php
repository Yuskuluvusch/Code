<?php

if (!class_exists('ExtraClassTwo')) {
    class ExtraClassTwo extends ExtraFormula
    {
		
		public function __construct()
        {
			$this->functions['EXAMPLE_THREE'] = array(
				'syntax' => $this->l('EXAMPLE_THREE(number)'),
				'name' => $this->l('Example three'),
				'desc' => $this->l('Returns the parameter times 10.'),
				'class' => 'ExtraClassTwo',
				'method' => 'formulaThree',
				'is_extra' => true,
				'arguments' => 1
			);
			$this->functions['EXAMPLE_FOUR'] = array(
				'syntax' => $this->l('EXAMPLE_FOUR(number1;number2;...)'),
				'name' => $this->l('Example four'),
				'desc' => $this->l('Returns the sum of parameters times 20.'),
				'class' => 'ExtraClassTwo',
				'method' => 'formulaFour',
				'is_extra' => true,
				'arguments' => FormulaBuilder::UNLIMITED_ARGS,
			);
		}
		
		/**
		 * Your extras formula here
		 */
		
		public function formulaThree($arg)
		{
			return $arg * 10;
		}
		
		public function formulaFour()
		{
			$args = func_get_args();
			$result = 0;
			foreach($args as $arg) {
				$result += $arg;
			}
			return $result * 20;
		}
		
    }
}
