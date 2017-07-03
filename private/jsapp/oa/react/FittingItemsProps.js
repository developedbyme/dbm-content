import React from 'react';
import ReactDOM from 'react-dom';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

//import FittingItemsProps from "oa/react/FittingItemsProps";
export default class FittingItemsProps extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this.state["numberOfItems"] = -1;
		this.state["adjustedItemWidth"] = -1;
		this.state["responsiveProps"] = new Object();
		
		this._callback_sizeChangedBound = this._callback_sizeChanged.bind(this);
	}
	
	_selectProps() {
		//console.log("oa/react/FittingItemsProps::_selectProps");
		
		var currentWidth = ReactDOM.findDOMNode(this).clientWidth;
		
		var itemWidth = this.props.itemWidth;
		var spacing = this.props.spacing;
		
		var extraWidth = Math.max(0, currentWidth-itemWidth);
		
		var numberOfFittingItems = 1+Math.floor(extraWidth/(itemWidth+spacing));
		
		if(this.props.maxNumberOfItems) {
			numberOfFittingItems = Math.min(numberOfFittingItems, this.props.maxNumberOfItems);
		}
		
		var adjustedItemWidth = (currentWidth-((numberOfFittingItems-1)*spacing))/numberOfFittingItems;
		
		var selectedProps = null;
		if(this.props.propsByNumberOfItems && this.props.propsByNumberOfItems[numberOfFittingItems-1]) {
			selectedProps = this.props.propsByNumberOfItems[numberOfFittingItems-1];
		}
		
		if(numberOfFittingItems !== this.state["numberOfItems"] || adjustedItemWidth !== this.state["adjustedItemWidth"]) {
			this.setState({"numberOfItems": numberOfFittingItems, "adjustedItemWidth": adjustedItemWidth, "responsiveProps": selectedProps});
		}
	}
	
	_callback_sizeChanged(aEvent) {
		//console.log("oa/react/FittingItemsProps::_callback_sizeChanged");
		
		this._selectProps();
	}
	
	componentDidMount() {
		//console.log("oa/react/FittingItemsProps::componentDidMount");
		
		this._selectProps();
		
		window.addEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	componentDidUpdate() {
		//console.log("oa/react/FittingItemsProps::componentDidUpdate");
		
		//METODO: this is causing a loop
		//this._selectProps();
	}
	
	componentWillUnmount() {
		//console.log("oa/react/FittingItemsProps::componentWillUnmount");
		
		window.removeEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/FittingItemsProps::_manipulateProps");
		
		var returnObject = super._manipulateProps(aReturnObject);
		
		returnObject["itemsPerRow"] = this.state["numberOfItems"];
		returnObject["adjustedItemWidth"] = this.state["adjustedItemWidth"];
		
		var responsiveProps = this.state["responsiveProps"];
		if(responsiveProps) {
			for(var objectName in responsiveProps) {
				returnObject[objectName] = responsiveProps[objectName];
			}
		}
		
		delete aReturnObject["itemWidth"];
		delete aReturnObject["spacing"];
		delete aReturnObject["propsByNumberOfItems"];
		
		return returnObject;
	}
}
