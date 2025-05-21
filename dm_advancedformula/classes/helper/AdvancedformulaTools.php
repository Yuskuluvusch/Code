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

class AdvancedformulaTools extends Helper
{

    public static function getVersionMajor()
    {
        static $version = null;
        
        if($version == null) {
            $version_with_point = Tools::substr(_PS_VERSION_, 0, 3);
            $version = str_replace('.', '', $version_with_point);
        }
        
        return $version;
    }
	
	public static function existColumnInTable($table_name, $column_name) 
    {
        $sql = 'DESCRIBE '._DB_PREFIX_.$table_name;        
        $columns = Db::getInstance()->executeS($sql);
        $found = false;
        
        foreach($columns as $col){
            if($col['Field'] == $column_name){
                $found = true;
                break;
            }
        }
        
        return $found;
    }
    
}
