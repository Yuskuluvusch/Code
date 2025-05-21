<?php
/**
 * 2007-2016 PrestaShop
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
 *  @author    DMConcept <support@dmconcept.fr>
 *  @copyright 2015 DMConcept
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/classes/helper/AdvancedformulaTools.php');
require_once(dirname(__FILE__).'/classes/helper/AdvancedformulaHelper.php');
require_once(dirname(__FILE__).'/classes/formula/FormulaBuilder.php');
require_once(dirname(__FILE__).'/classes/formula/adapters/FormulaStepAdapter.php');
require_once(dirname(__FILE__).'/classes/formula/adapters/FormulaTestAdapter.php');
require_once(dirname(__FILE__).'/classes/customformula/AdvancedFormulaCustom.php');

class Dm_Advancedformula extends Module
{

    private $hooks = array(
        'configuratorActionCartDetailGetAmount',
        'configuratorActionCartDetailAddOption',
        'configuratorActionCartDetailComputeAddingPrice',
        'configuratorAdminActionStepsAfterSaveOption',
        'configuratorAdminActionStepsControllerInitForm',
        'configuratorAdminActionStepsControllerSetMedia',
        'configuratorAdminDisplayStepsControllerPriceImpact',
        'configuratorAdminActionStepsControllerInitModal',
        'configuratorAdminActionStepsControllerAfterPostProcess',
        'actionObjectConfiguratorStepModelBeforeAfter',
        'displayAdminStepConfiguratorAfterForm'
    );

    public function __construct()
    {
        $this->name = 'dm_advancedformula';
        $this->tab = 'front_office_features';
        $this->version = '1.0.8';
        $this->ps_versions_compliancy = array('min' => '1.6.0.4', 'max' => _PS_VERSION_);
        $this->author = 'DMConcept';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Advanced Formula');
        $this->description = $this->l('You can customize the product price with an advanced formula or add a formula to calculate an option price in the Configurator.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');
        //$this->registerHooks();
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install($keep = false)
    {
        include(dirname(__FILE__) . '/sql/install.php');
        return parent::install() && $this->registerHooks();
    }

    public function registerHooks()
    {
        foreach ($this->hooks as $hook) {
            if(!$this->registerHook($hook)) {
                return false;
            }
        }
        return true;
    }

    public function uninstall($keep = false)
    {
        if (!$keep) {
            include(dirname(__FILE__) . '/sql/uninstall.php');
        }
        return parent::uninstall();
    }

    public function reset()
    {
        if ($this->uninstall(true)) {
            return $this->install(true);
        }
        return false;
    }

    public function disable($force_all = false)
    {
        if (AdvancedformulaTools::getVersionMajor() < 17) {
            $this->uninstallOverrides();
        }
        return parent::disable($force_all);
    }

    public function enable($force_all = false)
    {
        if (AdvancedformulaTools::getVersionMajor() < 17) {
            $this->uninstallOverrides();
            $this->installOverrides();
        }
        return parent::enable($force_all);
    }


    // HOOK

    public function hookConfiguratorActionCartDetailGetAmount($params)
    {
        $cart_detail_model = &$params['cart_detail_model'];
        $option = $params['option'];
        $base_price = $params['base_price'];
        $result = &$params['result'];

        switch ($option->impact_type) {
            case ConfiguratorStepOptionAbstract::IMPACT_TYPE_AREA:
                if ((int) $option->impact_step_id) {
                    // Surface par formule de calcul
                    $configurator_step = ConfiguratorStepFactory::newObject((int) $option->impact_step_id);
                    if (Validate::isLoadedObject($configurator_step) && !empty($configurator_step->formula_surface)) {
                        $result = ((float) $option->impact_value * (float) AdvancedformulaHelper::getSurfaceFormula(
                            $cart_detail_model,
                            $configurator_step,
                            $cart_detail_model->getDetail()
                        ));
                        // Surface venant d'une grille tarifaire
                    }
                }
                break;
            case ConfiguratorStepOptionAbstract::IMPACT_TYPE_AREA_MULTIPLE:
                if ($option->impact_multiple_step_id) {
                    $impact_multiple_step_id = explode(',', $option->impact_multiple_step_id);
                    foreach ($impact_multiple_step_id as $id) {
                        // Surface par formule de calcul
                        $configurator_step = ConfiguratorStepFactory::newObject((int)$id);
                        if ($cart_detail_model->existStep((int) $configurator_step->id)) {
                            if (Validate::isLoadedObject($configurator_step) && !empty($configurator_step->formula_surface)) {
                                $result = ((float)$option->impact_value * (float)AdvancedformulaHelper::getSurfaceFormula(
                                    $cart_detail_model,
                                    $configurator_step,
                                    $cart_detail_model->getDetail()
                                ));
                                // Surface venant d'une grille tarifaire
                            }
                        }
                    }
                }
                break;
            case ConfiguratorStepOptionAbstract::IMPACT_TYPE_PRICELIST_AREA_SQUARE:
            case ConfiguratorStepOptionAbstract::IMPACT_TYPE_PRICELIST_AREA:
                if ((int) $option->impact_step_id && !empty($option->price_list)) {
                    // Récupération de la surface de l'étape
                    $configurator_step = ConfiguratorStepFactory::newObject((int)$option->impact_step_id);
                    if (Validate::isLoadedObject($configurator_step) && !empty($configurator_step->formula_surface)) {
                        $surface = (float)AdvancedformulaHelper::getSurfaceFormula(
                            $cart_detail_model,
                            $configurator_step,
                            $cart_detail_model->getDetail()
                        );
                        // Surface venant d'une grille tarifaire

                        /**
                         * @todo: Voir comment traiter les bornes extérieures
                         */
                        $cart_detail_model->pricelist_helper->setPricelist(
                            Tools::jsonDecode($option->price_list, true)
                        );
                        $price = $cart_detail_model->pricelist_helper->getValue($surface);

                        if (ConfiguratorStepOptionAbstract::IMPACT_TYPE_PRICELIST_AREA){
                            $surface = $price * $surface;
                        } else {
                            $surface = $price;
                        }

                        $result = $surface;
                    }
                }
                break;
            case ConfiguratorStepOptionAbstract::IMPACT_TYPE_AMOUNT_FORMULA:
                $formula = $option->impact_formula;
                $configurator_step = ConfiguratorStepFactory::newObject((int)$option->id_configurator_step);
                $result = (float) AdvancedformulaHelper::getOptionPriceFormula(
                    $cart_detail_model,
                    $configurator_step,
                    $cart_detail_model->getDetail(),
                    $formula
                );
        }

        return false;
    }

    public function hookConfiguratorActionCartDetailAddOption($params)
    {
        $cart_detail_model = &$params['cart_detail_model'];
        $detail = $params['detail'];
        $configuratorStep = $params['configuratorStep'];
        $configurator_step_option = $params['configurator_step_option'];
        $valueIsInvalid = $params['valueIsInvalid'];
        $extras = $params['extras'];
        $option = &$params['option'];

        // DEFAULT VALUE
        $default = false;
        if ($configurator_step_option->default_value !== "0") {
            //Valeur par défaut
            if (isset($configurator_step_option->default_value) && $configurator_step_option->default_value !== false) {
                // Passage dans la formule
                $default = AdvancedformulaHelper::getDefaultValueFormula(
                    $cart_detail_model,
                    $configuratorStep,
                    $detail,
                    $configurator_step_option->default_value
                );
            }

            // Force Default value
            if (isset($configurator_step_option->default_value)
                && $configurator_step_option->default_value !== false
                && $configurator_step_option->force_value
            ) {
                $valueIsInvalid = true;
                $default = AdvancedformulaHelper::getDefaultValueFormula(
                    $cart_detail_model,
                    $configuratorStep,
                    $detail,
                    $configurator_step_option->default_value
                );
            }
        } else {
            $default = $configurator_step_option->default_value;
        }

        // WEIGHT
        $option['weight'] = (float) AdvancedformulaHelper::loadFormula(
            $cart_detail_model,
            $configuratorStep,
            $detail,
            $configurator_step_option->weight
        );

        // DELIVERY IMPACT
        $option['delivery_impact'] = (float) AdvancedformulaHelper::loadFormula(
            $cart_detail_model,
            $configuratorStep,
            $detail,
            $configurator_step_option->delivery_impact
        );

        $option['value'] = ($valueIsInvalid ? $default : $extras['value']);
        if ($option['value'] !== false) {
            $option['selected'] = true;
        }
    }

    public function hookConfiguratorActionCartDetailComputeAddingPrice($params)
    {
        $cart_detail_model = &$params['cart_detail_model'];
        $configurator_step = $params['step'];
        $detail = $params['detail'];

        if (!empty($configurator_step->formula)) {
            return AdvancedformulaHelper::getPriceFormula($cart_detail_model, $configurator_step, $detail);
        }

        return false;
    }

    public function hookConfiguratorAdminActionStepsControllerSetMedia($params)
    {
        $controller = &$params['controller'];
        $controller->addJS(array(
            _MODULE_DIR_.$this->name.'/views/js/services/formula-syntax.js',
            _MODULE_DIR_.$this->name.'/views/js/services/formula-form.js',
            _MODULE_DIR_.$this->name.'/views/js/tinymce/plugins/formula/formula.plugin.js',
            _MODULE_DIR_.$this->name.'/views/js/adminConfigurator.js',
            _MODULE_DIR_.$this->name.'/views/js/formulaEditor.js',
        ));
    }

    public function hookConfiguratorAdminActionStepsControllerInitModal($params)
    {
        $controller = &$params['controller'];
        $controller->modals[] = array(
            'modal_id' => 'modal_configurator_formula',
            'modal_class' => 'modal-md',
            'modal_title' => '<i class="icon-calculator"></i> ' . $this->l('Insert a function'),
            'modal_content' => $this->getModalFormulaContent($controller),
        );
    }

    public function hookConfiguratorAdminActionStepsAfterSaveOption($params)
    {
        $step = $params['step'];
        $input_id = $params['input_id'];
        $configuratorStepOption = &$params['configuratorStepOption'];

        switch ($configuratorStepOption->impact_type) {
            case ConfiguratorStepOptionAbstract::IMPACT_TYPE_AMOUNT_FORMULA:
                $configuratorStepOption->impact_formula = Tools::getValue('impact_formula_'.$input_id);
                $configuratorStepOption->impact_step_id = null;
                $configuratorStepOption->impact_multiple_step_id = null;
                $configuratorStepOption->price_list = null;
                break;
        }
    }

    public function hookConfiguratorAdminActionStepsControllerInitForm($params)
    {
        $id = $params['id'];
        $controller = $params['controller'];

        if (method_exists($this, 'initForm'.$id)) {
            $form = $this->{'initForm'.$id}($controller, Tools::strtolower($id).'.tpl');
            $controller->tpl_form_vars['tabs'][$id]['form_content_html'] = $form;
        }
    }

    public function hookConfiguratorAdminDisplayStepsControllerPriceImpact($params)
    {
        $id = (int)$params['id'];
        $configuratorStepOption = $params['configuratorStepOption'];

        $this->context->smarty->assign(array(
            'id' => $id,
            'configuratorStepOption' => $configuratorStepOption
        ));

        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configurator_steps/price_impact.tpl');
    }

    public function hookConfiguratorAdminActionStepsControllerAfterPostProcess($params)
    {
        $action = (Tools::getIsset('action')) ? Tools::getValue('action') : null;
        if (method_exists($this, 'ajaxProcess'.$action)) {
            $this->{'ajaxProcess'.$action}();
        }
    }

    public function hookActionObjectConfiguratorStepModelBeforeAfter($params)
    {
        // @todo
        $configurator_step = &$params['configurator_step'];

        $configurator_step->formula = Tools::getValue('formula');
        $configurator_step->formula_surface = Tools::getValue('formula_surface');
        
        AdvancedFormulaCustom::deleteCustomFormulasByStepId($configurator_step->id);
        if (isset(Tools::getValue('customformula')['step'][$configurator_step->id])) {
            $customformulas = json_decode(Tools::getValue('customformula')['step'][$configurator_step->id], true);
            foreach ($customformulas as $customformula) {
                $customformulaModel = new AdvancedFormulaCustom();
                $customformulaModel->id_configurator_step = $configurator_step->id;
                $customformulaModel->variable = (string)$customformula['variable'];
                $customformulaModel->value = (string)$customformula['value'];
                $customformulaModel->save();
            }
        }
    }
    
    public function hookDisplayAdminStepConfiguratorAfterForm($params)
    {
        $configurator_step = $params['configurator_step'];

        $this->context->smarty->assign(array(
            'type' => 'step',
            'configurator_step' => $configurator_step,
            'customFormulasStepValues' => json_encode(AdvancedFormulaCustom::findByStepId($configurator_step->id))
        ));

        return $this->context->smarty->fetch($this->local_path.'views/templates/hook/display-admin-step-configurator-after-form.tpl');
    }


    // FUNCTIONS

    public function getModalFormulaContent($controller)
    {
        $choices = isset($controller->conditions_choices['block_option']['groups'])
            ? $controller->conditions_choices['block_option']['groups']
            : array();

        if (empty($choices)) {
            $choices[0] = array(
                'class' => 'col-lg-4',
                'selects' => array(
                    0 => array (
                        'params' => array (
                            'class' => 'select_step'
                        ),
                        'options' => array()
                    )
                )
            );
        }

        // Dans les options disponibles on rajoute les options de l'étape courante
        if (Validate::isLoadedObject($controller->getObject())) {
            $options = $controller->getObject()->getOptions($controller->getContext()->language->id);
            $options_option = array();
            foreach ($options as $configurator_step_option) {
                if (Validate::isLoadedObject($configurator_step_option)) {
                    $options_option[$configurator_step_option->id] = array(
                        'option' => $configurator_step_option->option['name'],
                        'classname' => get_class($configurator_step_option),
                        'object' => $configurator_step_option,
                        'attrs' => array()
                    );
                }
            }
            $choices[1]['selects'][] = array(
                'params' => array(
                    'data-parentid' => (int)$controller->getObject()->id,
                    'class' => 'select_option'
                ),
                'options' => $options_option
            );
        }

        // Formula
        $formula_builder = new FormulaBuilder();

        $configurator_step = $controller->getObject();

        $this->context->smarty->assign(array(
            'functions' => $formula_builder->getFunctions(),
            'step' => $configurator_step,
            'choices' => $choices
        ));

        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configurator_steps/modal/formula.tpl');
    }

    public function initFormFormula($controller, $tpl_form)
    {
        // Languages
        $this->context->smarty->assign(array(
            'languages' => $controller->_languages,
            'default_form_language' => $controller->default_form_language,
            'id_lang' => $controller->getContext()->language->id
        ));
        // Configurator
        $configurator_step = $controller->getObject();
        $this->context->smarty->assign(array(
            'configurator_step' => $controller->getObject(),
            'id_configurator' => $controller->getIdConfigurator(),
            'step' => $configurator_step
        ));
        // Options
        $options = false;
        if ($controller->getDisplay() === 'edit') {
            $options = $configurator_step->getOptions($controller->getContext()->language->id);
        }
        $this->context->smarty->assign('options', $options);
        // Rendering
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configurator_steps/'.$tpl_form);
    }

    public function initFormFormulaSurface($controller, $tpl_form)
    {
        // Languages
        $this->context->smarty->assign(array(
            'languages' => $controller->_languages,
            'default_form_language' => $controller->default_form_language,
            'id_lang' => $controller->getContext()->language->id
        ));
        // Configurator
        $configurator_step = $controller->getObject();
        $this->context->smarty->assign(array(
            'configurator_step' => $controller->getObject(),
            'id_configurator' => $controller->getIdConfigurator(),
            'step' => $configurator_step
        ));
        // Options
        $options = false;
        if ($controller->getDisplay() === 'edit') {
            $options = $configurator_step->getOptions($controller->getContext()->language->id);
        }
        $this->context->smarty->assign('options', $options);
        // Rendering
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configurator_steps/'.$tpl_form);
    }

    public function ajaxProcessFormulaValidation()
    {
        $success = 1;
        $formula = Tools::getValue('formula', '');
        $adapter = new FormulaTestAdapter();
        $formula_builder = new FormulaBuilder($formula);
        $formula_builder->setAdapter($adapter);
        if (!$formula_builder->exec()) {
            $success = 0;
        }
        die(Tools::jsonEncode($success));
    }
}
