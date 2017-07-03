import React from "react";

import OaBaseComponent from "oa/react/OaBaseComponent";

// import TextArea from "oa/utils/react/TextArea";
export default class TextArea extends OaBaseComponent {

	constructor( props ) {
		super( props );
		
		this._mainElementType = "textarea";
		
		this._callback_changeBound = this._callback_change.bind(this);
	}
	
	_callback_change(aEvent) {
		console.log("oa/utils/react/TextArea::_callback_change");
		console.log(aEvent);
		console.log(aEvent.target.value);
		
		this.getReferences().getObject("value/" + this.props.valueName).updateValue(this.props.valueName, aEvent.target.value);
	}
	
	_getMainElementProps() {
		var returnObject = super._getMainElementProps();
		
		returnObject["id"] = this.props.id;
		returnObject["name"] = this.props.name;
		
		returnObject["onChange"] = this._callback_changeBound;
		
		return returnObject;
	}

	_renderMainElement() {
		//console.log("oa/utils/react/TextArea::_renderMainElement");
		
		return <wrapper>{this.props.value}</wrapper>;
	}

}
