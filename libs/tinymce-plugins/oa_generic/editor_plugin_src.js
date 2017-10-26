(function() {
	var DOM = tinymce.DOM;

	tinymce.create("tinymce.plugins.OA_generic", {
		init : function(aEditor) {
			//console.log("tinymce.plugins.OA_generic::init");
			//console.log(aEditor);
			
			aEditor.on('init', function(aArguments) {
				//console.log("tinymce.plugins.OA_generic::init aEditor::init");
				//console.log(aArguments)
				
				window.OA.mceEditorMananger.registerEditor(aEditor);
			});
			
		},
		
		getInfo : function() {
			return {
				longname : 'Odd Alice generic integration',
				author : 'Odd Alice',
				authorurl : 'http://oddalice.se/',
				infourl : 'http://oddalice.se/',
				version : '1.0.0'
			};
		}
		
	});

	// Register plugin
	tinymce.PluginManager.add('oa_generic', tinymce.plugins.OA_generic);
})();