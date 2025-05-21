<?php
/**
 * 2007-2019 PrestaShop
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
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5.0
 */
if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

if (!class_exists('AdvancedFormulaCustom')) {

    /**
     * Class AdvancedFormulaCustom
     */
    class AdvancedFormulaCustom extends ObjectModel
    {
        public $id_configurator_step;
        public $variable;
        public $value;

        public static $definition = array(
            'table' => 'advancedformula_custom',
            'primary' => 'id_advancedformula_custom',
            'fields' => array(
                /* Classic fields */
                'id_configurator_step' => array(
                    'type' => self::TYPE_INT,
                    'validate' => 'isUnsignedId',
                    'required' => true
                ),
                'variable' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
                'value' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true)
            )
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            parent::__construct($id, $id_lang, $id_shop);
        }
        
        public static function deleteCustomFormulasByStepId($id_step)
        {
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . self::$definition['table']
                . ' WHERE id_configurator_step = ' . (int)$id_step;
            Db::getInstance()->execute($sql);
        }
        
        public static function findByStepId($id_step)
        {
            $key = 'AdvancedFormulaCustom::findByStepId-' . $id_step;
            if (DmCache::getInstance()->isStored($key)) {
                return DmCache::getInstance()->retrieve($key);
            }
            
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table']
                . ' WHERE id_configurator_step = ' . (int)$id_step;
            $results = Db::getInstance()->executeS($sql);
            
            $advancedFormulaCustomModel = new AdvancedFormulaCustom();
            $return = $advancedFormulaCustomModel->hydrateCollection(
                get_class(),
                $results,
                (int)Context::getContext()->language->id
            );
            DmCache::getInstance()->store($key, $return);

            return $return;
        }

    }
}
