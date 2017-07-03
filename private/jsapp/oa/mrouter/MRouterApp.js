import React, {Component} from 'react';
import { connect } from 'react-redux';

import PropTypes from 'prop-types';

import {requestUrl} from "./store/actioncreators/mRouterActionCreators";
import {setCurrentPage} from "oa/mrouter/store/actioncreators/mRouterActionCreators";

import OaBaseComponent from "oa/react/OaBaseComponent";

import PageSelectorCreator from "./utils/PageSelectorCreator";

import {BasePageReduxConnected} from "./pages/BasePage";
import {SingleBasePageReduxConnected} from "./pages/single/SingleBasePage";
import {SinglePostReduxConnected} from "./pages/single/SinglePost";
import {SinglePageReduxConnected} from "./pages/single/SinglePage";
import {ListBasePageReduxConnected} from "./pages/list/ListBasePage";
import {TaxonomyTermArchivePageReduxConnected} from "./pages/list/TaxonomyTermArchivePage";
import {AuthorPageReduxConnected} from "oa/mrouter/pages/list/AuthorPage";
import {SearchPageReduxConnected} from "oa/mrouter/pages/list/SearchPage";
import {PostsPageReduxConnected} from "oa/mrouter/pages/list/PostsPage";
import {StaticBasePageReduxConnected} from "./pages/static/StaticBasePage";
import {Error404PageReduxConnected} from "./pages/static/Error404Page";

import {ErrorLoadingPageReduxConnected} from "./pages/static/ErrorLoadingPage";
import {LoadingPageReduxConnected} from "./pages/static/LoadingPage";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import MRouterApp from "oa/mrouter/MRouterApp";
//import {MRouterAppReduxConnected} from "oa/mrouter/MRouterApp";
export default class MRouterApp extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._constructor();
		this._init();
		
	}
	
	_constructor() {
		this._appName = "Unnamed app";

		this.state["mainElementClassNames"] = null;
		this.state["headerElementClassNames"] = null;
		this.state["footerElementClassNames"] = null;
		this.state["overlayData"] = null;
		this.state["overlayCurrent"] = 0;
		
		this._renderFunctions = new Array();

		this._references = new ReferencesHolder();

		this._references.addObject("mainElementController", this);
		this._references.addObject("headerElementController", this);
		this._references.addObject("footerElementController", this);

		this._references.addObject("urlResolver/site", this);
		this._references.addObject("urlResolver/theme", this);

		this._pageSelectors = new Array();
		
		this._addPropToCheck("currentPage");
		this._addPropToCheck("currentMRouterData");

		this._addStateToCheck("mainElementClassNames");
		this._addStateToCheck("footerElementClassNames");
		this._addStateToCheck("headerElementClassNames");
	}
	
	_init() {
		this._registerAllPages();
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		//console.log("oa/mrouter/MRouterApp::getChildContext")
		return {"references": this._references};
	}

	resolveUrl(aPath, aRealtiveTo) {
		//console.log("oa/mrouter/MRouterApp::resolveUrl");

		if(this.props.basePaths && this.props.basePaths[aRealtiveTo] != undefined) {
			return this.props.basePaths[aRealtiveTo] + "/" + aPath;
		}
		return aPath;
	}

	setMainElementClassNames(aClassNames) {
		this.setState({"mainElementClassNames": aClassNames});
	}

	setFooterElementClassNames(aClassNames) {
		//console.log("oa/mrouter/MRouterApp::setFooterElementClassNames");

		this.setState({"footerElementClassNames": aClassNames});
	}

	setHeaderElementClassNames(aClassNames) {
		//console.log("oa/mrouter/MRouterApp::setHeaderElementClassNames");

		this.setState({"headerElementClassNames": aClassNames});
	}

	setOverlay(overlayData, overlayCurrent = 0) {
		this.setState({
			overlayData,
			overlayCurrent
		});

		// React is weird, we need to force an update to make it work
		this.forceUpdate();
	}

	_registerAllPages() {
		this._registerStatusPages();
		this._registerPages();
		this._registerDefaultPages();
		this._registerCatchAllPages();

		return this;
	}

	_registerStatusPages() {

		this._registerPageSelector(PageSelectorCreator.createLoadingStatusPage(2, LoadingPageReduxConnected, this._references, "Loading"));
		this._registerPageSelector(PageSelectorCreator.createLoadingStatusPage(-1, ErrorLoadingPageReduxConnected, this._references, "Error loading"));

		return this;
	}

	_registerPages() {
		//MENOTE: should be overridden

		return this;
	}

	_registerDefaultPages() {

		//Single
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_page", true, SinglePageReduxConnected, this._references, "page"));
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_single", true, SinglePostReduxConnected, this._references, "post"));
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_singular", true, SingleBasePageReduxConnected, this._references, "single"));

		//List
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_category", true, TaxonomyTermArchivePageReduxConnected, this._references, "Category archive"));
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_tag", true, TaxonomyTermArchivePageReduxConnected, this._references, "Tags archive"));
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_tax", true, TaxonomyTermArchivePageReduxConnected, this._references, "Taxonomy archive"));
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_author", true, AuthorPageReduxConnected, this._references, "Author page"));
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_search", true, SearchPageReduxConnected, this._references, "Search page"));
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_posts_page", true, PostsPageReduxConnected, this._references, "Posts page"));
		this._registerPageSelector(PageSelectorCreator.createList(ListBasePageReduxConnected, this._references, "Generic archive"));

		//Static
		this._registerPageSelector(PageSelectorCreator.createTemplateSelection("is_404", true, Error404PageReduxConnected, this._references, "404"));

		return this;
	}

	_registerCatchAllPages() {
		this._registerPageSelector(PageSelectorCreator.createDefaultPage(BasePageReduxConnected, this._references, "Default page"));
		this._registerPageSelector(PageSelectorCreator.createCatchAllPage(StaticBasePageReduxConnected, this._references));

		return this;
	}

	_registerPageSelector(aPageSelector) {
		this._pageSelectors.push(aPageSelector);

		return this;
	}
	
	_createTypePageSelector(aType, aPageClass) {
		
		var pageSelector = PageSelectorCreator.createTemplateSelection(aType, true, aPageClass, this._references, this._appName + " " + aType);
		
		this._registerPageSelector(pageSelector);
		
		return pageSelector;
	}
	
	_createCustomPostTypePageSelector(aCustomPostTypeName, aPageClass) {
		
		var pageSelector = PageSelectorCreator.createCustomPostTypeSelection(aCustomPostTypeName, aPageClass, this._references, this._appName + " CPT " + aCustomPostTypeName);
		
		this._registerPageSelector(pageSelector);
		
		return pageSelector;
	}
	
	_createPageTemplatePageSelector(aPageTemplateName, aPageClass) {
		
		var pageSelector = PageSelectorCreator.createPageTemplateSelection(aPageTemplateName, aPageClass, this._references, this._appName + " page template " + aPageTemplateName);
		
		this._registerPageSelector(pageSelector);
		
		return pageSelector;
	}

	_selectPage(aLoaderData) {

		if(aLoaderData) {

			var currentArray = this._pageSelectors;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentSelector = currentArray[i];
				if(currentSelector.qualify(aLoaderData)) {
					console.log("Using template " + currentSelector.debugName);

					return currentSelector.createPage(aLoaderData);
				}
			}

			console.error("No page selector matched the current data");
			return <div>No page selector matched the current data</div>;
		}

		console.error("No loader data has been set");
		return <div>No loader data has been set</div>;
	}

	_renderOverlay() {
		if (this.state.overlayData) {
			return (
				<div className='media-overlay'>
		     	{this.state.overlayData[this.state.overlayCurrent]}

					{this.state.overlayData.length > 1 ?
						<nav className='overlay-controls'>
		        	<div className='overlay-pagination prev' onClick={this.setOverlay.bind(this, this.state.overlayData, this.state.overlayCurrent <= 0 ? this.state.overlayData.length - 1 : this.state.overlayCurrent - 1)} />
		        	<div className='close' onClick={this.setOverlay.bind(this, null)} />
		        	<div className='overlay-pagination next' onClick={this.setOverlay.bind(this, this.state.overlayData, this.state.overlayCurrent >= this.state.overlayData.length - 1 ? 0 : this.state.overlayCurrent + 1)} />
		      	</nav> : <nav className='overlay-controls'><div className='close' onClick={this.setOverlay.bind(this, null)} /></nav>}
		    </div>
			);
		} else {
			return null;
		}
	}

	_renderHeaderContent() {
		//MENOTE: should be overridden
		return null;
	}

	_renderMain() {

		var page = this._selectPage(this.props.currentMRouterData);

		return (<main id="site-content" role="main" className={this.state.mainElementClassNames}>
			{page}
		</main>);
	}

	_renderFooterContent() {
		//MENOTE: should be overridden
		return null;
	}

	componentWillMount() {
		//console.log("oa/mrouter/MRouterApp::componentWillMount");

		

		this.props.dispatch(requestUrl(this.props.currentPage));
		
	}

	componentDidMount () {
		/*
    history.listen((event) => {
      this.props.dispatch(requestUrl(event.pathname));
      this.setState({location: event.pathname});
    });

    var content = document.getElementById("odd-root");
    content.addEventListener("click", ContentClick, false);

    function ContentClick (event) {
      event.preventDefault();
      history.push(event.target.pathname);
      return false;
    }
		*/
	}

	componentWillUnmount () {
		//history.unlisten();
	}
	
	_prepareRender() {
		super._prepareRender()
		
		this._references.setParent(this.props.references);
	}
	
	_addMainChildren(aReturnArray) {
		console.log("oa/mrouter/MRouterApp::_addMainChildren");
		
		var currentArray = this._renderFunctions;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentFunction = currentArray[i];
			aReturnArray.push(currentFunction.call(this, this));
		}
	}

	_renderMainElement() {
		console.log("oa/mrouter/MRouterApp::_renderMainElement");
		
		return <wrapper>
			{this._renderOverlay()}
			<header id="site-header" role="banner" className={this.state.headerElementClassNames}>
				{this._renderHeaderContent()}
			</header>
			{this._renderMain()}
			<footer id="site-footer" role="contentinfo" className={this.state.footerElementClassNames}>
				{this._renderFooterContent()}
			</footer>
		</wrapper>;
	}

	static mapStateToProps(state, myProps) {
		//console.log("oa/mrouter/MRouterApp.mapStateToProps (static)");
		//console.log(state, myProps);

		var returnObject = OaBaseComponent.mapStateToProps(state, myProps);

		var currentMRouterData = null;

		if(state.mRouter && state.mRouter.currentPage && state.mRouter.data) {
			if(state.mRouter.data[state.mRouter.currentPage]) {
				currentMRouterData = state.mRouter.data[state.mRouter.currentPage];
			}
		}
		
		returnObject["currentPage"] = state.mRouter ? state.mRouter.currentPage : null;
		returnObject["currentMRouterData"] = currentMRouterData;
		returnObject["basePaths"] = {"site": state.settings.sitePath, "theme": state.settings.themePath};

		return returnObject;
	};
}

MRouterApp.childContextTypes = {
	"references": PropTypes.object
};

export let MRouterAppReduxConnected = connect(MRouterApp.mapStateToProps)(MRouterApp);
