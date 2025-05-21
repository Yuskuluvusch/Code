<div class="form-group" {if $configuratorStepOption and $configuratorStepOption->impact_type neq 'amount_formula'}style='display: none;'{/if}>
	<div class='col-lg-12 alert alert-warning'>
		{l s='The formula will not be checked, you have to be careful on what you are entering.' mod='dm_advancedformula'}
	</div>
	<label class="control-label col-lg-4 required" for="impact_formula_{$id|escape:'htmlall':'UTF-8'}">{l s='Amount formula' mod='dm_advancedformula'}</label>
	<div class="col-lg-7">
		<input type='text' id='impact_formula_{$id|escape:'htmlall':'UTF-8'}' class="impact_formula formula_editor" type='text' name='impact_formula_{$id|escape:'htmlall':'UTF-8'}' value="{$configuratorStepOption->impact_formula|escape:'html':'UTF-8'}"/>
	</div>
</div>
	
	
<script>
	advancedFormulaHandler.init();
</script>