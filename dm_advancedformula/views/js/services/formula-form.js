/**
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
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

var CONFIGURATOR = CONFIGURATOR || {};
/**
 * Gère les interactions avec le formulaire permettant
 * d'ajouter des fonctions avec un ou plusieurs arguments
 * @requires jQuery, FormulaSyntax plugin
 */
CONFIGURATOR.FormulaForm = (function($, FormulaSyntax){
	var self = {};
	var label = '';
	var el_id = {
		'form' : '#formula_form',
		'fblock_suffix' : '_block',
		'cancel_btn' : '#cancel-insert-function',
		'select' : '#function_select',
		'modal' : '#modal_configurator_formula'
	};
	var el_class = {
		'function_container' : '.function-block',
		'link_add' : '.add-arg',
		'container' : '.arguments-block',
		'container_unlimited_args' : '.arguments-block.unlimited-args',
		'wrapper' : '.argument-wrapper',
		'item' : '.argument-item',
		'item_label' : '.input-group-addon',
		'select_step' : '.select_step',
		'select_option' : '.select_option'
	};

	var bindAll = function(){
		var container = $(".adminconfiguratorsteps");
		container.on('click', el_class.link_add, function(e){
			e.preventDefault();
			self.addArgument($(this));
			return false;
		});
		container.on('submit', el_id.form, function(){
			sendFunction(serializeObject($(this).serializeArray()));
			self.reset();
			return false;
		});
		container.on('click', el_id.cancel_btn, function(){
			self.reset();
		});
		container.on('change', el_id.select, function(){
			self.reset();
			showBlock($(this).val());
		});
		container.on('change', el_class.container + ' ' + el_class.select_step, function(){
			updateSelect($(this));
		});
	};

	var showBlock = function(fKey) {
		$(el_class.function_container).hide();
		$('#'+fKey+el_id.fblock_suffix).fadeIn();
	};

	var serializeObject = function(a) {
		var o = {};
		$.each(a, function() {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

	var sendFunction = function(formData) {
		if(formData.function_select === undefined) {
			return;
		}
		var fName = formData.function_select;
		var inputName = fName + '_args';
		var args = new Array();
		if(typeof formData[inputName] === 'string') {
			formData[inputName] = new Array(formData[inputName]);
		}
		for(var i in formData[inputName]) {
			if(formData[inputName][i] !== '') {
				args.push(parseFloat(formData[inputName][i].replace(',','.')));
			}
		}
		FormulaSyntax.insert(fName, args);
		$(el_id.modal).modal('hide');
	};

	var updateSelect = function(select){
		var container = select.closest(el_class.wrapper);
		var id = select.val();
		container.find(el_class.select_option).prop('disabled', true).hide();
		container.find(el_class.select_option + '[data-parentid=' + id + ']').prop('disabled', false).show();
	};

	self.init = function(){
		bindAll();
		$(el_class.container + ' ' + el_class.select_step).each(function(){
			updateSelect($(this));
		});
		showBlock($(el_id.select).val());
	};

	self.reset = function(){
		var form = $(el_id.form);
		form.find('input').val('');
		form.find(el_class.container_unlimited_args).each(function(){
			$(this).find(el_class.item).each(function(index){
				if (index > 0) {
					$(this).remove();
				}
			});
		});
	};

	self.setLabel = function(labelI18n) {
		label = labelI18n;
	};

	self.addArgument = function(link){
		var container = link.closest(el_class.container);
		var last_item = container.find(el_class.item).last();
		var new_item = last_item.clone();
		new_item.find('input').val('');
		new_item.find(el_class.item_label).text(label + (container.find(el_class.item).length + 1));
		new_item.appendTo(container.find(el_class.wrapper));
	};

	return self;
})(jQuery, CONFIGURATOR.FormulaSyntax);