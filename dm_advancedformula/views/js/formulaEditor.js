formulaEditor = {
	init: function() {
		if (typeof ad === "undefined") {
			return;
		}
		console.log('init formulaEditor');
		if (CONFIGURATOR.FormulaSyntax === undefined) {
			alert('[FATAL ERROR] CONFIGURATOR.FormulaSyntax NOT FOUND! Formula feature is disabled.');
			return;
		}

        tinyMCE.EditorManager.on('AddEditor', function(data) {
        	// FIX : suppression des classes utilisées pour l'initialisation de l'éditeur
			// car on rafraichie l'éditeur en AJAX
            $('#' + data.editor.id).removeClass("formula_editor");
            $('#' + data.editor.id).removeClass("autoload_rte");

            if (data.editor.id === 'formula' || data.editor.id === 'formula_surface') {
                CONFIGURATOR.FormulaSyntax.setEditor(data.editor);
            }
        });

		CONFIGURATOR.FormulaSyntax.syntaxValidMsg = "{l s='This formula is valid!' mod='dm_advancedformula' js=1}";
		CONFIGURATOR.FormulaSyntax.syntaxInvalidMsg = "{l s='This formula is not valid!' mod='dm_advancedformula' js=1}";

		var config = {
			selector: ".formula_editor",
			plugins : "formula,autosave",
			toolbar: "undo redo | function-formula | erase-formula | check-formula",
			toolbar1: "",
			toolbar2: "",
			browser_spellcheck: false,
			menubar : false,
			statusbar: false,
			// Define in CSS !important
			height: "35px"
		};
		tinySetup(config);
	}
};

$(document).ready(function(){
	formulaEditor.init();
});