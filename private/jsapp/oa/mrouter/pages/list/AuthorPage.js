import React, {Component} from 'react';
import { connect } from 'react-redux';

import ListBasePage from "./ListBasePage";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import AuthorPage from "oa/mrouter/pages/list/AuthorPage";
//import {AuthorPageReduxConnected} from "oa/mrouter/pages/list/AuthorPage";
export default class AuthorPage extends ListBasePage {

	constructor (props) {
		super(props);
	}
	
	_renderIntro(aPageData) {
		
		return <header className="archive-header">
			<h1>Author: <span className="vcard">{aPageData.queriedData.name}</span></h1>
		</header>;
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/pages/AuthorPage::mapStateToProps (static)");
		//console.log(state, myProps);
		
		return ListBasePage.mapStateToProps(state, myProps);
	};
}

export let AuthorPageReduxConnected = connect(AuthorPage.mapStateToProps)(AuthorPage);
