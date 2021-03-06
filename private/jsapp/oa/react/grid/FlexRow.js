import React from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

//import FlexRow from "oa/react/grid/FlexRow";
export default class FlexRow extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._mainElementType = "div";
		
		this._addMainElementClassName("flex-row");
	}
	
	_getChildren() {
		//console.log("oa/react/grid/FlexRow::_getChildren");
		
		var children = this.props.dynamicChildren ? this.props.dynamicChildren : this.props.children;
		
		if(children) {
			var returnArray = Array.isArray(children) ? children : [children];
			return returnArray;
		}
		
		return null;
	}
	
	_getItemClassName(aChild, aKeyIndex) {
		
		var returnString = "flex-row-item";
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
		if(currentArray) {
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentChild = currentArray[i];
				returnArray.push(this._getFlexItem(currentChild, i));
			}
		}
		else {
			console.warn("Row doesn't have any elements.", this);
		}
		
		return returnArray;
	}

	_renderMainElement() {
		return <wrapper>
			{this._getFlexItems()}
		</wrapper>;
	}
}