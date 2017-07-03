import React from 'react';

import PropTypes from 'prop-types';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";
import SourceData from "oa/reference/SourceData";
import PostData from "oa/mrouter/data/PostData";

//import AcfRowInjection from "oa/react/AcfRowInjection";
export default class AcfRowInjection extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this._references = new ReferencesHolder();
		
		this._postData = new PostData();
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		//console.log("oa/react/AcfRowInjection::getReferences")
		return {"references": this._references};
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/AcfRowInjection::_removeUsedProps");
		
		delete aReturnObject["input"];
		delete aReturnObject["rowData"];
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/AcfRowInjection::_manipulateProps");
		
		var references = this.getReferences();
		
		var inputName = this.props.input ? this.props.input : "rowData";
		var rowData = this.props[inputName];
		
		this._references.addObject("mRouter/postData/acfRow", rowData);
		
		aReturnObject["references"] = references;
		
		return aReturnObject;
	}
	
	_prepareRender() {
		super._prepareRender();
		
		this._references.setParent(this.context.references);
	}
}

AcfRowInjection.childContextTypes = {
	"references": PropTypes.object
};
