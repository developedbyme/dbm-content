import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import SourceData from "oa/reference/SourceData";

//import DataInjection from "oa/react/DataInjection";
export default class DataInjection extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/DataInjection::_removeUsedProps");
		
		delete aReturnObject["injectData"];
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/DataInjection::_manipulateProps");
		
		var references = this.getReferences();
		
		var injectData = this.props.injectData;
		for(var objectName in injectData) {
			var sourcedData = SourceData.getSourceWithType(injectData[objectName], this);
			aReturnObject[objectName] = sourcedData;
		}
		
		return aReturnObject;
	}
}
