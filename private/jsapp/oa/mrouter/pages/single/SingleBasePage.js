import React, {Component} from 'react';
import { connect } from 'react-redux';

import PostData from "oa/mrouter/data/PostData";

import BasePage from "../BasePage";

//import SingleBasePage from "oa/mrouter/pages/single/SingleBasePage";
export default class SingleBasePage extends BasePage {

	constructor (props) {
		super(props);

		this._mainElementType = "article";
		this._addMainElementClassName("entry");
		this._addMainElementClassName("hentry");
		
		this._postData = new PostData();
	}

	_getMainElementClassNames() {
		var returnArray = super._getMainElementClassNames();

		var postData = this.props.pageData.posts[0];
		returnArray.push("post-" + postData["id"]);
		returnArray.push(postData["type"]);
		returnArray.push("type-" + postData["type"]);
		returnArray.push("status-" + postData["status"]);

		return returnArray;
	}

	_renderFeaturedImageElement() {
		var postData = this.props.pageData.queriedData;
		
		if(!postData.image) {
			return null;
		}

		return <a className="entry-image" href={postData.permalink} title={postData.title}>
			<img width={postData.image.sizes.large.width} height={postData.image.sizes.large.height} src={postData.image.sizes.large.url} className="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="" />
		</a>
	}

	_renderTitleElement() {
		var postData = this.props.pageData.queriedData;
		return <h1>{postData["title"]}</h1>
	}

	_renderExcerptElement() {
		var postData = this.props.pageData.queriedData;
		return <div className="excerpt" dangerouslySetInnerHTML={{__html: postData["excerpt"]}}></div>
	}

	_renderContentElement() {
		var postData = this.props.pageData.queriedData;
		return <div className="content" dangerouslySetInnerHTML={{__html: postData["content"]}}></div>;
	}
	
	componentWillMount() {
		//console.log("oa/mrouter/pages/single/SingleBasePage::componentWillMount");

		super.componentWillMount();
		
		var postData = this.props.pageData.queriedData;
		this._postData.setData(postData);
	}
	
	_prepareRender() {
		//console.log("oa/mrouter/pages/single/SingleBasePage::_prepareRender");
		
		super._prepareRender()
		
		var postData = this.props.pageData.queriedData;
		this._postData.setData(postData);
		
		this._references.addObject("mRouter/postData", this._postData);
		this._references.addObject("mRouter/postData/acfObject", postData.acf);
	}
	
	_renderMainElement() {
		
		return <wrapper>
			{this._renderFeaturedImageElement()}
			{this._renderTitleElement()}
			{this._renderExcerptElement()}
			{this._renderContentElement()}
			{this._renderDebugElement()}
		</wrapper>;
	}
}
