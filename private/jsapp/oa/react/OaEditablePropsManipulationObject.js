import React from 'react';

import PropTypes from 'prop-types';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import OaEditablePropsManipulationObject from "oa/react/OaEditablePropsManipulationObject";
export default class OaEditablePropsManipulationObject extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this._references = new ReferencesHolder();
		
		this._propsThatShouldNotCopy.push("editableProps");
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		//console.log("oa/react/OaEditablePropsManipulationObject::getReferences")
		return {"references": this._references};
	}
	
	updateValue(aName, aValue) {
		console.log("oa/react/OaEditablePropsManipulationObject::updateValue");
		
		var stateObject = new Object();
		stateObject[aName] = aValue;
		
		this.setState(stateObject);
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/OaEditablePropsManipulationObject::_manipulateProps");
		
		var editableProps = this.props.editableProps;
		
		if(editableProps) {
			var currentArray;
			if(typeof(editableProps) === "string") {
				currentArray = editableProps.split(","); //METODO: remove whitespace
			}
			else if(editableProps instanceof Array) {
				currentArray = editableProps;
			}
			
			if(currentArray) {
				var currentArrayLength = currentArray.length;
				for(var i = 0; i < currentArrayLength; i++) {
					var currentName = currentArray[i];
					
					if(this.state[currentName] !== undefined) {
						aReturnObject[currentName] = this.state[currentName];
					}
					
					this._references.addObject("value/" + currentName, this);
				}
			}
		}
		else {
			console.warn("No pros are set as editable.");
		}
		
		
		aReturnObject["references"] = this._references;
		
		return aReturnObject;
	}
	
	_prepareRender() {
		
		this._references.setParent(this.context.references);
		
		super._prepareRender();
	}
}

OaEditablePropsManipulationObject.childContextTypes = {
	"references": PropTypes.object
};
