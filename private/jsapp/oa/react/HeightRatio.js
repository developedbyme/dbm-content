import React from 'react';
import ReactDOM from 'react-dom';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";
import SourceData from "oa/reference/SourceData";

//import HeightRatio from "oa/react/HeightRatio";
export default class HeightRatio extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this.state["height"] = 0;
		
		this._callback_sizeChangedBound = this._callback_sizeChanged.bind(this);
	}
	
	_updateHeight() {
		//console.log("oa/react/HeightRatio::_updateHeight");
		
		var currentWidth = ReactDOM.findDOMNode(this).clientWidth;
		
		var inputName = this.props.input ? this.props.input : "ratio";
		
		var ratio = this.props[inputName];
		
		if(isNaN(ratio)) {
			console.error("Height " + ratio + " is not a number", this);
			return;
		}
		
		var newHeight = ratio*currentWidth;
		
		if(newHeight !== this.state["height"]) {
			this.setState({"height": newHeight});
		}
	}
	
	_callback_sizeChanged(aEvent) {
		//console.log("oa/react/HeightRatio::_callback_sizeChanged");
		
		this._updateHeight();
	}
	
	componentDidMount() {
		//console.log("oa/react/HeightRatio::componentDidMount");
		
		this._updateHeight();
		
		window.addEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	componentDidUpdate() {
		//console.log("oa/react/HeightRatio::componentDidUpdate");
		
		this._updateHeight();
	}
	
	componentWillUnmount() {
		//console.log("oa/react/HeightRatio::componentWillUnmount");
		
		window.removeEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/HeightRatio::_manipulateProps");
		
		var returnObject = super._manipulateProps(aReturnObject);
		
		var outputName = this.props.output ? this.props.output : "height";
		
		returnObject[outputName] = this.state["height"];
		
		delete aReturnObject["ratio"];
		delete aReturnObject["input"];
		delete aReturnObject["output"];
		
		return returnObject;
	}
}
