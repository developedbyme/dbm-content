import React, {Component} from 'react';
import { connect } from 'react-redux';

import OaBaseComponent from "oa/react/OaBaseComponent";

import {requestPostById} from "oa/mrouter/store/actioncreators/mRouterActionCreators";

//import MRouterPageDataObject from "oa/mrouter/components/MRouterPageDataObject";
//import {MRouterPageDataObjectReduxConnected} from "oa/mrouter/components/MRouterPageDataObject";
export default class MRouterPageDataObject extends OaBaseComponent {

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
		//console.log("oa/mrouter/components/MRouterPageDataObject.componentWillMount");
		
		super.componentWillMount();
		
		if(!this.props.pageData) {
			this.props.dispatch(requestPostById(this.props.pageId));
		}
	}

	componentDidMount() {
		//console.log("oa/mrouter/components/MRouterPageDataObject.componentDidMount");
		
		super.componentDidMount();
	}

	componentWillUnmount() {
		//console.log("oa/mrouter/components/MRouterPageDataObject.componentWillUnmount");
		
		super.componentWillUnmount();
	}
	
	_renderLoadingContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterPageDataObject::_renderLoadingContentElement should have been overridden.");
		
		return null;
	}
	
	_renderLoadingErrorContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterPageDataObject::_renderLoadingErrorContentElement should have been overridden.");
		
		return null;
	}
	
	_renderLoadedContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterPageDataObject::_renderLoadedContentElement should have been overridden.");
		
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
	
	render() {
		return React.createElement(this._mainElementType, this._getMainElementProps(),
			this._renderContentElement()
		);
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/components/MRouterPageDataObject.mapStateToProps (static)");
		//console.log(state, myProps);
		
		var returnObject = OaBaseComponent.mapStateToProps(state, myProps);
		
		if(myProps.pageData) {
			returnObject["loadingStatus"] = 1;
			returnObject["pageData"] = myProps.pageData;
		}
		else {
			var pageId = myProps.pageId;
		
			returnObject["loadingStatus"] = 0;
			returnObject["pageData"] = null;
			if(state.mRouter.idLinks[pageId] !== undefined) {
				var currentLink = state.mRouter.idLinks[pageId];
				var currentStatus = currentLink.status;
				returnObject["loadingStatus"] = currentStatus;
				if(currentStatus === 1) {
					var url = currentLink.url;
					var data = state.mRouter.postData[url].data;
					returnObject["pageData"] = data;
				}
			}
		}
		
		
		
		
		return returnObject;
	};
}

export let MRouterPageDataObjectReduxConnected = connect(MRouterPageDataObject.mapStateToProps)(MRouterPageDataObject);
