import React, {Component} from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import FilterProps from "oa/react/FilterProps";
export default class FilterProps extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
	}
	
	_getPropsToCopy() {
		var returnArray = new Array();
		var propSettings = this.props.propsToCopy;
		if(propSettings) {
			if(Array.isArray(propSettings)) {
				returnArray = returnArray.concat(propSettings);
			}
			else {
				returnArray = propSettings.split(",");
			}
		}
		
		return returnArray;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/FilterProps::_manipulateProps");
		
		var returnObject = new Object();
		
		var currentArray = this._getPropsToCopy();
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentPropName = currentArray[i];
			returnObject[currentPropName] = aReturnObject[currentPropName];
		}
		
		return returnObject;
	}
}
