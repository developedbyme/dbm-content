import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

//import CreateChildrenBaseObject from "oa/react/create/CreateChildrenBaseObject";
export default class CreateChildrenBaseObject extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_getCreateFunction() {
		return this.props.createFunction;
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/create/CreateChildrenBaseObject::_removeUsedProps");
		
		delete aReturnObject["createFunction"];
		delete aReturnObject["output"];
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/create/CreateChildrenBaseObject::_manipulateProps");
		
		var createFunction = this._getCreateFunction();
		
		if(createFunction) {
			var children = createFunction(this);
			var outputName = this.props.output ? this.props.output : "dynamicChildren";
			aReturnObject[outputName] = children;
		}
		else {
			console.warn("Create function is not set");
		}
		
		return aReturnObject;
	}
}