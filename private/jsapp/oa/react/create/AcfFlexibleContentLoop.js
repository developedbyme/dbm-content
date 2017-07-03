import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import SourceData from "oa/reference/SourceData";

//import AcfFlexibleContentLoop from "oa/react/create/AcfFlexibleContentLoop";
export default class AcfFlexibleContentLoop extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/create/AcfFlexibleContentLoop::_removeUsedProps");
		
		delete aReturnObject["data"];
		delete aReturnObject["input"];
		delete aReturnObject["output"];
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/create/AcfFlexibleContentLoop::_manipulateProps");
		
		var references = this.getReferences();
		
		var inputName = this.props.input ? this.props.input : "data";
		var outputName = this.props.output ? this.props.output : "dynamicChildren";
		
		var returnArray = new Array();
		
		var currentArray = SourceData.getSourceWithType(this.props[inputName], this);
		if(currentArray) {
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentData = currentArray[i];
				var currentTemplate = currentData.selectedTemplate;
				
				var contentCreatorPath = "contentCreators/acfFlexibleContent/" + currentTemplate;
				var contentCreator = references.getObject(contentCreatorPath);
				if(!contentCreator) {
					console.warn("No content creator for template " + currentTemplate);
					console.log(this);
					
					continue;
				}
				
				contentCreator(currentData.value, i, references, returnArray);
			}
		}
		else {
			console.error("Data for loop not set correctly.");
			console.log(this);
		}
		
		aReturnObject[outputName] = returnArray;
		
		return aReturnObject;
	}
}