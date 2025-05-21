{assign var=id value=$configurator_step->id}

<div class="panel configurator-steps-tab">
    <h3 class="tab">
        <i class="icon-calculator"></i>
        {l s='Custom formula' mod='dm_advancedformula'}
    </h3>

    {if !Validate::isLoadedObject($configurator_step)}
        <div class="alert alert-warning">
            {l s='You must save this step before configuring filters.' mod='configurator'}
        </div>
    {else}
        <div id="customformulas_block_{$type|escape:'htmlall':'UTF-8'}_{$id|escape:'htmlall':'UTF-8'}" class="customformulas_block formulas_{$type|escape:'htmlall':'UTF-8'}_block" data-type="{$type|escape:'htmlall':'UTF-8'}" data-id="{$id|escape:'htmlall':'UTF-8'}">
            <div id="customformulas_{$type|escape:'htmlall':'UTF-8'}_{$id|escape:'htmlall':'UTF-8'}">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{l s='Variable' mod='dm_advancedformula'}</th>
                            <th>{l s='Formula' mod='dm_advancedformula'}</th>
                            <th>{l s='Actions' mod='dm_advancedformula'}</th>
                        </tr>
                    </thead>
                    <tbody id="customformula_group_list_{$type|escape:'htmlall':'UTF-8'}_{$id|escape:'htmlall':'UTF-8'}" class="customformula_group_list">
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input id="customformula_variable" type="text">
                            </td>
                            <td>
                                <input id="customformula_value" class="form-control formula_editor" />
                            </td>
                            <td>
                                <button id="add_customformula" type="button" class="btn btn-default">
                                    <i class="icon-plus-sign"></i> {l s='Add' mod='advancedformula'}
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    {/if}

    <div class="panel-footer">
        <a
            href="{$link->getAdminLink('AdminConfiguratorSteps')|escape:'html':'UTF-8'}&id_configurator={$configurator_step->id_configurator|escape:'htmlall':'UTF-8'}"
            class="btn btn-default"
            >
            <i class="process-icon-cancel"></i>
            {l s='Cancel' mod='advancedformula'}
        </a>
        <button type="submit" name="submitAddconfigurator_step" class="btn btn-default pull-right">
            <i class="process-icon-save"></i>
            {l s='Save' mod='advancedformula'}
        </button>
        <button type="submit" name="submitAddconfigurator_stepAndStay" class="btn btn-default pull-right">
            <i class="process-icon-save"></i>
            {l s='Save and stay' mod='advancedformula'}
        </button>
    </div>
</div>

{literal}
    <script id="tmpl_customformula_row" type="text/x-handlebars-template">
        <tr class="customformula_row" data-id="{{id}}" data-variable="{{variable}}" data-value="{{value}}">
            <td>{{variable}}</td>
            <td>{{value}}</td>
            <td>
                <button type="button" class="btn btn-default delete_customformula">
                    <i class="icon-remove"></i>
                    {/literal}{l s='Delete' mod='advancedformula'}{literal}
                </button>
            </td>
        </tr>
    </script>
{/literal}

<script type="text/javascript">
    (function($) {
        $(function() {
            advancedFormulaCustomBlockHandler.init();
            advancedFormulaCustomBlockHandler.renderCustomFormulas('#customformulas_block_{$type|escape:'htmlall':'UTF-8'}_{$id|escape:'htmlall':'UTF-8'}', '{$customFormulasStepValues}'); {* $values is JSON data, no escape necessary *}
        });
    })(jQuery);
</script>