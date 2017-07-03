const queryString = require('query-string');

var singletonIntance = null;

// import DevelopmentSettings from "oa/utils/development/DevelopmentSettings";
export default class DevelopmentSettings {

	constructor() {
		
		this._blockAllDebugSettings = false;
		this._ignoreSettings = false;
		
		this._debugSettings = new Object();
		this._debugData = new Object();
	}
	
	addDebugSetting(aId, aShouldDebug = true) {
		this._debugSettings[aId] = aShouldDebug;
	}
	
	addDebugData(aId, aData) {
		this._debugData[aId] = aData;
	}
	
	setMode(aMode) {
		switch(aMode) {
			case DevelopmentSettings.MODE_DEVELOPMENT:
				//MENOTE: do nothing
				break;
			case DevelopmentSettings.MODE_DEBUG:
				this._ignoreSettings = true;
				break;
			case DevelopmentSettings.MODE_PRODUCTION:
				this._blockAllDebugSettings = true;
				break;
			default:
				console.warn("Unkonown mode " + aMode + ". Using development.");
				break;
		}
	}
	
	shouldDebug(aId) {
		if(this._blockAllDebugSettings) {
			return false;
		}
		
		if(!this._ignoreSettings && this._debugSettings[aId]) {
			return true;
		}
		
		var parsedQueryString = queryString.parse(location.search);
		if(parsedQueryString["debug_" + aId] == "1") {
			return true;
		}
		
		return false;
	}
	
	getDebugData(aId) {
		var returnData = null;
		
		if(this._debugData[aId]) {
			returnData = this._debugData[aId];
		}
		
		var parsedQueryString = queryString.parse(location.search);
		if(parsedQueryString["debugData_" + aId]) {
			returnData = parsedQueryString["debugData_" + aId];
		}
		return returnData;
	}
}

DevelopmentSettings.getInstance = function() {
	if(!singletonIntance) {
		singletonIntance = new DevelopmentSettings();
	}
	
	return singletonIntance;
}

DevelopmentSettings.MODE_DEVELOPMENT = "development";
DevelopmentSettings.MODE_DEBUG = "debug";
DevelopmentSettings.MODE_PRODUCTION = "production";
