import React, {Component} from 'react';
import { connect } from 'react-redux';

import BasePage from "../BasePage";

//import StaticBasePage from "./oa/mrouter/pages/static/StaticBasePage";
//import {StaticBasePageReduxConnected} from "./oa/mrouter/pages/static/StaticBasePage";
export default class StaticBasePage extends BasePage {

	constructor (props) {
		super(props);
		
		this._title = null;
		this._content = null;
		
		this._addMainElementClassName("entry");
	}
	
	_getMainElementClassNames() {
		var returnArray = super._getMainElementClassNames();
		
		return returnArray;
	}
	
	_getTitle() {
		//MENOTE: should be overridden
		return this._title;
	}
	
	_renderTitleElement(aPostData) {
		return <h1>{this._getTitle()}</h1>
	}
	
	_getContent() {
		//MENOTE: should be overridden
		return this._content;
	}
	
	_renderContentElement() {
		return <div className="content">{this._getContent()}</div>;
	}
	
	render() {
		
		var classNames = this._getMainElementClassNames();
		
		return (
			<div className={classNames.join(" ")}>
				{this._renderTitleElement()}
				{this._renderContentElement()}
				{this._renderDebugElement()}
			</div>
		);
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/pages/StaticBasePage.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return BasePage.mapStateToProps(state, myProps);
	};
}

export let StaticBasePageReduxConnected = connect(StaticBasePage.mapStateToProps)(StaticBasePage);
