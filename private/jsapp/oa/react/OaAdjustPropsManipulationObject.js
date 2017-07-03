import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

//import OaAdjustPropsManipulationObject from "oa/react/OaAdjustPropsManipulationObject";
export default class OaAdjustPropsManipulationObject extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/OaAdjustPropsManipulationObject::_manipulateProps");
		
		if(this.props.adjustFunction) {
			this.props.adjustFunction(this, aReturnObject);
		}
		else {
			console.warn("Adjust function is not set");
		}
		
		delete aReturnObject["adjustFunction"];
		
		return aReturnObject;
	}
}
