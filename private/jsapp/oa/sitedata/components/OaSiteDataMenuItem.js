import React, {Component} from 'react';
import { connect } from 'react-redux';

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import OaSiteDataMenuItem from "oa/sitedata/components/OaSiteDataMenuItem";
//import {OaSiteDataMenuItemReduxConnected} from "oa/sitedata/components/OaSiteDataMenuItem";
export default class OaSiteDataMenuItem extends Component {

	constructor (props) {
		super(props);
		this.state = {

		};

		this._mainElementType = "li";
		this._classNames = new Array();

		this._references = new ReferencesHolder();
        this.toggleActive = this.toggleActive.bind( this );
	}

    toggleActive(e) {
        e.preventDefault();

        // Set sub nav toggle to active
        var target = e.target.parentNode.parentNode;
        target.classList.toggle( 'active' );

        // Set the list item parent of the nav-toggle to displays-children
        var targetParent = e.target.parentNode.parentNode.parentNode;
        targetParent.classList.toggle( 'displays-children' );
    }

	_addMainElementClassName(aName) {
		this._classNames.push(aName);
	}

	_getMainElementClassNames() {
		var returnArray = new Array();

		var currentArray = this._classNames;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			returnArray.push(currentArray[i]);
		}

		return returnArray;
	}

	escapeString (textString) {
    var element = document.createElement('textarea');
    element.innerHTML = textString;
    return element.value;
  }

	componentWillMount() {
		//console.log("oa/sitedata/components/OaSiteDataMenuItem.componentWillMount");
	}

	componentDidMount() {
		//console.log("oa/sitedata/components/OaSiteDataMenuItem.componentDidMount");
	}

	componentWillUnmount() {
		//console.log("oa/sitedata/components/OaSiteDataMenuItem::componentWillUnmount");
	}

	_renderContent(aData) {
		//console.log("oa/sitedata/components/OaSiteDataMenuItem::_renderContent");
		//console.log(aData);

        var returnArray = new Array();

		returnArray.push(<a key={Math.floor((Math.random() * 10000) + 1)} href={aData.link} title={this.escapeString(aData.alt)} className={aData.css_classes.join(" ")}>{this.escapeString(aData.title)}</a>);

        if ( aData.children && aData.children.length ) {
            returnArray.push(<div key={Math.floor((Math.random() * 10000) + 1)} className="sub-nav-toggle hexagon" onClick={this.toggleActive}>
                <div className="hexagon-inner">
                    <div className="hexagon-content"></div>
                </div>
            </div>);
        }

		return returnArray;
	}

	render() {

		this._references.setParent(this.getReferences());

		var classNames = this._getMainElementClassNames();
        var subNavToggle = '';

		if (this.props.data.children && this.props.data.children.length) {
			classNames.push('has-children');
		}

		if (this.props.data.link === window.location.href) {
			classNames.push('active');
		}

		return React.createElement(this._mainElementType, {"className": classNames.join(" ")},
			this._renderContent(this.props.data),
			this.props.children
		);
	}

	static mapStateToProps(state, myProps) {
		//console.log("oa/sitedata/components/OaSiteDataMenuItem.mapStateToProps (static)");
		//console.log(state, myProps);

		return {

		};
	};
}

export let OaSiteDataMenuItemReduxConnected = connect(OaSiteDataMenuItem.mapStateToProps)(OaSiteDataMenuItem);
