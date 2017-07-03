import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';

import OaBaseComponent from "oa/react/OaBaseComponent";

import ReactImageUpdater from "oa/imageloader/ReactImageUpdater";

//import OaLazyImage from "oa/react/OaLazyImage";
//import {OaLazyImageReduxConnected} from "oa/react/OaLazyImage";
export default class OaLazyImage extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this.state["imageStatus"] = 0;
		this.state["renderedImage"] = null;
		
		this._imageUpdater = null;
	}
	
	_getMainElementProps() {
		var returnObject = super._getMainElementProps();
		
		if(this.state["imageStatus"] === 1) {
			if(this._mainElementType === "img") {
				returnObject["src"] = this.state["renderedImage"];
			}
			else {
				if(!returnObject["style"]) {
					returnObject["style"] = new Object();
				}
				returnObject["style"]["backgroundImage"] = "url('" + this.state["renderedImage"] + "')";
			}
		}
		
		return returnObject;
	}
	
	_getImageData() {
		return this.props.sources;
	}
	
	_getSettings() {
		if(this.props.settings) {
			return this.props.settings;
		}
		
		return {"type": "scale"};
	}
	
	componentDidMount() {
		//console.log("oa/react/OaBaseComponent.componentDidMount");
		
		var imageData = this._getImageData();
		
		if(imageData) {
			this._imageUpdater = ReactImageUpdater.create(this, ReactDOM.findDOMNode(this), imageData, this._getSettings(), window.OA.imageLoaderManager);
			window.OA.imageLoaderManager.addUpdater(this._imageUpdater);
		}
		else {
			console.warn("Image doesn't data", this);
		}
		
		super.componentDidMount();
	}

	componentWillUnmount() {
		//console.log("oa/react/OaBaseComponent.componentWillUnmount");
		
		window.OA.imageLoaderManager.removeUpdater(this._imageUpdater);
		this._imageUpdater = null;
		
		super.componentWillUnmount();
	}
	
	_renderContentElement() {
		return this.props.children;
	}
	
	render() {
		return React.createElement(this._mainElementType, this._getMainElementProps(),
			this._renderContentElement()
		);
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/react/OaLazyImage.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return OaBaseComponent.mapStateToProps(state, myProps);
	};
}

export let OaLazyImageReduxConnected = connect(OaLazyImage.mapStateToProps)(OaLazyImage);
