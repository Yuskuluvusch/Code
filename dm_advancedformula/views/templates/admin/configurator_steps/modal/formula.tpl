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

<form id="formula_form" class="form-horizontal" action="" method="post">
	<div class="modal-body">

		<div class="form-group">
			<label class="control-label col-lg-4 required" for="function_select">{l s='Choose a function' mod='dm_advancedformula'}</label>
			<div class="col-lg-8">
				<select name="function_select" id="function_select" class='chosen'>
				{foreach $functions as $key => $function}
					<option value="{$key|escape:'htmlall':'UTF-8'}">
						{$function.name|escape:'htmlall':'UTF-8'}
					</option>
				{/foreach}
				</select>
			</div>
		</div>

		{foreach $functions as $key => $function}
		<div id="{$key|escape:'htmlall':'UTF-8'}_block" class="function-block form-group">
			<div class="col-lg-11 col-lg-offset-1">
				<h4>{$key|escape:'htmlall':'UTF-8'} {l s='function' mod='dm_advancedformula'}</h4>
				<p><strong>{$function.syntax|escape:'htmlall':'UTF-8'}</strong></p>
				<p class='help-block'>
					{$function.desc|escape:'htmlall':'UTF-8'}
				</p>
			</div>
				
			{if $function.arguments neq 0}
				{if $function.arguments > 0}
					{assign var=nb_arg_field value=$function.arguments}
				{else}
					{assign var=nb_arg_field value=1}
				{/if}
				<div class="col-lg-11 col-lg-offset-1">
					<div class="arguments-block {if $function.arguments eq FormulaBuilder::UNLIMITED_ARGS}unlimited-args{/if} sub-form-group">
						<h4>{l s='Arguments:' mod='dm_advancedformula'}</h4>
						<div class="argument-wrapper">
							{for $i=1 to $nb_arg_field}
							<div class="argument-item input-group">
								<span class="input-group-addon">
									{l s='Value' mod='dm_advancedformula'}
									{if $nb_arg_field > 1 || $function.arguments eq FormulaBuilder::UNLIMITED_ARGS}{$i|intval}{/if}
								</span>
								{if ($key eq 'OPTION' or $key eq 'STEP_OPT_POSITION_SELECTED' or $key eq 'STEP_OPT_VALUE_SELECTED' or $key eq 'STEP_PRICE' or $key eq 'SUM_OPTION_QTY' or $key eq 'MAX_VALUE' or $key eq 'MAX_VALUE_SELECTED') and !empty($choices) and !empty($choices[$i-1])}
									{assign var=selects value=$choices[$i-1].selects}
									{foreach $selects as $k => $select}
									<select 
										name="{$key|escape:'htmlall':'UTF-8'}_args"
										{foreach $select.params as $attr => $param}
										{$attr|cat:"="|cat:$param|escape:'htmlall':'UTF-8'} 
										{/foreach}
									>
										{if $i-1 eq 0 and $k eq 0}
											<option value="{$step->id|intval}" selected>{l s='Current step' mod='dm_advancedformula'}</option>
										{/if}
										{foreach $select['options'] as $id => $option}
											{if $option.classname neq 'ConfiguratorStepAbstract' || $option.classname eq 'ConfiguratorStepAbstract' and $option.object->use_input}
											<option value="{$id|intval}">{$option['option']|escape:'htmlall':'UTF-8'}</option>
											{/if}
										{/foreach}
									</select>
									{/foreach}
								{else}
								<input type="text" name='{$key|escape:'htmlall':'UTF-8'}_args' class="form-control" />
								{/if}
							</div>
							{/for}
						</div>
						{if $function.arguments eq FormulaBuilder::UNLIMITED_ARGS}
						<a href="javascript:void(0);" class='add-arg btn btn-link'><i class="icon-plus-sign"></i> {l s='Add an argument' mod='dm_advancedformula'}</a>
						{/if}
					</div>
				</div>
			{/if}
		</div>
		{/foreach}
	</div>
    <div class="modal-footer">
        <button id='cancel-insert-function' type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancel' mod='dm_advancedformula'}</button>
        <button type="submit" class="btn btn-primary">{l s='Insert function' mod='dm_advancedformula'}</button>
    </div>
</form>
	
<script type="text/javascript">
$(document).on('ready', function(){
	if (CONFIGURATOR.FormulaForm === undefined) {
		alert('[FATAL ERROR] CONFIGURATOR.FormulaForm NOT FOUND! Formula form feature is disabled.');
		return;
	}
	// Launch formula form
	CONFIGURATOR.FormulaForm.setLabel('{l s='Value' mod='dm_advancedformula' js=1} ');
	CONFIGURATOR.FormulaForm.init();
});
</script>