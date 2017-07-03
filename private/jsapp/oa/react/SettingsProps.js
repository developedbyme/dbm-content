import React from 'react';
import ReactDOM from 'react-dom';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";
import SourceData from "oa/reference/SourceData";

//import SettingsProps from "oa/react/SettingsProps";
export default class SettingsProps extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_getActiveSettings() {
		var currentSettings = SourceData.getPrefixedSource(this.props.source, this);
		if(currentSettings !== null && currentSettings !== undefined) {
			if(Array.isArray(currentSettings)) {
				return currentSettings;
			}
			//METODO: trim
			return currentSettings.split(",");
		}
		
		console.warn("No selected settings.");
		return [];
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/SettingsProps::_manipulateProps");
		
		var returnObject = super._manipulateProps(aReturnObject);
		
		var settings = this.props.settings;
		
		var currentArray = this._getActiveSettings();
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentSettingName = currentArray[i];
			var currentProps = settings[currentSettingName];
			if(currentProps) {
				for(var objectName in currentProps) {
					aReturnObject[objectName] = currentProps[objectName];
				}
			}
			else {
				console.warn("No setting named " + currentSettingName);
			}
		}
		
		delete aReturnObject["settings"];
		delete aReturnObject["selectedSettings"];
		
		return returnObject;
	}
}
