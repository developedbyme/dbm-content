import React from "react";

import OaBaseComponent from "oa/react/OaBaseComponent";

// import FormField from "oa/utils/react/FormField";
export default class FormField extends OaBaseComponent {

	constructor( props ) {
		super( props );
		
		this._mainElementType = "input";
		
		this._callback_changeBound = this._callback_change.bind(this);
		this._callback_blurBound = this._callback_blur.bind(this);
		this._callback_focusBound = this._callback_focus.bind(this);
	}
	
	_callback_change(aEvent) {
		console.log("oa/utils/react/FormField::_callback_change");
		console.log(aEvent);
		console.log(aEvent.target.value);
		
		this.getReference("value/" + this.props.valueName).updateValue(this.props.valueName, aEvent.target.value, this.props.additionalData);
	}
	
	_validate(aType) {
		var validation = this.getReference("validation/validate");
		if(validation) {
			validation.validate(aType);
		}
	}
	
	_callback_blur(aEvent) {
		console.log("oa/utils/react/FormField::_callback_blur");
		
		this._validate("blur");
	}
	
	_callback_focus(aEvent) {
		console.log("oa/utils/react/FormField::_callback_focus");
		
		this._validate("focus");
	}
	
	_getMainElementProps() {
		var returnObject = super._getMainElementProps();
		
		returnObject["id"] = this.getSourcedProp("id");
		returnObject["name"] = this.getSourcedProp("name");
		returnObject["type"] = this.getSourcedProp("type");
		returnObject["placeholder"] = this.getSourcedProp("placeholder");
		
		returnObject["value"] = this.getSourcedProp("value");
		returnObject["onChange"] = this._callback_changeBound;
		returnObject["onBlur"] = this._callback_blurBound;
		returnObject["onFocus"] = this._callback_focusBound;
		
		return returnObject;
	}

	_renderMainElement() {
		//console.log("oa/utils/react/FormField::_renderMainElement");
		
		return <wrapper />;
	}

}
