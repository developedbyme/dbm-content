import React, {Component} from 'react';
import { connect } from 'react-redux';

import ListBasePage from "./ListBasePage";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import TaxonomyTermArchivePage from "oa/mrouter/pages/list/TaxonomyTermArchivePage";
export default class TaxonomyTermArchivePage extends ListBasePage {

	constructor (props) {
		super(props);
	}
	
	_renderIntro(aPageData) {
		return <header className="archive-header">
			<h1>{aPageData["queriedData"]["taxonomy"]}: {aPageData["queriedData"]["name"]}</h1>
			<div>
				{aPageData["queriedData"]["description"]}
			</div>
		</header>;
	}
}
