import React, {Component} from 'react';
import { connect } from 'react-redux';

import StaticBasePage from "./StaticBasePage";

//import ErrorLoadingPage from "./oa/mrouter/pages/static/ErrorLoadingPage";
//import {ErrorLoadingPageReduxConnected} from "./oa/mrouter/pages/static/ErrorLoadingPage";
export default class ErrorLoadingPage extends StaticBasePage {

	constructor (props) {
		super(props);
		
		this._title = "Error";
		this._content = "An error occured while loading your content...";
		
		this._addMainElementClassName("error-loading");
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/pages/ErrorLoadingPage.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return StaticBasePage.mapStateToProps(state, myProps);
	};
}

export let ErrorLoadingPageReduxConnected = connect(ErrorLoadingPage.mapStateToProps)(ErrorLoadingPage);
