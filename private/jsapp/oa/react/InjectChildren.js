import React from 'react';

var objectPath = require("object-path");

import FilterProps from "oa/react/FilterProps";

import SourceData from "oa/reference/SourceData";

//import InjectChildren from "oa/react/InjectChildren";
export default class InjectChildren extends FilterProps {

	constructor (props) {
		super(props);
		
		this._mainElementType = "div";
	}
	
	_getChildren() {
		var source = this.props.source ? this.props.source : "prop:dynamicChildren";
		
		return SourceData.getSourceWithType(source, this);
	}
	
	_performClone(aChild, aProps) {
		var callArray = [aChild, aProps];
		
		var children = this._getChildren();
		callArray = callArray.concat(children);
		
		return React.cloneElement.apply(React, callArray);
	}
	
	_cloneChildAndAddProps() {
		//console.log("oa/react/text/TextInjection::_cloneChildAndAddProps");
		
		//MENOTE: this.props.children can be undefiend, the only child or an array
		var children = this.props.children;
		if(children == undefined) {
			
			var newProps = this._getMainElementProps();
			var callArray = [this._getMainElementType(), newProps];
			
			var children = this._getChildren();
			callArray = callArray.concat(children);
			
			return React.createElement.apply(React, callArray);
		}
		
		var firstChild;
		if(children instanceof Array) {
			if(children.length > 1) {
				console.warn("Object has to many children. Using first element for ", this);
			}
			firstChild = children[0];
		}
		else {
			firstChild = children;
		}
		
		return this._performClone(firstChild, this._getMainElementProps());
	}
}
