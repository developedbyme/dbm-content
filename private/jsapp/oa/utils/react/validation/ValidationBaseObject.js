import React from "react";

import PropTypes from 'prop-types';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";

// import ValidationBaseObject from "oa/utils/react/validation/ValidationBaseObject";
export default class ValidationBaseObject extends OaDataManipulationBaseObject {

	constructor(props) {
		super(props);
		
		this.state["valid"] = ValidationBaseObject.NOT_VALIDATED;
		
		this._references = new ReferencesHolder();
		
		this._references.addObject("validation/validate", this);
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		return {"references": this._references};
	}
	
	componentWillMount() {
		var validationController = this.getReference("validation/form");
		if(validationController) {
			validationController.addValidation(this);
		}
	}
	
	componentWillUnmount() {
		var validationController = this.getReference("validation/form");
		if(validationController) {
			validationController.removeValidation(this);
		}
	}
	
	componentWillReceiveProps(aNextProps) {
		console.log("oa/utils/react/validation/ValidationBaseObject::componentWillReceiveProps");
		this._validateWithProps("change", aNextProps);
	}
	
	validate(aType) {
		return this._validateWithProps(aType, this.props);
	}
	
	_validateWithProps(aType, aProps) {
		console.log("oa/utils/react/validation/ValidationBaseObject::_validateWithProps");
		
		var statePropsObject = {"state": this.state, "props": aProps};
		
		var checkValue = this.getSourcedPropInStateChange("check", statePropsObject);
		var validationFunction = this.getSourcedPropInStateChange("validateFunction", statePropsObject);
		
		console.log(checkValue);
		
		var newValid = this.state["valid"];
		if(aType === "focus") {
			if(this.state["valid"] === -1) {
				newValid = 0;
			}
		}
		else if(aType === "blur") {
			var validateOnBlur = this.getSourcedPropInStateChange("validateOnBlur", statePropsObject);
			if(validateOnBlur) {
				newValid = validationFunction(checkValue) ? 1 : -1;
			}
			
			if(validateOnBlur === ValidationBaseObject.VALIDATE_ONLY_TRUE && newValid === -1) {
				newValid = 0;
			}
		}
		else if(aType === "change") {
			var validateOnChange = this.getSourcedPropInStateChange("validateOnChange", statePropsObject);
			if(validateOnChange) {
				newValid = validationFunction(checkValue) ? 1 : -1;
			}
			
			if(validateOnChange === ValidationBaseObject.VALIDATE_ONLY_TRUE && newValid === -1) {
				newValid = 0;
			}
		}
		else if(aType === "submit") {
			newValid = validationFunction(checkValue) ? 1 : -1;
		}
		
		if(newValid !== this.state["valid"]) {
			this.setState({"valid": newValid});
		}
		
		return (newValid >= 0);
	}
	
	_manipulateProps(aReturnObject) {
		console.log("oa/utils/react/validation/ValidationBaseObject::_manipulateProps");
		
		aReturnObject["valid"] = this.state["valid"];
		
		return aReturnObject;
	}
	
	_prepareRender() {
		super._prepareRender();
		
		this._references.setParent(this.context.references);
	}
}

ValidationBaseObject.childContextTypes = {
	"references": PropTypes.object
};

ValidationBaseObject.VALID = 1;
ValidationBaseObject.NOT_VALIDATED = 0;
ValidationBaseObject.INVALID = -1;

ValidationBaseObject.VALIDATE_ONLY_TRUE = 2;
