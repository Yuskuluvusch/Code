<?php

if (!class_exists('ExtraClassOne')) {
    class ExtraClassOne extends ExtraFormula
    {
		
		public function __construct()
        {
			$this->functions['EXAMPLE_ONE'] = array(
				'syntax' => $this->l('EXAMPLE_ONE(number)'),
				'name' => $this->l('Example one'),
				'desc' => $this->l('Returns the parameter times 10.'),
				'class' => 'ExtraClassOne',
				'method' => 'formulaOne',
				'is_extra' => true,
				'arguments' => 1
			);
			$this->functions['EXAMPLE_TWO'] = array(
				'syntax' => $this->l('EXAMPLE_TWO(number1;number2;...)'),
				'name' => $this->l('Example two'),
				'desc' => $this->l('Returns the sum of parameters times 20.'),
				'class' => 'ExtraClassOne',
				'method' => 'formulaTwo',
				'is_extra' => true,
				'arguments' => FormulaBuilder::UNLIMITED_ARGS,
			);
		}
		
		/**
		 * Your extras formula here
		 */
		
		public function formulaOne($arg)
		{
			return $arg * 10;
		}
		
		public function formulaTwo()
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
