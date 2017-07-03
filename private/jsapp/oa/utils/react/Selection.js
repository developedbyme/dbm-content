import React from "react";

import OaBaseComponent from "oa/react/OaBaseComponent";

// import Selection from "oa/utils/react/Selection";
export default class Selection extends OaBaseComponent {

	constructor( props ) {
		super( props );
		
		this._mainElementType = "select";
		
		this._callback_changeBound = this._callback_change.bind(this);
	}
	
	_getMainElementProps() {
		var returnObject = super._getMainElementProps();
		
		returnObject["onChange"] = this._callback_changeBound;
		returnObject["value"] = this.props.selection;
		
		return returnObject;
	}
		
	componentWillMount() {
		
	}
	
	_callback_change(aEvent) {
		//console.log("Selection::_callback_change");
		//console.log(aEvent);
		//console.log(aEvent.target.value);
		
		var value = aEvent.target.value;
		var additionalData = this.props.additionalData;
		
		if(Array.isArray(this.props.options)) {
			var currentArray = this.props.options;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentData = currentArray[i];
				if(currentData.value == value) {
					if(currentData.additionalData !== undefined) {
						additionalData = currentData.additionalData;
					}
					break;
				}
			}
		}
		
		this.getReferences().getObject("value/" + this.props.valueName).updateValue(this.props.valueName, aEvent.target.value, additionalData);
	}

	_renderContentElement() {
		//console.log("Selection::_renderContentElement");
		
		var options = new Array();
		
		if(Array.isArray(this.props.options)) {
			var currentArray = this.props.options;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentObject = currentArray[i];
				
				if(typeof(currentObject) === "object") {
					options.push(<option key={currentObject["value"]} ref={"option-" + currentObject["value"]} value={currentObject["value"]}>{currentObject["label"]}</option>);
				}
				else {
					options.push(<option key={currentObject} value={currentObject}>{currentObject}</option>);
				}
			}
		}
		else {
			for(var objectName in this.props.options) {
				options.push(<option key={objectName} value={objectName}>{this.props.options[objectName]}</option>);
			}
		}
		
		return options;
	}

}
