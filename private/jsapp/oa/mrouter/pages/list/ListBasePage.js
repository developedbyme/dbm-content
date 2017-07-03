import React, {Component} from 'react';
import { connect } from 'react-redux';

import BasePage from "../BasePage";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import ListBasePage from "oa/mrouter/pages/list/ListBasePage";
export default class ListBasePage extends BasePage {

	constructor (props) {
		super(props);
		
		this._mainElementType = "div";
		
		this._references = new ReferencesHolder();
		this._postPreviewContentCreatorPath = "contentCreatorClasses/mRouter/postPreview";
	}
	
	_renderIntro(aPageData) {
		//MENOTE: should be overridden
		return null;
	}
	
	_renderPostElements(aPostsData) {
		
		var returnArray = new Array();
		
		var PostPreviewClass = this._references.getObject(this._postPreviewContentCreatorPath);
		if(!PostPreviewClass) {
			console.error("No class set for post previews (" + this._menuItemContentCreatorPath + ")");
			return null;
		}
		
		var currentArray = aPostsData;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentData = currentArray[i];
			returnArray.push(
				React.createElement(PostPreviewClass, {"key": "post-" + currentData["id"], "pageData": currentData})
			);
		}
		
		return returnArray;
	}
	
	_renderPagination(aPageData) {
		//METODO
		
		return null;
	}
	
	_renderMainElement() {
		
		this._references.setParent(this.context.references);
		
		var postsData = this.props.pageData.posts;
		
		return <wrapper>
			{this._renderFeaturedImageElement()}
			{this._renderIntro(this.props.pageData)}
			{this._renderPostElements(postsData)}
			{this._renderPagination(this.props.pageData)}
			{this._renderDebugElement()}
		</wrapper>;
	}
}
