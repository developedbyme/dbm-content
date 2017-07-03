"use strict";

var objectPath = require("object-path");

import PostData from "oa/mrouter/data/PostData";

import AcfFunctions from "oa/mrouter/utils/AcfFunctions";

// import SourceData from "oa/reference/SourceData";
export default class SourceData {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa/reference/SourceData::constructor");
		
		this._type = null;
		this._path = null;
	}
	
	setup(aType, aPath) {
		
		this._type = aType;
		this._path = aPath;
		
		return this;
	}
	
	getSource(aFromObject) {
		return SourceData.getSource(this._type, this._path, aFromObject, aFromObject);
	}
	
	getSourceInStateChange(aFromObject, aNewPropsAndState) {
		return SourceData.getSource(this._type, this._path, aFromObject, aNewPropsAndState);
	}
	
	static create(aType, aPath) {
		var newSourceData = new SourceData();
		
		newSourceData.setup(aType, aPath);
		
		return newSourceData;
	}
	
	static getSource(aType, aPath, aFromObject, aPropsAndState) {
		
		var references = aFromObject.getReferences();
		
		switch(aType) {
			case "prop":
				return objectPath.get(aPropsAndState.props, aPath);
			case "acf":
				return references.getObject("mRouter/postData").getAcfData(aPath);
			case "acfRow":
				var rowObject = references.getObject("mRouter/postData/acfRow");
				return AcfFunctions.getAcfSubfieldData(rowObject, aPath);
			case "text":
				return references.getObject("oa/textManager").getText(aPath);
			case "postData":
				var dataObject = references.getObject("mRouter/postData");
				switch(aPath) {
					case "title":
						return dataObject.getTitle();
					case "excerpt":
						return dataObject.getExcerpt();
					case "content":
						return dataObject.getContent();
					case "permalink":
						return dataObject.getPermalink();
					case "image":
						return dataObject.getImage();
					default:
						console.error("Unknown postData type " + aPath);
						break;
				}
				break;
			case "reference":
				return references.getObject(aPath);
			default:
				console.error("Unknown type " + aType);
				break;
		}
		
		return null;
	}
	
	static getSourceWithType(aPrefixedPath, aFromObject) {
		if(!aPrefixedPath) {
			console.error("Path is not set");
			console.log(aFromObject);
			
			return null;
		}
		
		var type = "prop";
		var path = aPrefixedPath;
		var colonIndex = path.indexOf(":");
		
		if(colonIndex !== -1) {
			type = path.substring(0, colonIndex);
			path = path.substring(colonIndex+1, path.length);
		}
		
		return SourceData.getSource(type, path, aFromObject, aFromObject);
	}
}