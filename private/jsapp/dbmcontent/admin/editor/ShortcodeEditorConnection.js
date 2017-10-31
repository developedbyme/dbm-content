"use strict";

// import ShortcodeEditorConnection from "dbmcontent/admin/editor/ShortcodeEditorConnection";
export default class ShortcodeEditorConnection {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("dbmcontent.admin.editor.ShortcodeEditorConnection::constructor");
		
		this._editor = null;
		
		this._shortcodes = {};
		this._currentShortcodeIds = new Array();
		
		this._adminUpdateBound = this._adminUpdate.bind(this);
		this._callback_contentUpdated_bound = this._callback_contentUpdated.bind(this);
	}
	
	_adminUpdate(aNewData) {
		//console.log("dbmcontent.admin.editor.ShortcodeEditorConnection::_adminUpdate");
		//console.log(aNewData);
		
		var shortcodes = aNewData.dataObject.shortcodes;
		for(var objectName in shortcodes) {
			var newData = shortcodes[objectName];
			
			var currentShortcode = this._shortcodes[objectName];
			if(currentShortcode) {
				if(currentShortcode.type !== newData.type) {
					this._setShortcodeAttribute(objectName, "type", newData.type);
				}
				
				var encodedData = this._encodeData(newData.data);
				
				if(encodedData !== currentShortcode.encodedData) {
					this._setShortcodeAttribute(objectName, "data", encodedData);
				}
			}
		}
	}
	
	_setShortcodeAttribute(aId, aAttribute, aValue) {
		
		var currentContent = this._editor.getContent();
		
		var wprrShortcodeRegExp = new RegExp("\\[wprr-component[^\\[\\]]*\\]", "gm");
		var shortcodeIdRegExp = new RegExp(" shortcodeId=\\\"([^\"]*)\\\"", "");
		
		while(true) {
			var matches = wprrShortcodeRegExp.exec(currentContent);
			
			if(matches !== null) {
				var currentShortcode = matches[0];
				
				var currentShortcodeId;
				var idResult = currentShortcode.match(shortcodeIdRegExp);
				if(idResult !== null) {
					currentShortcodeId = idResult[1];
					
					if(currentShortcodeId === aId) {
						var attributeRegExp = new RegExp(" " + aAttribute + "=\\\"([^\"]*)\\\"", "");
						
						var attributeResult = currentShortcode.match(attributeRegExp);
						
						if(attributeResult) {
							var newShortcode = currentShortcode.replace(attributeRegExp, " " + aAttribute + "=\"" + aValue + "\"");
							var newContent = currentContent.replace(currentShortcode, newShortcode);
							this._editor.setContent(newContent);
						}
						else {
							newShortcode = currentShortcode.substring(0, currentShortcode.length-1) + " " + aAttribute + "=\"" + aValue + "\"]";
							var newContent = currentContent.replace(currentShortcode, newShortcode);
							this._editor.setContent(newContent);
						}
					}
				}
			}
			else {
				break;
			}
		}
	}
	
	_encodeData(aData) {
		if(!aData) {
			return null;
		}
		
		var string = JSON.stringify(aData);
		return string.split("\"").join("&quot;").split("[").join("&#91;").split("]").join("&#93;").split(" ").join("&#32;").split("&").join("&amp;");
	}
	
	_decodeData(aString) {
		if(!aString) {
			return null;
		}
		
		return JSON.parse(aString.split("&amp;").join("&").split("&quot;").join("\"").split("&#91;").join("[").split("&#93;").join("]").split("&#32;").join(" "));
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
		
		this._scanForChanges();
		
		return this;
	}
	
	_scanForChanges() {
		var currentContent = this._editor.getContent();
		
		console.log(this._editor);
		console.log(currentContent);
		
		var wprrShortcodeRegExp = new RegExp("\\[wprr-component[^\\[\\]]*\\]", "gm");
		var shortcodeIdRegExp = new RegExp(" shortcodeId=\\\"([^\"]*)\\\"", "");
		var typeRegExp = new RegExp(" type=\\\"([^\"]*)\\\"", "");
		var dataRegExp = new RegExp(" data=\\\"([^\"]*)\\\"", "");
		
		var updatedShortcodeIds = new Array();
		
		var hasInjection = false;
		
		while(true) {
			var matches = wprrShortcodeRegExp.exec(currentContent);
			
			if(matches !== null) {
				var currentShortcode = matches[0];
				
				var currentShortcodeId;
				var idResult = currentShortcode.match(shortcodeIdRegExp);
				if(idResult !== null) {
					currentShortcodeId = idResult[1];
				}
				else {
					currentShortcodeId = "id"+Math.round(100000*Math.random()); //METODO: generate better ids
					
					hasInjection = true;
					
					var insertPosition = matches.index + ("[wprr-component").length;
					var firstPart = currentContent.substring(0, insertPosition);
					var secondPart = currentContent.substring(insertPosition, currentContent.length);
					
					currentContent = firstPart + " shortcodeId=\"" + currentShortcodeId + "\"" + secondPart;
				}
				
				updatedShortcodeIds.push(currentShortcodeId);
				
				var typeResult = currentShortcode.match(typeRegExp);
				var dataResult = currentShortcode.match(dataRegExp);
				
				this._shortcodes[currentShortcodeId] = {
					"type": (typeResult ? typeResult[1] : null),
					"encodedData": (dataResult ? dataResult[1] : null)
				}
			}
			else {
				break;
			}
		}
		
		if(hasInjection) {
			this._editor.setContent(currentContent);
		}
		
		this._currentShortcodeIds = updatedShortcodeIds;
		
		var currentArray = this._currentShortcodeIds;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentId = currentArray[i];
			var currentData = this._shortcodes[currentId];
			
			var decodedData = this._decodeData(currentData.encodedData);
			
			window.OA.wpAdminManager.addShortcode(currentId, currentData.type, decodedData);
		}
	}
	
	/**
	 * Callback when content is updated.
	 */
	_callback_contentUpdated(aEvent) {
		console.log("dbmcontent.admin.editor.ShortcodeEditorConnection::_callback_contentUpdated");
		//console.log(aEvent);
		
		this._scanForChanges();
		
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
		
		window.OA.wpAdminManager.subscribe(this._adminUpdateBound);
		
		//this._callback_contentUpdated();
		//this._editor.setContent(this._editor.getContent()); //METODO: force update to show preview
		
		return this;
	}
}