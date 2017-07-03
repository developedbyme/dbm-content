import React from 'react';

import PropTypes from 'prop-types';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";
import SourceData from "oa/reference/SourceData";
import PostData from "oa/mrouter/data/PostData";

//import PostDataInjection from "oa/react/PostDataInjection";
export default class PostDataInjection extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this._references = new ReferencesHolder();
		
		this._postData = new PostData();
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		//console.log("oa/react/PostDataInjection::getReferences")
		return {"references": this._references};
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/PostDataInjection::_removeUsedProps");
		
		delete aReturnObject["input"];
		delete aReturnObject["postData"];
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/PostDataInjection::_manipulateProps");
		
		var references = this.getReferences();
		
		var inputName = this.props.input ? this.props.input : "postData";
		var postData = this.props[inputName];
		
		this._postData.setData(postData);
		
		this._references.addObject("mRouter/postData", this._postData);
		this._references.addObject("mRouter/postData/acfObject", postData.acf);
		
		aReturnObject["references"] = references;
		
		return aReturnObject;
	}
	
	_prepareRender() {
		super._prepareRender();
		
		this._references.setParent(this.context.references);
	}
}

PostDataInjection.childContextTypes = {
	"references": PropTypes.object
};
