import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import SourceData from "oa/reference/SourceData";

//import HasData from "oa/react/HasData";
export default class HasData extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/HasData::_removeUsedProps");
		
		delete aReturnObject["check"];
		
		return aReturnObject;
	}
	
	_checkData(aData, aType) {
		switch(aType) {
			case "notEmpty":
				if(aData && aData !== "") {
					return true;
				}
				break;
			default:
				console.warn("Unknown check type " + aType + ". Using default.");
			case "default":
				if(aData) {
					return true;
				}
				break;
		}
		
		return false;
	}
	
	_renderMainElement() {
		//console.log("oa/react/HasData::_renderMainElement");
		
		var data = SourceData.getSourceWithType(this.props.check, this);
		var type = this.props.checkType ? this.props.checkType : "default";
		
		if(this._checkData(data, type)) {
			return super._renderMainElement();
		}
		
		return null;
	}
}
