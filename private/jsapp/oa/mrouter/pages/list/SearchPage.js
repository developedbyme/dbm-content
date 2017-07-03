import React, {Component} from 'react';
import { connect } from 'react-redux';

import ListBasePage from "./ListBasePage";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import SearchPage from "oa/mrouter/pages/list/SearchPage";
//import {SearchPageReduxConnected} from "oa/mrouter/pages/list/SearchPage";
export default class SearchPage extends ListBasePage {

	constructor (props) {
		super(props);
	}
	
	_renderIntro(aPageData) {
		
		//METODO: insert search string
		
		return <header className="archive-header">
			<h1>Search</h1>
		</header>;
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/pages/SearchPage.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return ListBasePage.mapStateToProps(state, myProps);
	};
}

export let SearchPageReduxConnected = connect(SearchPage.mapStateToProps)(SearchPage);
