import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import SourceData from "oa/reference/SourceData";

//import Loop from "oa/react/create/Loop";
export default class Loop extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/create/Loop::_removeUsedProps");
		
		delete aReturnObject["data"];
		delete aReturnObject["input"];
		delete aReturnObject["output"];
		
		return aReturnObject;
	}
	
	_v1_manipulateProps(aReturnObject) {
		var references = this.getReferences();
		
		var inputName = this.props.input ? this.props.input : "data";
		var outputName = this.props.output ? this.props.output : "dynamicChildren";
		
		var returnArray = new Array();
		
		var contentCreatorPath = this.props.contentCreator ? this.props.contentCreator : "contentCreators/loop";
		var contentCreator = references.getObject(contentCreatorPath);
		
		var spacingContentCreator = null;
		if(this.props.spacingContentCreator) {
			spacingContentCreator = references.getObject(this.props.spacingContentCreator);
		}
		
		if(!contentCreator) {
			console.error("Loop doesn't have content creator " + contentCreatorPath, this);
			
			aReturnObject[outputName] = returnArray;
			return aReturnObject;
		}
		
		var currentArray = SourceData.getSourceWithType(this.props[inputName], this);
		if(currentArray) {
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentData = currentArray[i];
				
				if(spacingContentCreator !== null && i !== 0) {
					spacingContentCreator(null, i, references, returnArray);
				}
				
				contentCreator(currentData, i, references, returnArray);
			}
		}
		else {
			console.error("Data for loop not set correctly.", this);
		}
		
		aReturnObject[outputName] = returnArray;
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/create/Loop::_manipulateProps");
		
		if(this.props.v != "2") {
			console.log("Upgrade loop to v2", this);
			return this._v1_manipulateProps(aReturnObject);
		}
		
		
		var references = this.getReferences();
		
		
		
		var returnArray = new Array();
		
		var contentCreator = this.getSourcedProp("contentCreator");
		
		var spacingContentCreator = null;
		if(this.props.spacingContentCreator) {
			spacingContentCreator = this.getSourcedProp("spacingContentCreator");
		}
		
		if(!contentCreator) {
			console.error("Loop doesn't have content creator.", this);
			
			aReturnObject[outputName] = returnArray;
			return aReturnObject;
		}
		
		var currentArray = this.getSourcedProp("input");
		if(currentArray) {
			var currentArrayLength = currentArray.length;
			if(currentArrayLength > 0) {
				for(var i = 0; i < currentArrayLength; i++) {
					var currentData = currentArray[i];
				
					if(spacingContentCreator !== null && i !== 0) {
						spacingContentCreator(null, i, references, returnArray);
					}
					
					contentCreator(currentData, i, references, returnArray);
				}
			}
			else {
				var noItemsContentCreator = this.getSourcedProp("noItemsContentCreator");
				if(noItemsContentCreator) {
					noItemsContentCreator(currentArray, 0, references, returnArray);
				}
			}
		}
		else {
			console.error("Data for loop not set correctly.", this);
		}
		
		var outputName = this.props.output ? this.props.output : "dynamicChildren";
		aReturnObject[outputName] = returnArray;
		
		return aReturnObject;
	}
}