import React, {Component} from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

//import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";
export default class OaDataManipulationBaseObject extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._propsThatShouldNotCopy = new Array();
		this._propsThatShouldNotOverride = new Array(); //METODO: implement this
		
		this._clonedElement = null;
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/OaDataManipulationBaseObject::_removeUsedProps");
		
		//MENOTE: should be overridden
		
		return aReturnObject;
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/OaDataManipulationBaseObject::_manipulateProps");
		
		//MENOTE: should be overridden
		
		return aReturnObject;
	}
	
	_getMainElementProps() {
		//console.log("oa/react/OaDataManipulationBaseObject::_getMainElementProps");
		var returnObject = super._getMainElementProps();

		for(var objectName in this.props) {
			if(objectName === "className") {
				//MENOTE: className is copied in _getMainElementProps
				continue;
			}
			if(this._propsThatShouldNotCopy.indexOf(objectName) !== -1) {
				continue;
			}
			returnObject[objectName] = this.props[objectName];
		}
		
		returnObject = this._removeUsedProps(returnObject);
		returnObject = this._manipulateProps(returnObject);
		
		return returnObject;
	}
	
	_performCloneWithNewChildren(aChild, aProps, aChildren) {
		var callArray = [aChild, aProps];
		
		callArray = callArray.concat(aChildren);
		
		return React.cloneElement.apply(React, callArray);
	}
	
	_performClone(aChild, aProps) {
		var callArray = [aChild, aProps];
		
		var firstChildChildren = aChild.props.children;
		if(!firstChildChildren) {
			callArray.push(null);
		}
		else if(firstChildChildren instanceof Array) {
			callArray = callArray.concat(firstChildChildren);
		}
		else {
			callArray.push(firstChildChildren);
		}
		
		return React.cloneElement.apply(React, callArray);
	}
	
	_getInputChildrenForComponent(aComponent) {
		//MENOTE: aComponent.props.children can be undefiend, the only child or an array
		var children = aComponent.props.children;
		if(children == undefined) {
			return [];
		}
		
		if(children instanceof Array) {
			return children;
		}
		else {
			return [children];
		}
	}
	
	_getInputChild() {
		//MENOTE: this.props.children can be undefiend, the only child or an array
		var children = this.props.children;
		if(children == undefined) {
			
			return null;
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
		
		return firstChild;
	}
	
	_getChildToClone() {
		return this._getInputChild();
	}
	
	_cloneChildAndAddProps() {
		//console.log("oa/react/OaDataManipulationBaseObject::_cloneChildAndAddProps");
		
		var firstChild = this._getChildToClone();
		if(firstChild === null) {
			console.error("Object doesn't have any children. Can't render main element for ", this);
			//METODO: check for error display
			return null;
		}
		
		return this._performClone(firstChild, this._getMainElementProps());
	}
	
	_createClonedElement() {
		//console.log("oa/react/OaDataManipulationBaseObject::_createClonedElement");
		
		this._clonedElement = this._cloneChildAndAddProps();
	}
	
	_renderMainElement() {
		this._createClonedElement();
		return this._clonedElement;
	}
	
	_prepareRender() {
		super._prepareRender();
	}
}
