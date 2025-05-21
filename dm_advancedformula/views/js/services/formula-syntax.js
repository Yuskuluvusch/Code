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
 * Helper pour les manipulations de formules
 * Intermédiaire entre le serveur et le WYSIWYG
 * et entre les scripts JS et le WYSIWYG
 * permettant de transformer la syntaxe "visuelle" en une syntaxe reconnaisable
 * par le serveur
 * @requires AdminController context
 */
CONFIGURATOR.FormulaSyntax = (function(urlValidation){
	// Private
	var formula = '';
	var editor;
	
	/**
	 * Renvoi le HTML d'une fonction à insérer dans l'éditeur
	 * @param {String} fName
	 * @param {Array} args
	 * @returns {String}
	 */
	var getFunctionHtml = function(fName, args) {
		return ' ' + fName + '(' + args.join(';') + ')';
	};
	
	// Public
    var self = {};
	
	self.syntaxValidMsg = '';
	self.syntaxInvalidMsg = '';
	
	/**
	 * Récupère une instance de tinyMCE utilisé pour les formules
	 * @param {TinyMCE.Editor} tinyMCE editor instance
	 */
	self.setEditor = function(tinyMceEditor){
		tinyMceEditor.format = 'text';
		editor = tinyMceEditor;
	};
	
	/**
	 * Insère une fonction dans l'éditeur WYSIWYG avec les paramètres
	 * @param {String} fName
	 * @param {Array} args
	 */
	self.insert = function(fName, args) {
		if (editor === undefined) {
			return;
		}
		editor.insertContent(getFunctionHtml(fName, args));
		self.update();
	};
	
	/**
	 * Met à jour la formule depuis l'éditeur
	 */
	self.update = function() {
		formula = editor.getContent({format : 'text'});
	};
	
	self.getFormula = function() {
		return formula;
	};
	
	/**
	 * Valide une formule en ajax
	 * @param {function} callback
	 */
	self.validate = function(callback) {
		if(typeof callback !== 'function') {
			callback = function(){};
		}
		
		if (formula === "") {
			callback(true);
			return;
		}
		
		// On assume être dans AdminController
		$.post(urlValidation, {
			'ajax' : 1,
			'action' : 'FormulaValidation',
			'formula' : formula
		}).done(function(success){
			if(isNaN(success)) {
				callback(false);
			} else {
				callback(parseInt(success));
			}
		});
		return;
	};
	
	// return Public properties and method
    return self;
})(currentIndex+'&token='+token);