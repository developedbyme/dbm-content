import React from 'react';

import PropTypes from 'prop-types';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";
import SourceData from "oa/reference/SourceData";

//import ReferenceInjection from "oa/react/ReferenceInjection";
export default class ReferenceInjection extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this._references = new ReferencesHolder();
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		//console.log("oa/react/ReferenceInjection::getReferences")
		return {"references": this._references};
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/ReferenceInjection::_removeUsedProps");
		
		delete aReturnObject["injectData"];
		
		return aReturnObject;
	}
	
	_v1_manipulateProps(aReturnObject) {
		//console.log("oa/react/ReferenceInjection::_v1_manipulateProps");
		
		var references = this.getReferences();
		
		var injectData = this.props.injectData;
		for(var objectName in injectData) {
			var sourcedData = SourceData.getSourceWithType(injectData[objectName], this);
			references.addObject(objectName, sourcedData);
		}
		
		aReturnObject["references"] = references;
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/ReferenceInjection::_manipulateProps");
		
		if(this.props.v != "2") {
			console.log("Upgrade reference injectsion to v2", this);
			return this._v1_manipulateProps(aReturnObject);
		}
		
		var references = this.getReferences();
		
		var injectData = this.props.injectData;
		for(var objectName in injectData) {
			var sourcedData = this.resolveSourcedData(injectData[objectName]);
			references.addObject(objectName, sourcedData);
		}
		
		aReturnObject["references"] = references;
		
		return aReturnObject;
	}
	
	_prepareRender() {
		super._prepareRender();
		
		this._references.setParent(this.context.references);
	}
}

ReferenceInjection.childContextTypes = {
	"references": PropTypes.object
};
