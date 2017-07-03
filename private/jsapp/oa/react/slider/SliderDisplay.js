import React from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

//import SliderDisplay from "oa/react/slider/SliderDisplay";
export default class SliderDisplay extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._startItemIndex = 0;
		this._numberOfItems = 0;
		this._items = new Array();
	}
	
	_setupItems() {
		//console.log("oa/react/slider/SliderDisplay::_setupItems");
		
		var containerWidth = this.props.width;
		var itemWidth = this.getSourcedProp("itemWidth");
		if(!itemWidth) {
			itemWidth = containerWidth;
		}
		var spacing = (this.props.spacing ? this.props.spacing : 0);
		var position = this.props.position;
		
		//METODO: start position calculation
		
		this._items = new Array();
		
		if(containerWidth === 0) {
			return;
		}
		
		var numberOfItemsFitting = (containerWidth/itemWidth);
		
		var focusIndex = Math.floor(position);
		this._startItemIndex = focusIndex;
		
		if(focusIndex !== position) {
			numberOfItemsFitting++;
			/*
			if(focusIndex < position) {
				this._startItemIndex--;
			}
			*/
		}
		
		this._numberOfItems = numberOfItemsFitting;
		
		var references = this.getReferences();
		
		var contentCreator = this.getSourcedProp("contentCreator");
		
		for(var i = 0; i < this._numberOfItems; i++) {
			var returnArray = new Array();
			contentCreator({"index": this._startItemIndex+i}, this._startItemIndex+i, references, returnArray);
			
			this._items.push({"index": this._startItemIndex+i, "elements": returnArray});
		}
	}
	
	_getScreenPosition(aItemIndex) {
		var containerWidth = this.props.width;
		var itemWidth = this.getSourcedProp("itemWidth");
		if(!itemWidth) {
			itemWidth = containerWidth;
		}
		var spacing = (this.props.spacing ? this.props.spacing : 0);
		var position = this.props.position;
		
		var movementParameter = aItemIndex-position;
		var movement = (itemWidth+spacing)*movementParameter;
		
		return movement;
	}
	
	_renderPlacedItems() {
		var containerWidth = this.props.width;
		var itemWidth = this.getSourcedProp("itemWidth");
		if(!itemWidth) {
			itemWidth = containerWidth;
		}
		
		var placementClasses = "absolute-for-transform";
		if(this.props.placementClassName) {
			placementClasses += " " + this.props.placementClassName;
		}
		
		var returnArray = new Array();
		var currentArray = this._items;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentData = currentArray[i];
			
			var currentIndex = currentData.index;
			
			var screenPosition = this._getScreenPosition(currentIndex);
			
			var styleObject = {
				"transform": "translateX(" + screenPosition + "px)",
				"width": itemWidth
			}
			
			returnArray.push(<div className={placementClasses} key={"placement-" + currentIndex} style={styleObject} >
				{currentData.elements}
			</div>);
		}
		
		return returnArray;
	}
	
	_renderMainElement() {
		//console.log("oa/react/slider/SliderDisplay::_renderMainElement");
		
		this._setupItems();
		var placedItems = this._renderPlacedItems();
		
		return <wrapper>
			{placedItems}
		</wrapper>;
	}
}
