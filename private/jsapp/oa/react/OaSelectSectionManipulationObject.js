import React, {Component} from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

//import OaSelectSectionManipulationObject from "oa/react/OaSelectSectionManipulationObject";
export default class OaSelectSectionManipulationObject extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this._canBeEmpty = false;
		this._canHaveMultipleChildren = false;
		
		this._propsThatShouldNotCopy.push("selectedSections");
	}
	
	_isSectionActive(aSection, aActiveSection) {
		//console.log("oa/react/OaSelectSectionManipulationObject::_isSectionActive");
		//console.log(aSection, aActiveSection);
		
		return (aActiveSection.indexOf(aSection) !== -1);
	}
	
	_cloneChildAndAddProps() {
		//console.log("oa/react/OaSelectSectionManipulationObject::_cloneChildAndAddProps");
		
		//MENOTE: this.props.children can be undefiend, the only child or an array
		var children = this.props.children;
		if(children == undefined) {
			console.error("Object doesn't have any children. Can't render main element for ", this);
			//METODO: check for error display
			return null;
		}
		
		var selectedSections = this.props.selectedSections;
		if(typeof(selectedSections) === "string") {
			selectedSections = selectedSections.split(","); //METODO: trim whitespace
		}
		
		var availableChildren;
		if(children instanceof Array) {
			availableChildren = children;
		}
		else {
			availableChildren = [children];
		}
		
		var mainProps = this._getMainElementProps();
		
		var activeChildren = new Array();
		var currentArray = availableChildren;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentChild = currentArray[i];
			if(this._isSectionActive(currentChild.props.sectionName, selectedSections)) {
				activeChildren.push(this._performClone(currentChild, mainProps));
			}
		}
		
		if(activeChildren.length === 0) {
			if(!this._canBeEmpty && !this.props.canBeEmpty) {
				console.warn("Object doesn't have any active children.");
			}
			return null;
		}
		if(activeChildren.length > 1) {
			if(this._canHaveMultipleChildren) {
				return React.createElement(this._mainElementType, {},
					activeChildren
				);
			}
			
			console.warn("Object has to many active children. Using first active child for ", this);
		}
		return activeChildren[0];
	}
}
