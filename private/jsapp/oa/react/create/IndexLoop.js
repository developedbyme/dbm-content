import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import SourceData from "oa/reference/SourceData";

//import IndexLoop from "oa/react/create/IndexLoop";
export default class IndexLoop extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/create/IndexLoop::_removeUsedProps");
		
		delete aReturnObject["data"];
		delete aReturnObject["numberOfItems"];
		delete aReturnObject["input"];
		delete aReturnObject["output"];
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/create/IndexLoop::_manipulateProps");
		
		var references = this.getReferences();
		
		var inputName = this.props.input ? this.props.input : "numberOfItems";
		var outputName = this.props.output ? this.props.output : "dynamicChildren";
		
		var returnArray = new Array();
		
		var contentCreatorPath = this.props.contentCreator ? this.props.contentCreator : "contentCreators/loop";
		var contentCreator = references.getObject(contentCreatorPath);
		
		var spacingContentCreator = null;
		if(this.props.spacingContentCreator) {
			spacingContentCreator = references.getObject(this.props.spacingContentCreator);
			console.log(">>>>>", spacingContentCreator);
		}
		
		if(!contentCreator) {
			console.error("IndexLoop doesn't have content creator " + contentCreatorPath, this);
			
			aReturnObject[outputName] = returnArray;
			return aReturnObject;
		}
		
		var numberOfItems = this.props[inputName];
		
			for(var i = 0; i < numberOfItems; i++) {
				
				if(spacingContentCreator !== null && i !== 0) {
					spacingContentCreator(null, i, references, returnArray);
				}
				
				contentCreator({"index": i, "data": this.props.data}, i, references, returnArray);
			}
		
		aReturnObject[outputName] = returnArray;
		
		return aReturnObject;
	}
}