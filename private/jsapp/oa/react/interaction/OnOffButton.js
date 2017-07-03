import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import OaSelectSectionManipulationObject from "oa/react/OaSelectSectionManipulationObject";

//import OnOffButton from "oa/react/interaction/OnOffButton";
export default class OnOffButton extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this._mainElementType = "div";
		
		this._callback_changeBound = this._callback_change.bind(this);
	}
	
	_callback_change(aEvent) {
		console.log("oa/react/interaction/OnOffButton::_callback_change");
		
		this.getReferences().getObject("value/" + this.props.valueName).updateValue(this.props.valueName, !this.props[this.props.valueName], this.props.additionalData);
	}
	
	_manipulateProps(aReturnObject) {
		aReturnObject["onClick"] = this._callback_changeBound;
		aReturnObject["selectedSections"] = this.props[this.props.valueName] ? "on" : "off";
		
		return aReturnObject;
	}
	
	_getChildToClone() {
		return <OaSelectSectionManipulationObject>
			{this.props.children}
		</OaSelectSectionManipulationObject>;
	}
}
