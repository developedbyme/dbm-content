import React from 'react';

var objectPath = require("object-path");

import FilterProps from "oa/react/FilterProps";

import SourceData from "oa/reference/SourceData";

//import TextInjection from "oa/react/text/TextInjection";
export default class TextInjection extends FilterProps {

	constructor (props) {
		super(props);
		
		this._mainElementType = "span";
		
		this._propsThatShouldNotCopy.push("source");
	}
	
	_getText() {
		
		return SourceData.getSourceWithType(this.props.source, this);
		
	}
	
	_performClone(aChild, aProps) {
		var callArray = [aChild, aProps];
		
		var child = null;
		
		if(this.props.format === "html") {
			aProps["dangerouslySetInnerHTML"] = {"__html": this._getText()};
		}
		else {
			child = TextInjection.escapeString(this._getText());
		}
		
		callArray.push(child);
		
		return React.cloneElement.apply(React, callArray);
	}
	
	_cloneChildAndAddProps() {
		//console.log("oa/react/text/TextInjection::_cloneChildAndAddProps");
		
		//MENOTE: this.props.children can be undefiend, the only child or an array
		var children = this.props.children;
		if(children == undefined) {
			
			var newProps = this._getMainElementProps();
			var child = null;
			
			if(this.props.format === "html") {
				newProps["dangerouslySetInnerHTML"] = {"__html": this._getText()};
			}
			else {
				child = TextInjection.escapeString(this._getText());
			}
			
			return React.createElement(this._getMainElementType(), newProps,
				child
			);
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
	
	static escapeString(aText) {
		if(!TextInjection.tempTextArea) {
			TextInjection.tempTextArea = document.createElement("textarea");
		}
		
		TextInjection.tempTextArea.innerHTML = aText;
		return TextInjection.tempTextArea.value;
	}
}

TextInjection.tempTextArea = null;