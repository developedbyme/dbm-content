var objectPath = require("object-path");

//import TextManager from "oa/textmanager/TextManager";
export default class TextManager {
	
	constructor() {
		//console.log("oa/textmanager/TextManager::constructor");
		
		this._data = null;
	}
	
	setData(aDataObject) {
		this._data = aDataObject;
	}
	
	getText(aPath) {
		//console.log("oa/textmanager/TextManager::getText");
		
		var returnText = objectPath.get(this._data, aPath);
		
		if(returnText == undefined) {
			console.warn("No text for path " + aPath);
			return null;
		}
		
		return returnText;
	}
}