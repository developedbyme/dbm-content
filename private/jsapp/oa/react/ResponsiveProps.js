import React from 'react';
import ReactDOM from 'react-dom';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import ResponsiveProps from "oa/react/ResponsiveProps";
export default class ResponsiveProps extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this._selectedId = "";
		this.state["responsiveProps"] = new Object();
		
		this._callback_sizeChangedBound = this._callback_sizeChanged.bind(this);
	}
	
	_selectProps() {
		//console.log("oa/react/ResponsiveProps::_selectProps");
		
		var currentWidth = ReactDOM.findDOMNode(this).clientWidth;
		
		var selectedQueries = new Array();
		var selectedIndicies = new Array();
		
		var currentArray = this.props.mediaQueries;
		if(currentArray) {
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentMediaQuery = currentArray[i];
				if((currentMediaQuery.minWidth === undefined || currentWidth >= currentMediaQuery.minWidth) && (currentMediaQuery.maxWidth === undefined || currentWidth <= currentMediaQuery.maxWidth)) {
					selectedIndicies.push(i);
					selectedQueries.push(currentMediaQuery);
				}
			}
		}
		
		var newId = selectedIndicies.join("-");
		if(newId !== this._selectedId) {
			
			var stateResponsiveProps = new Object();
			
			var currentArray = selectedQueries;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentProps = currentArray[i]["props"];
				for(var objectName in currentProps) {
					stateResponsiveProps[objectName] = currentProps[objectName];
				}
			}
			
			this._selectedId = newId;
			this.setState({"responsiveProps": stateResponsiveProps});
		}
	}
	
	_callback_sizeChanged(aEvent) {
		//console.log("oa/react/ResponsiveProps::_callback_sizeChanged");
		
		this._selectProps();
	}
	
	componentDidMount() {
		//console.log("oa/react/ResponsiveProps::componentDidMount");
		
		this._selectProps();
		
		window.addEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	componentDidUpdate() {
		//console.log("oa/react/ResponsiveProps::componentDidUpdate");
		
		this._selectProps();
	}
	
	componentWillUnmount() {
		//console.log("oa/react/ResponsiveProps::componentWillUnmount");
		
		window.removeEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/ResponsiveProps::_manipulateProps");
		
		var returnObject = super._manipulateProps(aReturnObject);
		
		var responsiveProps = this.state["responsiveProps"];
		for(var objectName in responsiveProps) {
			returnObject[objectName] = responsiveProps[objectName];
		}
		
		delete aReturnObject["mediaQueries"];
		
		return returnObject;
	}
}
