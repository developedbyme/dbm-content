"use strict";

// import ReferencesHolder from "oa/reference/ReferencesHolder";
export default class ReferencesHolder {
	
	/**
	 * Constructor
	 */
	constructor() {
		//console.log("oa/reference/ReferencesHolder::constructor");
		
		this._objects = new Object();
		this._parentReferenceHolder = null;
	}
	
	setParent(aParent) {
		this._parentReferenceHolder = aParent;
		
		return this;
	}
	
	addObject(aName, aObject) {
		this._objects[aName] = aObject;
		
		return this;
	}
	
	
	getObject(aName) {
		//console.log("oa/reference/ReferencesHolder::getObject");
		
		if(!this._objects[aName]) {
			if(this._parentReferenceHolder) {
				return this._parentReferenceHolder.getObject(aName);
			}
			console.warn("Reference " + aName + " doesn't exist.");
			return null;
		}
		
		return this._objects[aName];
	}
}