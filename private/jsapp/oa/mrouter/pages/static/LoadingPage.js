import React, {Component} from 'react';
import { connect } from 'react-redux';

import StaticBasePage from "./StaticBasePage";

//import LoadingPage from "./oa/mrouter/pages/static/LoadingPage";
//import {LoadingPageReduxConnected} from "./oa/mrouter/pages/static/LoadingPage";
export default class LoadingPage extends StaticBasePage {

	constructor (props) {
		super(props);
		
		this._title = "Loading";
		this._content = "Your content is loading...";
		
		this._addMainElementClassName("loading");
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/pages/LoadingPage.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return StaticBasePage.mapStateToProps(state, myProps);
	};
}

export let LoadingPageReduxConnected = connect(LoadingPage.mapStateToProps)(LoadingPage);
