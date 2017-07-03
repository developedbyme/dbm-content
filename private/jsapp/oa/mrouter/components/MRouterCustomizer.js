import React, {Component} from 'react';
import { connect } from 'react-redux';

import OaBaseComponent from "oa/react/OaBaseComponent";

import {requestCustomizerData} from "oa/mrouter/store/actioncreators/mRouterActionCreators";

//import MRouterCustomizer from "oa/mrouter/components/MRouterCustomizer";
//import {MRouterCustomizerReduxConnected} from "oa/mrouter/components/MRouterCustomizer";
export default class MRouterCustomizer extends OaBaseComponent {

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
		//console.log("oa/mrouter/components/MRouterCustomizer.componentWillMount");

		super.componentWillMount();
		
		if(this.props.options) {
			this.props.dispatch(requestCustomizerData(this.props.options));
		}
		else {
			console.warn("Element doesn't have any options set", this);
		}
	}

	componentDidMount() {
		//console.log("oa/mrouter/components/MRouterCustomizer.componentDidMount");

		super.componentDidMount();
	}

	componentWillUnmount() {
		//console.log("oa/mrouter/components/MRouterCustomizer.componentWillUnmount");

		super.componentWillUnmount();
	}

	_renderLoadingContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterCustomizer::_renderLoadingContentElement should have been overridden.");

		return null;
	}

	_renderLoadingErrorContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterCustomizer::_renderLoadingErrorContentElement should have been overridden.");
		
		if(!this.props.options && !this.props.customizerData) {
			return <div className="error-message">Component could not render as prop options are not set</div>;
		}
		return null;
	}

	_renderLoadedContentElement() {
		//MENOTE: should be overridden
		console.warn("oa/mrouter/components/MRouterCustomizer::_renderLoadedContentElement should have been overridden.");

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
		//console.log("oa/mrouter/components/MRouterCustomizer.mapStateToProps (static)");
		//console.log(state, myProps);

		var returnObject = OaBaseComponent.mapStateToProps(state, myProps);

		var pageId = myProps.options;

		returnObject["loadingStatus"] = 0;
		returnObject["customizerData"] = null;
		if(!pageId) {
			returnObject["loadingStatus"] = -1;
		}
		else if(state.mRouter.customizerData[pageId] !== undefined) {
			var currentLink = state.mRouter.customizerData[pageId];
			var currentStatus = currentLink.status;
			returnObject["loadingStatus"] = currentStatus;
			if(currentStatus === 1) {
				returnObject["customizerData"] = currentLink.data;
			}
		}


		return returnObject;
	}
}

export let MRouterCustomizerReduxConnected = connect(MRouterCustomizer.mapStateToProps)(MRouterCustomizer);
