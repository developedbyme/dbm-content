import React, {Component} from 'react';
import { connect } from 'react-redux';

import OaLazyImage from "oa/react/OaLazyImage";

//import MRouterLazyImageImg from "oa/mrouter/components/MRouterLazyImageImg";
//import {MRouterLazyImageImgReduxConnected} from "oa/mrouter/components/MRouterLazyImageImg";
export default class MRouterLazyImageImg extends OaLazyImage {

	constructor (props) {
		super(props);
		
		this._mainElementType = "img";
	}
	
	_getImageData() {
		return this.props.data.sizes;
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/components/MRouterLazyImageImg.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return OaLazyImage.mapStateToProps(state, myProps);
	};
}

export let MRouterLazyImageImgReduxConnected = connect(MRouterLazyImageImg.mapStateToProps)(MRouterLazyImageImg);
