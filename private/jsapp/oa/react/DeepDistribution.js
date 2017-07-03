import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import DistributionTarget from "oa/react/DistributionTarget";

import SourceData from "oa/reference/SourceData";

//import DeepDistribution from "oa/react/DeepDistribution";
export default class DeepDistribution extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
	}
	
	_deepDistribution(aComponent, aProps) {
		//console.log("oa/react/DeepDistribution::_deepDistribution");
		
		//this._performClone(firstChild, this._getMainElementProps());
		
		if(aComponent.type === DistributionTarget) {
			return this._performClone(aComponent, aProps);
		}
		
		if(aComponent.props && aComponent.props.children) {
			var hasDistribution = false;
			var returnArray = new Array();
			var currentArray = this._getInputChildrenForComponent(aComponent);
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentChild = currentArray[i];
				var replacedDistribution = this._deepDistribution(currentChild, aProps);
				if(replacedDistribution) {
					hasDistribution = true;
					returnArray.push(replacedDistribution);
				}
				else {
					returnArray.push(currentChild);
				}
			}
			
			if(hasDistribution) {
				return this._performCloneWithNewChildren(aComponent, aComponent.props, returnArray);
			}
		}
		
		return null;
	}
	
	_createClonedElement() {
		//console.log("oa/react/DeepDistribution::_createClonedElement");
		
		var mainElementProps = this._getMainElementProps();
		
		var returnArray = new Array();
		var currentArray = this._getInputChildrenForComponent(this);
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentChild = currentArray[i];
			var replacedDistribution = this._deepDistribution(currentChild, mainElementProps);
			if(replacedDistribution) {
				returnArray.push(replacedDistribution);
			}
			else {
				returnArray.push(currentChild);
			}
		}
		
		if(returnArray.length === 0) {
			this._clonedElement = null;
		}
		else if(returnArray.length === 1) {
			this._clonedElement = returnArray[0];
		}
		else {
			var callArray = [this._getMainElementType(), {}];
			callArray = callArray.concat(returnArray);
			
			this._clonedElement = React.createElement.apply(React, callArray);
		}
	}
}
