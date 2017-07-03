import React, {Component} from 'react';
import { connect } from 'react-redux';

import OaBaseComponent from "oa/react/OaBaseComponent";

import {requestOaSiteData} from "oa/sitedata/store/actioncreators/oaSiteDataActionCreators";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import OaSiteDataObject from "oa/sitedata/components/OaSiteDataObject";
//import {OaSiteDataObjectReduxConnected} from "oa/sitedata/components/OaSiteDataObject";
export default class OaSiteDataObject extends OaBaseComponent {

	constructor (props) {
		super(props);
	}
	
	componentWillMount() {
		//console.log("oa/sitedata/components/OaSiteDataObject.componentWillMount");
		
		super.componentWillMount();
		this.props.dispatch(requestOaSiteData(this.props.dataPath));
	}

	componentDidMount() {
		//console.log("oa/sitedata/components/OaSiteDataObject.componentDidMount");
		
		super.componentDidMount();
	}

	componentWillUnmount() {
		//console.log("oa/sitedata/components/OaSiteDataObject::componentWillUnmount");
		
		super.componentWillUnmount();
	}
	
	_renderLoadingContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/sitedata/components/OaSiteDataObject::_renderLoadingContentElement should have been overridden.");
		
		return null;
	}
	
	_renderLoadingErrorContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/sitedata/components/OaSiteDataObject::_renderLoadingErrorContentElement should have been overridden.");
		
		return null;
	}
	
	_renderLoadedContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/sitedata/components/OaSiteDataObject::_renderLoadedContentElement should have been overridden.");
		
		return null;
	}
	
	_renderContentElement() {
		
		if(this.props.loadingStatus === 1) {
			return this._renderLoadedContentElement();
		}
		else if(this.props.loadingStatus === 0 || this.props.loadingStatus === 2) {
			return this._renderLoadingContentElement();
		}
		else{
			return this._renderLoadingErrorContentElement();
		}
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/sitedata/components/OaSiteDataObject.mapStateToProps (static)");
		//console.log(state, myProps);
		
		var returnObject = OaBaseComponent.mapStateToProps(state, myProps);
		
		returnObject["loadingStatus"] = 0;
		returnObject["siteData"] = null;
		
		if(state.oaSiteData.data[myProps.dataPath] !== undefined) {
			var currentLoader = state.oaSiteData.data[myProps.dataPath];
			var currentStatus = currentLoader.status;
			returnObject["loadingStatus"] = currentStatus;
			
			if(currentStatus === 1) {
				returnObject["siteData"] = currentLoader.data;
			}
		}
		
		return returnObject;
	};
}

export let OaSiteDataObjectReduxConnected = connect(OaSiteDataObject.mapStateToProps)(OaSiteDataObject);
