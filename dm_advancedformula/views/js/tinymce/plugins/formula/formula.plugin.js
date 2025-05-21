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
$(function (){
    /*setTimeout(function() {*/
        tinymce.addI18n('fr_FR', {
          'Insert a function': 'Insérer une fonction',
          'Erase formula': 'Effacer la formule',
          'Check syntax validity': 'Vérifier la syntaxe',
          'Are you sure you want to erase this formula?': 'Êtes-vous sûr de vouloir effacer cette formule ?'
        });

        tinyMCE.PluginManager.add('formula', function(editor){
                if(CONFIGURATOR === undefined) { return; }
                if(CONFIGURATOR.FormulaSyntax === undefined) { return; }
                /**
                 * Ecrasement d'une formule
                 */
                editor.addButton('erase-formula', {
                        icon: 'erase',
                        title: 'Erase formula',
                        onclick: function () {
                                editor.windowManager.confirm(
                                        'Are you sure you want to erase this formula?',
                                        function(confirm){
                                                if (confirm) {
                                                        editor.setContent('');
                                                }
                                        }
                                );
                        }
                });
                /**
                 * Bouton de vérification syntaxique
                 */
                editor.addButton('check-formula', {
                        icon: 'check',
                        title: 'Check syntax validity',
                        onclick: function (event) {
                                editor.save();
                                var icon = $(event.target).find('i');
                                icon.removeClass('text-danger')
                                        .removeClass('text-success')
                                        .removeClass('mce-i-check')
                                        .removeClass('mce-i-error')
                                        .addClass('mce-i-loader');
                                // Validation de la formule
                                CONFIGURATOR.FormulaSyntax.validate(function(success){
                                        icon.removeClass('mce-i-loader');
                                        if (success) {
                                                icon.addClass('mce-i-check')
                                                        .addClass('text-success');
                                                showSuccessMessage(CONFIGURATOR.FormulaSyntax.syntaxValidMsg);
                                        } else {
                                                icon.addClass('mce-i-error')
                                                        .addClass('text-danger');
                                                showErrorMessage(CONFIGURATOR.FormulaSyntax.syntaxInvalidMsg);
                                        }
                                });
                        }
                });
                /**
                 * Insertion d'une fonction
                 * Permet d'ouvrir la modal
                 */
                editor.addButton('function-formula', {
                        icon: 'function',
                        title: 'Insert a function',
                        onclick: function() {
                                editor.save();
                                $('#modal_configurator_formula').modal('show');
                                CONFIGURATOR.FormulaSyntax.setEditor(editor);
                        }
                });
                /**
                 * A chaque modification levée on met à jour la formule
                 */
                editor.on('change', function(){
                        CONFIGURATOR.FormulaSyntax.setEditor(editor);
                        CONFIGURATOR.FormulaSyntax.update();
                });
                /**
                 * A chaque sauvegarde du contenu on met à jour la formule
                 * et on la renvoi à l'objet e permettant de modifier le contenu sauvegardé
                 * avant enregistrement
                 */
                editor.on('SaveContent', function(e){
                        CONFIGURATOR.FormulaSyntax.setEditor(editor);
                        CONFIGURATOR.FormulaSyntax.update();
                        e.content = CONFIGURATOR.FormulaSyntax.getFormula();
                });
        });
   /* }, 3000);*/
});