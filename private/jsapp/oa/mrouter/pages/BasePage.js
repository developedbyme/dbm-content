import React from 'react';

import PropTypes from 'prop-types';

import OaBaseComponent from "oa/react/OaBaseComponent";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import BasePage from "oa/mrouter/pages/BasePage";
export default class BasePage extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._references = new ReferencesHolder();
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		//console.log("oa/mrouter/pages/BasePage::getChildContext")
		return {"references": this._references};
	}
	
	_renderDebugElement() {
		
		//var dataString = JSON.stringify(this.props.pageData, null, '\t');
		//return (<div className="m-router-debug-data"><pre><code>{dataString}</code></pre></div>);
		
		return null;
	}
	
	componentWillMount() {
		//console.log("oa/mrouter/pages/BasePage.componentWillMount");
		
		super.componentWillMount();
	}

	componentDidMount() {
		//console.log("oa/mrouter/pages/BasePage.componentDidMount");
		
		super.componentDidMount();
	}

	componentWillUnmount() {
		//console.log("oa/mrouter/pages/BasePage.componentWillUnmount");
		
		super.componentWillUnmount();
	}
	
	_addMainChildren(aReturnArray) {
		
		var currentArray = this._renderFunctions;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentFunction = currentArray[i];
			aReturnArray.push(currentFunction.call(this, this));
		}
	}
	
	_prepareRender() {
		super._prepareRender();
		
		this._references.setParent(this.context.references);
	}

	_renderMainElement() {
		
		return <wrapper>
			<h1>Unknown data structure</h1>
			{this._renderDebugElement()}
		</wrapper>;
	}
}

BasePage.childContextTypes = {
	"references": PropTypes.object
};
