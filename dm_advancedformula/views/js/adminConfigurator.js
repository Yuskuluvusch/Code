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
 *  @author DMConcept <support@dmconcept.fr>
 *  @copyright 2015 DMConcept
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

advancedFormulaHandler = {
    // CLASS
    formula_value_class: '.impact_formula',
    input_value_class: '.impact_value',
    select_class: '.select_impact',
    price_impact_block_class: '.price_impact',
    input_value_form_class: '.form_value',

    // Initialization
    init: function () {
        this.bindAll();
        this.update();
    },
    update: function () {
        var self = this;
        var container = $('#configurator_step_form');

        $(container).find(self.select_class).each(function () {
            self.processEditInputGroupAddon($(this));
        });
    },
    // Bind all events needed
    bindAll: function () {
        var self = this;
        var container = $('#configurator_step_form');

        container.on('change', self.select_class, function () {
            self.processEditInputGroupAddon($(this));
            self.processEditInputFormValue($(this));
        });
    },
    // Change currency or percent suffix's input group addon depending of select's option
    processEditInputGroupAddon: function (select) {
        var parent_container = select.parents(this.price_impact_block_class);
        var option_value = select.find('option:selected').val();

        parent_container.find(this.formula_value_class).closest('.form-group').hide();

        if (option_value === 'amount_formula') {
            parent_container.find(this.input_value_class).closest('.form-group').hide();
            parent_container.find(this.formula_value_class).closest('.form-group').show();
        }
    },
    processEditInputFormValue: function (el) {
        var parent_container = el.parents(this.price_impact_block_class);
        var input_form_value = parent_container.find(this.input_value_form_class);
        var select = parent_container.find(this.select_class);
        var option_value = select.find('option:selected').val();
        var value = parent_container.find(this.input_value_class).val();

        input_form_value.val(option_value + ',' + value);
    }
};


advancedFormulaCustomBlockHandler = {
    // Custom formula
    form_id: '#configurator_step_form',
    add_customformula_btn_id: '#add_customformula',
    delete_customformula_btn_class: '.delete_customformula',
    customformula_variable_input_id: '#customformula_variable',
    customformula_value_input_id: '#customformula_value',
    customformula_group_list_block_class: '.customformula_group_list',
    tmpl_customformula_row_id: '#tmpl_customformula_row',
    container_class: '.customformulas_block',

    // Initialization
    init: function () {
        this.bindAll();
    },
    // Bind all events needed
    bindAll: function () {
        var self = this;
        var container = $(self.form_id);
        
        container.on('submit', function() {
            self.processSubmit();
        });
        
        container.on('click', self.add_customformula_btn_id, function(e) {
            self.addCustomFormula($(this));
        });
        
        container.on('click', self.delete_customformula_btn_class, function(e) {
            e.preventDefault();
            self.removeCustomFormula($(this));
        });
    },
    
    addCustomFormula: function(btn, datas) {
        var self = this;
        var form_group = btn.parents('tfoot');
        var tbody = form_group.parent().find(self.customformula_group_list_block_class);
        var template = self.getCompiledTemplate(self.tmpl_customformula_row_id);

        if (datas === undefined) {
            tinyMCE.triggerSave();
            var variable = form_group.find(self.customformula_variable_input_id);
            var value = form_group.find(self.customformula_value_input_id);
            
            let id = null;
            do {
                id = parseInt(Math.random() * 1000000000) + 1000000000;
            } while($('body [data-id="' + id + '"]').length !== 0);
            
            var datas = {
                id: id,
                variable: variable.val(),
                value: value.val()
            };
        }

        if (datas.id > 0 && datas.variable.length > 0 && datas.value.length > 0) {
            tbody.append(template({
                id: datas.id,
                variable: datas.variable,
                value: datas.value
            }));
        }
    },
    removeCustomFormula: function(btn) {
        var tr = btn.parents('tr');
        tr.remove();
    },
    getCompiledTemplate: function(id) {
        return Handlebars.compile($(id).html());
    },
    processSubmit: function() {
        var self = this;
        // For each customformulas block
        $(self.container_class).each(function() {
            var container = $(this);
            var type = container.data('type');
            var type_id = container.data('id');
            var customformulas = {};
            // For each customformulas row
            $(this).find(self.customformula_group_list_block_class).find('tr').each(function() {
                var id = $(this).data('id');
                var variable = $(this).data('variable');
                var value = $(this).data('value');
                customformulas[id] = { id, variable, value };
            });
            container.append($('<input type="hidden" />').attr('name', 'customformula[' + type + '][' + type_id + ']').val(JSON.stringify(customformulas)));
        });
    },
    renderCustomFormulas: function(selector, datas) {
        var container = $(selector);
        var self = this;
        if (datas !== '') {
            var btn = container.find(self.add_customformula_btn_id);
            var form_group = btn.parents('tfoot');
            datas = JSON.parse(datas);
            for (var data of datas) {
                form_group.find(self.customformula_variable_input_id).val(data.variable);
                form_group.find(self.customformula_value_input_id).val(data.value);
                this.addCustomFormula(btn);
            }
        }
    }
};