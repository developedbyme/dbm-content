import React, {Component} from 'react';
import { connect } from 'react-redux';

import {requestOaSiteDataMenu} from "oa/sitedata/store/actioncreators/oaSiteDataActionCreators";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import OaSiteDataMenu from "oa/sitedata/components/OaSiteDataMenu";
//import {OaSiteDataMenuReduxConnected} from "oa/sitedata/components/OaSiteDataMenu";
export default class OaSiteDataMenu extends Component {

	constructor (props) {
		super(props);
		this.state = {
		
		};
		
		this._mainElementType = "ul";
		this._classNames = new Array();
		
		this._references = new ReferencesHolder();
		this._menuItemContentCreatorPath = "contentCreatorClasses/oaSiteData/menuItem";
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
	
	componentWillMount() {
		//console.log("oa/sitedata/components/OaSiteDataMenu.componentWillMount");
		
		this.props.dispatch(requestOaSiteDataMenu(this.props.menuPosition));
	}

	componentDidMount() {
		//console.log("oa/sitedata/components/OaSiteDataMenu.componentDidMount");
	}

	componentWillUnmount() {
		//console.log("oa/sitedata/components/OaSiteDataMenu::componentWillUnmount");
	}
	
	_renderMenuItemChildren(aItem) {
		//console.log("oa/sitedata/components/OaSiteDataMenu::_renderMenuItemChildren");
		
		var children = aItem.children;
		
		if(children && children.length) {
			return React.createElement("ul", {"className": "sub-menu"},
				this._renderMenuItems(children)
			);
		}
		return null;
	}
	
	_renderMenuItems(aItems) {
		//console.log("oa/sitedata/components/OaSiteDataMenu::_renderMenuItems");
		//console.log(aItems);
		
		var returnArray = new Array();
		
		var currentArray = aItems;
		var currentArrayLength = currentArray.length;
		
		var MenuItemClass = this._references.getObject(this._menuItemContentCreatorPath);
		if(!MenuItemClass) {
			console.error("No class set for menu items (" + this._menuItemContentCreatorPath + ")");
			return null;
		}
		
		for(var i = 0; i < currentArrayLength; i++) {
			var currentData = currentArray[i];
			returnArray.push(
				React.createElement(MenuItemClass, {"key": "menu-item-" + i, "data": currentData},
					this._renderMenuItemChildren(currentData)
				)
			);
		}
		
		return returnArray;
	}
	
	_renderTopLevelMenuItems(aItems) {
		return this._renderMenuItems(aItems);
	}
	
	render() {
		
		this._references.setParent(this.getReferences());
		
		var classNames = this._getMainElementClassNames();
		
		return React.createElement(this._mainElementType, {"className": classNames.join(" ")},
			this._renderTopLevelMenuItems(this.props.menuItems)
		);
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/sitedata/components/OaSiteDataMenu.mapStateToProps (static)");
		//console.log(state, myProps);
		
		var menus = state.oaSiteData.menus;
		var menuPosition = myProps.menuPosition;
		var menuItems;
		
		if(menus[menuPosition] && menus[menuPosition].status === 1) {
			menuItems = menus[menuPosition].data;
		}
		else {
			menuItems = [];
		}
		
		return {
			"menuItems": menuItems
		};
	};
}

export let OaSiteDataMenuReduxConnected = connect(OaSiteDataMenu.mapStateToProps)(OaSiteDataMenu);
