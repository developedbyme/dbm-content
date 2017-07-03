import React, {Component} from 'react';
import { connect } from 'react-redux';

import OaBaseComponent from "oa/react/OaBaseComponent";

import {requestPostRange} from "oa/mrouter/store/actioncreators/mRouterActionCreators";

//import MRouterPostRangeObject from "oa/mrouter/components/MRouterPostRangeObject";
//import {MRouterPostRangeObjectReduxConnected} from "oa/mrouter/components/MRouterPostRangeObject";
export default class MRouterPostRangeObject extends OaBaseComponent {

	constructor (props) {
		super(props);
	}
	
	_getMainElementClassNames() {
		var returnArray = super._getMainElementClassNames();
		
		if(this.props.loadingStatus === 1) {
			returnArray.push("mrouter-loaded");
		}
		else if(this.props.loadingStatus === 0 || this.props.loadingStatus === 2) {
			returnArray.push("mrouter-loading");
		}
		else{
			returnArray.push("mrouter-loading-error");
		}
		
		return returnArray;
	}
	
	componentWillMount() {
		//console.log("oa/mrouter/components/MRouterPostRangeObject.componentWillMount");
		
		super.componentWillMount();
		
		this.props.dispatch(requestPostRange(this.props.postRangePath));
	}
	
	componentWillReceiveProps(aNextProps) {
		//console.log("oa/mrouter/components/MRouterPostRangeObject::componentWillReceiveProps");
		
		if(aNextProps.postRangePath !== this.props.postRangePath) {
			this.props.dispatch(requestPostRange(aNextProps.postRangePath));
		}
	}

	componentDidMount() {
		//console.log("oa/mrouter/components/MRouterPostRangeObject.componentDidMount");
		
		super.componentDidMount();
	}

	componentWillUnmount() {
		//console.log("oa/mrouter/components/MRouterPostRangeObject.componentWillUnmount");
		
		super.componentWillUnmount();
	}
	
	_renderLoadingContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterPostRangeObject::_renderLoadingContentElement should have been overridden.");
		
		return null;
	}
	
	_renderLoadingErrorContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterPostRangeObject::_renderLoadingErrorContentElement should have been overridden.");
		
		return null;
	}
	
	_renderLoadedContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterPostRangeObject::_renderLoadedContentElement should have been overridden.");
		
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
	
	_renderSafe() {
		return React.createElement(this._mainElementType, this._getMainElementProps(),
			this._renderContentElement()
		);
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/components/MRouterPostRangeObject.mapStateToProps (static)");
		//console.log(state, myProps);
		
		var returnObject = OaBaseComponent.mapStateToProps(state, myProps);
		
		var postRangePath = myProps.postRangePath;
		
		returnObject["loadingStatus"] = 0;
		returnObject["postRange"] = null;
		
		if(myProps.postRange) {
			returnObject["loadingStatus"] = 1;
			returnObject["postRange"] = myProps.postRange;
		}
		else {
			var currentLink = state.mRouter.postRanges[postRangePath];
			if(currentLink !== undefined) {
			
				var currentStatus = currentLink.status;
				returnObject["loadingStatus"] = currentStatus;
				returnObject["postRange"] = currentLink.data;
			}
		}
		
		return returnObject;
	};
}

export let MRouterPostRangeObjectReduxConnected = connect(MRouterPostRangeObject.mapStateToProps)(MRouterPostRangeObject);
