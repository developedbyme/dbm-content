import React, {Component} from 'react';
import { connect } from 'react-redux';

import StaticBasePage from "./StaticBasePage";

//import Error404Page from "./oa/mrouter/pages/static/Error404Page";
//import {Error404PageReduxConnected} from "./oa/mrouter/pages/static/Error404Page";
export default class Error404Page extends StaticBasePage {

	constructor (props) {
		super(props);
		
		this._title = "Error 404";
		this._content = "The page you are looking for could not be found.";
		
		this._addMainElementClassName("error404");
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/pages/Error404Page.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return StaticBasePage.mapStateToProps(state, myProps);
	};
}

export let Error404PageReduxConnected = connect(Error404Page.mapStateToProps)(Error404Page);
