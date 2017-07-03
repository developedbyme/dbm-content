import React from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

//import FlexColumn from "oa/react/grid/FlexColumn";
export default class FlexColumn extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._mainElementType = "div";
		
		this._addMainElementClassName("flex-column");
	}
	
	_getChildren() {
		//console.log("oa/react/grid/FlexColumn::_getChildren");
		
		var children = this.props.dynamicChildren ? this.props.dynamicChildren : this.props.children;
		
		if(children) {
			var returnArray = Array.isArray(children) ? children : [children];
			return returnArray;
		}
		
		return null;
	}
	
	_getItemClassName(aChild, aKeyIndex) {
		
		var returnString = "flex-column-item";
		if(this.props.itemClasses && this.props.itemClasses[aKeyIndex]) {
			returnString += " " + this.props.itemClasses[aKeyIndex];
		}
		
		return returnString;
	}
	
	_getFlexItem(aChild, aKeyIndex) {
		return <div key={"flex-item-"+aKeyIndex} className={this._getItemClassName(aChild, aKeyIndex)}>{aChild}</div>;
	}
	
	_getFlexItems() {
		
		var returnArray = new Array();
		
		var currentArray = this._getChildren();
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentChild = currentArray[i];
			returnArray.push(this._getFlexItem(currentChild, i));
		}
		
		return returnArray;
	}

	_renderMainElement() {
		return <wrapper>
			{this._getFlexItems()}
		</wrapper>;
	}
}