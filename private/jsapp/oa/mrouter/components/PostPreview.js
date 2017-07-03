import React, {Component} from 'react';
import { connect } from 'react-redux';

import MRouterPageDataObject from "oa/mrouter/components/MRouterPageDataObject";

//import PostPreview from "oa/mrouter/components/PostPreview";
//import {PostPreviewReduxConnected} from "oa/mrouter/components/PostPreview";
export default class PostPreview extends MRouterPageDataObject {

	constructor (props) {
		super(props);
		
		this._addMainElementClassName("entry");
		this._addMainElementClassName("hentry");
	}
	
	componentWillMount() {
		//console.log("oa/mrouter/components/PostPreview.componentWillMount");
		
		super.componentWillMount();
	}

	componentDidMount() {
		//console.log("oa/mrouter/components/PostPreview.componentDidMount");
		
		super.componentDidMount();
	}

	componentWillUnmount() {
		//console.log("oa/mrouter/components/PostPreview.componentWillUnmount");
		
		super.componentWillUnmount();
	}
	
	_renderImageElement() {
		
		if(!this.props.pageData.image) {
			return null;
		}
		
		return <a className="entry-image" href={this.props.pageData.permalink} title={this.props.pageData.title}>
			<img width={this.props.pageData.image.sizes.large.width} height={this.props.pageData.image.sizes.large.height} src={this.props.pageData.image.sizes.large.url} className="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="" />
		</a>
	}
	
	_renderHeaderContent() {
		var returnArray = new Array();
		
		returnArray.push(<h2 key="entry-title" className="entry-title"><a href={this.props.pageData.permalink} rel="bookmark">{this.props.pageData.title}</a></h2>);
		
		var authorElement = null;
		if(this.props.pageData.author) {
			authorElement = <div key="byline" className="byline"><a href={this.props.pageData.author.permalink} title={"Posts by " + this.props.pageData.author.name} rel="author">{this.props.pageData.author.name}</a></div>;
		}
		
		returnArray.push(<div key="entry-meta" className="entry-meta">
			<time className="posted-on"><a href={this.props.pageData.permalink} title={this.props.pageData.title}>{this.props.pageData.publishedDate}</a></time>
			{authorElement}
		</div>);
		
		return returnArray;
	}
	
	_renderHeaderElement() {
		
		return React.createElement("header", {"className": "entry-header"},
			this._renderHeaderContent()
		);
	}
	
	_renderLoadedContentElement() {
		return <div className="entry-content" dangerouslySetInnerHTML={{__html: this.props.pageData["excerpt"]}} />;
	}
	
	_renderTaxonomyTermElement(aType, aItem) {
		//console.log("oa/mrouter/components/PostPreview::_renderTaxonomyTermElement");
		//console.log(aType, aItem);
		
		return <li key={"term-" + aItem.id}><a href={aItem.permalink} rel={aType +" tag"}>{aItem.name}</a></li>;
	}
	
	_renderTaxonomyTermsElement(aType, aItems) {
		
		var termsArray = new Array();
		
		var currentArray = aItems;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			termsArray.push(this._renderTaxonomyTermElement(aType, currentArray[0]));
		}
		
		return <div key={aType} className={aType}>
			<ul className={"post-" + aType}>
				{termsArray}
			</ul>
		</div>;
	}
	
	_renderTaxonomyTerms() {
		var returnArray = new Array()
		
		if(this.props.pageData.terms.category && this.props.pageData.terms.category.length > 0) {
			returnArray.push(this._renderTaxonomyTermsElement("category", this.props.pageData.terms.category));
		}
		if(this.props.pageData.terms.tags && this.props.pageData.terms.tags.length > 0) {
			returnArray.push(this._renderTaxonomyTermsElement("tags", this.props.pageData.terms.tags));
		}
		
		return returnArray;
	}
	
	_renderFooterContent() {
		return this._renderTaxonomyTerms();
	}
	
	_renderFooterElement() {
		return React.createElement("footer", {"className": "entry-footer"},
			this._renderFooterContent()
		);
	}
	
	//METODO
	/*
	_renderSafe() {
		return React.createElement(this._mainElementType, this._getMainElementProps(),
			this._renderImageElement(),
			this._renderHeaderElement(),
			this._renderContentElement(),
			this._renderFooterElement()
		);
	}
	*/
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/components/PostPreview.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return MRouterPageDataObject.mapStateToProps(state, myProps);
	};
}

export let PostPreviewReduxConnected = connect(PostPreview.mapStateToProps)(PostPreview);
