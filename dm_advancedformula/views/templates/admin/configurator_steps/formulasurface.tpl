{*
* 2007-2014 PrestaShop
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
*  @author DMConcept <support@dmconcept.fr>
*  @copyright 2015 DMConcept
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="tab-pane tab-content">
    <div id="tab-pane-FormulaSurface" class="tab-pane">
        <div class="panel configurator-steps-tab">
            <h3 class="tab"> <i class="icon-calculator"></i> {l s='Surface formula' mod='dm_advancedformula'}</h3>

            {if $options}
				{if $step->use_input or $step->required}
				<div class="alert alert-info">
					{l s='This interface allows you to compose a formula that will be used to calculate the step\'s surface which will can be used to another step.' mod='dm_advancedformula'}
					<br/>
					{l s='You can use classic math operand such as:' mod='dm_advancedformula'}
					<span class="label label-info">+</span>
					<span class="label label-info">-</span>
					<span class="label label-info">/</span>
					<span class="label label-info">*</span>
					<span class="label label-info">(</span>
					<span class="label label-info">)</span>
					<span class="label label-info">% (modulo)</span>
					{l s='by typing the operand into the formula field.' mod='dm_advancedformula'}
				</div>
				
				{if empty($step->formula_surface)}
				<div class="alert alert-warning">
					{l s='If you use a formula for this step, it will disable your configuration on each option\'s impact price of this step.' mod='dm_advancedformula'}
				</div>
				{/if}
				
				<div class="form-group">
					<input id="formula_surface" class="formula_editor" name="formula_surface" type="hidden" value='{$step->formula_surface|escape:'htmlall':'UTF-8'}' />
				</div>
				
				{else}
				<div class="alert alert-warning">
					{l s='Surface formula is only available when your options use text fields.' mod='dm_advancedformula'}
				</div>
				{/if}
            {else}
				<div class="alert alert-warning">
					{l s='You must save this step before using a surface formula.' mod='dm_advancedformula'}
				</div>
            {/if}

            <div class="panel-footer">
				<a href="{$link->getAdminLink('AdminConfiguratorSteps')|escape:'html':'UTF-8'}&id_configurator={$id_configurator|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='dm_advancedformula'}</a>
				<button type="submit" name="submitAddconfigurator_step" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='dm_advancedformula'}</button>
				<button type="submit" name="submitAddconfigurator_stepAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='dm_advancedformula'}</button>
            </div>
        </div>
    </div>
</div>