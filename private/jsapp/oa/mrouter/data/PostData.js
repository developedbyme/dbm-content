import objectPath from "object-path";

// import PostData from "oa/mrouter/data/PostData";
export default class PostData {
	
	constructor() {
		this._data = null;
	}
	
	setData(aData) {
		//console.log("oa/mrouter/data/PostData::setData");
		this._data = aData;
		
		return this;
	}
	
	getData() {
		return this._data;
	}
	
	getId() {
		//console.log("oa/mrouter/data/PostData::getId");
		//console.log(this._data);
		
		return this._data.id;
	}
	
	getTitle() {
		//console.log("oa/mrouter/data/PostData::getTitle");
		//console.log(this._data);
		
		return this._data.title;
	}
	
	getExcerpt() {
		//console.log("oa/mrouter/data/PostData::getExcerpt");
		//console.log(this._data);
		
		return this._data.excerpt;
	}
	
	getImage() {
		//console.log("oa/mrouter/data/PostData::getImage");
		//console.log(this._data);
		
		return this._data.image;
	}
	
	getContent() {
		return this._data.content;
	}
	
	getPermalink() {
		return this._data.permalink;
	}
	
	getType() {
		return this._data.type;
	}
	
	getMetaData(aField) {
		if(this._data.meta) {
			return objectPath.get(this._data.meta, aField);
		}
		
		console.warn("No meta data set. Can't get field " + aField);
		return null;
	}
	
	getAcfData() {
		//console.log("oa/mrouter/data/PostData::getAcfData");
		//console.log(this._data);
		
		if(this._data && this._data.acf) {
			
			var passedPath = new Array();
			var currentObject = this._data.acf;
			
			var currentArray = arguments;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentPathPart = currentArray[i];
				
				passedPath.push(currentPathPart);
				
				if(!currentObject || !currentObject[currentPathPart]) {
					console.warn("No acf field for path", passedPath);
					return null;
				}
				
				if(typeof(currentPathPart) === "string") {
					currentObject = currentObject[currentPathPart].value;
				}
				else {
					currentObject = currentObject[currentPathPart];
				}
			}
			
			return currentObject;
		}
		
		console.warn("No acf data set");
		return null;
	}
	
	getAcfSubfieldData(aStartObject) {
		var passedPath = new Array();
		var currentObject = aStartObject;
		
		var currentArray = arguments;
		var currentArrayLength = currentArray.length;
		for(var i = 1; i < currentArrayLength; i++) {
			var currentPathPart = currentArray[i];
			
			passedPath.push(currentPathPart);
			
			if(!currentObject || !currentObject[currentPathPart]) {
				console.warn("No acf field for path", passedPath, "from start object", aStartObject);
				return null;
			}
			
			if(typeof(currentPathPart) === "string") {
				currentObject = currentObject[currentPathPart].value;
			}
			else {
				currentObject = currentObject[currentPathPart];
			}
		}
		
		return currentObject;
	}
	
	getTaxonomyMap(aTermsArray) {
		
		var returnObject = new Object();
		
		if(aTermsArray) {
			var currentArray = aTermsArray;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentData = currentArray[i];
				returnObject[currentData.slug] = currentData.name;
			}
		}
		else {
			console.warn("Terms array is not set");
		}
		
		return returnObject;
	}
	
	getAddOnsData(aField) {
		if(this._data.addOns) {
			return objectPath.get(this._data.addOns, aField);
		}
		
		console.warn("No add ons set. Can't get field " + aField);
		return null;
	}
	
	getFirstObjectInArray(aArray) {
		if(aArray && aArray.length > 0) {
			return aArray[0];
		}
		return null;
	}
	
	getRowIndexByFieldValue(aRows, aFieldName, aValue) {
		var currentArray = aRows;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentRow = currentArray[i];
			var currentValue = this.getAcfSubfieldData(currentRow, aFieldName);
			if(currentValue === aValue) {
				return i;
			}
		}
		return -1;
	}
	
	getRowByFieldValue(aRows, aFieldName, aValue) {
		var index = this.getRowIndexByFieldValue(aRows, aFieldName, aValue);
		if(index === -1) {
			return null;
		}
		return aRows[index];
	}
	
	getPrimaryTerm(aTaxonomy) {
		if(this._data && this._data.terms) {
			var firstTerm = this.getFirstObjectInArray(this._data.terms[aTaxonomy]);
			
			return firstTerm;
		}
		
		console.warn("No terms set");
		return null;
	}
	
	hasTermSlug(aSlug, aTaxonomy) {
		if(this._data && this._data.terms) {
			var currentArray = this._data.terms[aTaxonomy];
			if(currentArray) {
				var currentArrayLength = currentArray.length;
				for(var i = 0; i < currentArrayLength; i++) {
					var currentTerm = currentArray[i];
					if(currentTerm.slug === aSlug) {
						return true;
					}
				}
			}
			return false;
		}
		
		console.warn("No terms set");
		return false;
	}
	
	static create(aData) {
		var newPostData = new PostData();
		
		newPostData.setData(aData);
		
		return newPostData
	}
}