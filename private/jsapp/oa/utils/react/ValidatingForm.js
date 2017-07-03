import React from "react";
import ReactDOM from 'react-dom';

import OaBaseComponent from "oa/react/OaBaseComponent";
import ReferenceInjection from "oa/react/ReferenceInjection";

// import ValidatingForm from "oa/utils/react/ValidatingForm";
export default class ValidatingForm extends OaBaseComponent {

	constructor( props ) {
		super( props );
		
		this._elementsToValidate = new Array();
		this._mainElementType = "form";
		
		this._callback_submitBound = this._callback_submit.bind(this);
	}
	
	addValidation(aObject) {
		console.log("oa/utils/react/ValidatingForm::addValidation");
		this._elementsToValidate.push(aObject);
	}
	
	removeValidation(aObject) {
		console.log("oa/utils/react/ValidatingForm::removeValidation");
		//METODO
	}
	
	validate() {
		var returnValue = true;
		var currentArray = this._elementsToValidate;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentFieldIsValid = currentArray[i].validate("submit");
			
			returnValue &= currentFieldIsValid;
		}
		
		return returnValue;
	}
	
	_callback_submit(aEvent) {
		console.log("oa/utils/react/ValidatingForm::_callback_submit");
		console.log(aEvent);
		
		if(!this.validate()) {
			aEvent.preventDefault();
		}
		else {
			if(this.props.onSubmit) {
				this.props.onSubmit(aEvent);
			}
		}
	}
	
	submit() {
		console.log("oa/utils/react/ValidatingForm::submit");
		
		if(this.validate()) {
			ReactDOM.findDOMNode(this).submit();
		}
	}
	
	_copyPassthroughProps(aReturnObject) {
		
		if(this.props["action"]) {
			aReturnObject["action"] = this.props["action"];
		}
		if(this.props["method"]) {
			aReturnObject["method"] = this.props["method"];
		}
	}
	
	_getMainElementProps() {
		var returnObject = super._getMainElementProps();
		
		returnObject["onSubmit"] = this._callback_submitBound;
		
		return returnObject;
	}
	
	_renderMainElement() {
		//console.log("oa/utils/react/ValidatingForm::_renderMainElement");
		
		return <wrapper>
			<ReferenceInjection injectData={{"validation/form": this}} v="2">
				<div>
					{this.props.children}
				</div>
			</ReferenceInjection>
		</wrapper>;
	}

}
