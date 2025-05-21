<?php

if (!class_exists('ExtraFormula')) {

    class ExtraFormula {

        public $functions = array();
        public $classes = array();
        
        protected static $detail;
        
        public function __construct() {
            $dir = dirname(__FILE__) . '/extra';
            $classes = scandir($dir);

            foreach ($classes as $class) {
                if ($class != '.' && $class != '..' && strpos($class, '.php') !== false) {
                    include_once($dir . '/' . $class);
                    $class = str_replace('.php', '', $class);
                    $load_class = new $class();
                    $this->classes[$class] = $load_class;
                    $this->functions = array_merge($this->functions, $load_class->getFunctions());
                }
            }
        }

        protected function l($string) {
            $string = str_replace('\'', '\\\'', $string);
            return Translate::getModuleTranslation('Configurator', $string, __CLASS__);
        }

        public function getFunctions() {
            return $this->functions;
        }

        public static function setDetail($detail) {
            self::$detail = $detail;
        }
        
        public function getClasses() {
            return $this->classes;
        }

        public function getClass($class) {
            return $this->classes[$class];
        }

    }

}
