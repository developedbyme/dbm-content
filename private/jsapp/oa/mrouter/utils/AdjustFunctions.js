// import AdjustFunctions from "oa/mrouter/utils/AdjustFunctions";
export default class AdjustFunctions {
	
}

AdjustFunctions.rangeToOptions = function(aManipulationObject, aReturnObject) {
	//console.log("oa/mrouter/utils/AdjustFunctions::rangeToOptions");
	var rangeName = aManipulationObject.props.input ? aManipulationObject.props.input : "range";
	var optionsName = aManipulationObject.props.output ? aManipulationObject.props.output : "options";
	
	var returnArray = new Array();
	
	if(aManipulationObject.props.preValues) {
		if(Array.isArray(aManipulationObject.props.preValues)) {
			returnArray = returnArray.concat(aManipulationObject.props.preValues);
		}
		else {
			for(var objectName in aManipulationObject.props.preValues) {
				returnArray.push({"value": objectName, "label": aManipulationObject.props.preValues[objectName]});
			}
		}
		
	}
	
	var currentArray = aReturnObject[rangeName];
	if(currentArray) {
		currentArray = (new Array()).concat(currentArray);
		currentArray.sort(function(aA, aB) {
			aA = aA.title.toLowerCase();
			aB = aB.title.toLowerCase();
			if(aA < aB) {
				return -1
			}
			else if(aA > aB) {
				return 1;
			}
			return 0;
		});
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentItem = currentArray[i];
			returnArray.push({"value": currentItem.id, "label": currentItem.title, "additionalData": currentItem});
		}
	
		aReturnObject[optionsName] = returnArray;
		delete aReturnObject[rangeName];
	}
	else {
		console.warn("No range set");
	}
	
	delete aReturnObject["input"];
	delete aReturnObject["output"];
	delete aReturnObject["preValues"];
};

AdjustFunctions.heightToStyle = function(aManipulationObject, aReturnObject) {
	//console.log("oa/mrouter/utils/AdjustFunctions::heightToStyle");
	var inputName = aManipulationObject.props.input ? aManipulationObject.props.input : "height";
	var outputName = aManipulationObject.props.output ? aManipulationObject.props.output : "style";
	
	aReturnObject[outputName] = {"height": aManipulationObject.props[inputName]};
	
	delete aReturnObject["input"];
	delete aReturnObject["output"];
};

AdjustFunctions.booleanToName = function(aManipulationObject, aReturnObject) {
	//console.log("oa/mrouter/utils/AdjustFunctions::heightToStyle");
	var inputName = aManipulationObject.props.input ? aManipulationObject.props.input : "value";
	var outputName = aManipulationObject.props.output ? aManipulationObject.props.output : "value";
	
	var convertedValue = aManipulationObject.props[inputName] ? aManipulationObject.props["trueValue"] : aManipulationObject.props["falseValue"];
	
	delete aReturnObject["value"];
	
	aReturnObject[outputName] = convertedValue;
	
	delete aReturnObject["input"];
	delete aReturnObject["output"];
	delete aReturnObject["trueValue"];
	delete aReturnObject["falseValue"];
};

AdjustFunctions.containSizeFromImageData = function(aManipulationObject, aReturnObject) {
	console.log("oa/mrouter/utils/AdjustFunctions::containSizeFromImageData");
	
	var imageData = aManipulationObject.getSourcedProp("input");
	var maxWidth = aManipulationObject.getSourcedProp("maxWidth");
	var maxHeight = aManipulationObject.getSourcedProp("maxHeight");
	
	if(!imageData) {
		console.warn("No image data set, can't calculate contain size.");
		aReturnObject["width"] = 0;
		aReturnObject["height"] = 0;
		return;
	}
	
	var fullSize = imageData.sizes.full;
	var imageWidth = fullSize.width;
	var imageHeight = fullSize.height;
	
	console.log(imageData);
	console.log(maxWidth, maxHeight);
	
	if(imageWidth <= maxWidth && imageHeight <= maxHeight) {
		aReturnObject["width"] = imageWidth;
		aReturnObject["height"] = imageHeight;
		return;
	}
	
	var imageRatio = imageWidth/imageHeight;
	var containRatio = maxWidth/maxHeight;
	if(imageRatio >= containRatio) {
		aReturnObject["width"] = maxWidth;
		aReturnObject["height"] = maxWidth/imageRatio;
	}
	else {
		aReturnObject["width"] = imageRatio*maxHeight;
		aReturnObject["height"] = maxHeight;
	}
	
	delete aReturnObject["maxWidth"];
	delete aReturnObject["maxHeight"];
	
	console.log(aReturnObject);
}

AdjustFunctions.styleFromSize = function(aManipulationObject, aReturnObject) {
	console.log("oa/mrouter/utils/AdjustFunctions::styleFromSize");
	
	var width = aManipulationObject.getSourcedProp("width");
	var height = aManipulationObject.getSourcedProp("height");
	
	if(!aReturnObject["style"]) {
		aReturnObject["style"] = new Object();
	}
	
	aReturnObject["style"]["width"] = width;
	aReturnObject["style"]["height"] = height;
	
	console.log(aReturnObject["style"]);
}