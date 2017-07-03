import React, {Component} from 'react';
import { connect } from 'react-redux';

import ListBasePage from "./ListBasePage";

import ReferencesHolder from "oa/reference/ReferencesHolder";

import SinglePage from "oa/mrouter/pages/single/SinglePage";

//import PostsPage from "oa/mrouter/pages/list/PostsPage";
//import {PostsPageReduxConnected} from "oa/mrouter/pages/list/PostsPage";
export default class PostsPage extends ListBasePage {

	constructor (props) {
		super(props);
	}
	
	_renderIntro(aPageData) {
		
		//METODO: insert page data
		
		return <SinglePage pageData={this.props.pageData} references={this._references} />;
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/pages/PostsPage.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return ListBasePage.mapStateToProps(state, myProps);
	};
}

export let PostsPageReduxConnected = connect(PostsPage.mapStateToProps)(PostsPage);
