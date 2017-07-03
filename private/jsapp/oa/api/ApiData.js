"use strict";

// import ApiData from "oa/api/ApiData";
export default class ApiData {

	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa/api/ApiData::constructor");

		this._apiBasePath = null;
		this._data = new Object();

	}

	setApiPath(aPath) {
		this._apiBasePath = aPath;

		return this;
	}

	addObject(aName, aObject) {
		this._data[aName] = aObject;

		return this;
	}

	_dataLoaded(aPath, aData) {
		var currentStorage = this._data[aPath];

		currentStorage["data"] = aData;
		currentStorage["loaded"] = true;

		var currentArray = currentStorage["callbacks"];
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentCallback = currentArray[i];
			currentCallback(aData);
		}

		currentArray.splice(0, currentArrayLength);
	}

	getData(aPath, aCallback) {
		//console.log("oa/api/ApiData::getObject");

		var currentStorage = this._data[aPath];
		if(currentStorage) {
			if(currentStorage["loaded"]) {
				aCallback(this._data[aPath]["data"]);
			}
			else {
				currentStorage["callbacks"].push(aCallback);
			}
		}
		else {

			var currentStorage = new Object();
			currentStorage["loaded"] = false;
			currentStorage["data"] = null;
			currentStorage["callbacks"] = new Array();
			currentStorage["callbacks"].push(aCallback);

			this._data[aPath] = currentStorage;

			jQuery.get( this._apiBasePath + aPath, function (result) {
				this._dataLoaded( aPath, result );
			}.bind(this));
		}
	}
}
