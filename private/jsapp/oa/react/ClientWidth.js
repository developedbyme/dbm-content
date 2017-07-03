import React from 'react';
import ReactDOM from 'react-dom';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

//import ClientWidth from "oa/react/ClientWidth";
export default class ClientWidth extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this.state["width"] = 0;
		
		this._callback_sizeChangedBound = this._callback_sizeChanged.bind(this);
	}
	
	_updateWidth() {
		//console.log("oa/react/ClientWidth::_updateWidth");
		
		var currentWidth = ReactDOM.findDOMNode(this).clientWidth;
		
		if(currentWidth !== this.state["width"]) {
			this.setState({"width": currentWidth});
		}
	}
	
	_callback_sizeChanged(aEvent) {
		//console.log("oa/react/ClientWidth::_callback_sizeChanged");
		
		this._updateWidth();
	}
	
	componentDidMount() {
		//console.log("oa/react/ClientWidth::componentDidMount");
		
		this._updateWidth();
		
		window.addEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	componentDidUpdate() {
		//console.log("oa/react/ClientWidth::componentDidUpdate");
		
		this._updateWidth();
	}
	
	componentWillUnmount() {
		//console.log("oa/react/ClientWidth::componentWillUnmount");
		
		window.removeEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/ClientWidth::_manipulateProps");
		
		var returnObject = super._manipulateProps(aReturnObject);
		
		var outputName = this.props.output ? this.props.output : "width";
		
		returnObject[outputName] = this.state["width"];
		
		delete aReturnObject["output"];
		
		return returnObject;
	}
}
