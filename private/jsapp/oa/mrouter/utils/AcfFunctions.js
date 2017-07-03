import objectPath from "object-path";

// import AcfFunctions from "oa/mrouter/utils/AcfFunctions";
export default class AcfFunctions {
	
	static getAcfSubfieldData(aStartObject) {
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
	
	static getFirstObjectInArray(aArray) {
		if(aArray && aArray.length > 0) {
			return aArray[0];
		}
		return null;
	}
	
	static getRowIndexByFieldValue(aRows, aFieldName, aValue) {
		var currentArray = aRows;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentRow = currentArray[i];
			var currentValue = AcfFunctions.getAcfSubfieldData(currentRow, aFieldName);
			if(currentValue === aValue) {
				return i;
			}
		}
		return -1;
	}
	
	static getRowByFieldValue(aRows, aFieldName, aValue) {
		var index = AcfFunctions.getRowIndexByFieldValue(aRows, aFieldName, aValue);
		if(index === -1) {
			return null;
		}
		return aRows[index];
	}
}