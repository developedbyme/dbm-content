"use strict";

import React from "react";
import ReactDOM from "react-dom";

import GenericReactClassModuleCreator from "oa/GenericReactClassModuleCreator";

// import AdvancedSettingsModuleCreator from "oa/AdvancedSettingsModuleCreator";
export default class AdvancedSettingsModuleCreator extends GenericReactClassModuleCreator {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa.AdvancedSettingsModuleCreator::constructor");
		
		super();
		
		this._settingModules = new Object();
	}
	
	/**
	 * Adds a module for these settings
	 *
	 * @param	aId					String		The system id for the module.
	 * @param	aName				String		The name of the module.
	 * @param	aSettingFieldName	String		The name of the setting field.
	 * @param	aClass				ReactClass	The component to use for settings.
	 */
	addSettingsModule(aId, aName, aSettingFieldName, aClass) {
		
		this._settingModules[aId] = {"name": aName, "fieldName": aSettingFieldName, "reactClass": aClass};
		
	}
	
	/**
	 * Creates a new module
	 *
	 * aHolderNode	HTMLElement	The element to add the module to
	 * aData		Object		The dynamic data for the module
	 */
	createModule(aHolderNode, aData) {
		//console.log("oa.AdvancedSettingsModuleCreator::createModule");
		//console.log(aHolderNode, aData);
		
		var dataObject = new Object();
		
		for(var objectName in aData) {
			dataObject[objectName] = aData[objectName];
		}
		
		dataObject["availableModules"] = this._settingModules;
		
		return ReactDOM.render(React.createElement(this._reactClass, dataObject), aHolderNode);
	}
}