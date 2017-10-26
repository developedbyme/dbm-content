"use strict";

// import ShortcodeEditorConnection from "dbmcontent/admin/editor/ShortcodeEditorConnection";
export default class ShortcodeEditorConnection {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("dbmcontent.admin.editor.ShortcodeEditorConnection::constructor");
		
		this._editor = null;
		
		this._callback_contentUpdated_bound = this._callback_contentUpdated.bind(this);
	}
	
	/**
	 * Sets the editor
	 *
	 * @param	aEditor	tinymce.Editor	The editor to control.
	 *
	 * @return	self
	 */
	setEditor(aEditor) {
		//console.log("dbmcontent.admin.editor.ShortcodeEditorConnection::setEditor");
		//console.log(aEditor);
		
		this._editor = aEditor;
		
		return this;
	}
	
	/**
	 * Callback when content is updated.
	 */
	_callback_contentUpdated(aEvent) {
		console.log("dbmcontent.admin.editor.ShortcodeEditorConnection::_callback_contentUpdated");
		//console.log(aEvent);
		
		/*
		var rootNode = this._editor.dom.getRoot();
		
		var hasManualPlacement = false;
		
		var currentContent = this._editor.getContent();
		var adSpaceRegExp = new RegExp("\\[adSpace[^\\]]*\\]", "gm");
		var placementRegexp = new RegExp("\\bplacement=\"([^\"]*)\"", "");
		while(true) {
			var adSpaceMatches = adSpaceRegExp.exec(currentContent);
			
			if(adSpaceMatches !== null) {
				var placementResult = placementRegexp.exec(adSpaceMatches[0]);
				if(placementResult === null || placementResult[1] !== "auto") {
					hasManualPlacement = true;
					break;
				}
			}
			else {
				break;
			}
		}
		
		if(hasManualPlacement) {
			//console.log("Post has manual spaces");
			return;
		}
		
		var currentAdSpaces = new Array();
		var currentArray = rootNode.childNodes;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentNode = currentArray[i];
			if(currentNode.nodeType === 3 && currentNode.nodeValue === "[adSpace placement=\"auto\"]") {
				currentAdSpaces.push(currentArray[i]);
			}
			else if(currentNode.nodeType === 1 && currentNode.innerHTML === "[adSpace placement=\"auto\"]") {
				currentAdSpaces.push(currentArray[i]);
			}
			else if(currentNode.nodeType === 1 && currentNode.getAttribute("data-wpview-type") === "adSpace") {
				currentAdSpaces.push(currentArray[i]);
			}
			
		}
		
		var countData = this._calculator.getInsertPositionsForElement(rootNode);
		var hasChanges = false;
		
		var currentArray = countData;
		var currentArrayLength = currentArray.length;
		
		for(var i = 0; i < currentArrayLength; i++) {
			var newNode = this._calculator.createAdSpaceNode(rootNode.ownerDocument);
			var currentCountData = currentArray[i];
			
			var nextSibling = currentCountData.node.nextSibling;
			var nextSiblingIsAdSpace = false;
			var currentArray2 = currentAdSpaces;
			var currentArray2Length = currentArray2.length;
			for(var j = 0; j < currentArray2Length; j++) {
				if(currentArray2[j] === nextSibling) {
					currentArray2.splice(j, 1);
					nextSiblingIsAdSpace = true;
					break;
				}
			}
			if(!nextSiblingIsAdSpace) {
				this._editor.dom.insertAfter(newNode, currentCountData.node);
				hasChanges = true;
			}
		}
		
		var currentArray = currentAdSpaces;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			this._editor.dom.setOuterHTML(currentArray[i], "");
			hasChanges = true;
		}
		
		if(hasChanges) {
			//METODO: render shortcode displays
			
			//MENOTE: this does not solve the problem
			//wp.mce.views.setMarkers(this._editor.getContent());
			//wp.mce.views.render();
			
			//METODO: this works but restes the position
			//this._editor.setContent(this._editor.getContent());
		}
		*/
	}
	
	/**
	 * Starts the checking of the editor
	 *
	 * @return	self
	 */
	start() {
		console.log("dbmcontent.admin.editor.ShortcodeEditorConnection::start");
		
		this._editor.on("change", this._callback_contentUpdated_bound);
		this._editor.on("focus", this._callback_contentUpdated_bound);
		this._editor.on("blur", this._callback_contentUpdated_bound);
		
		//this._callback_contentUpdated();
		//this._editor.setContent(this._editor.getContent()); //METODO: force update to show preview
		
		return this;
	}
}