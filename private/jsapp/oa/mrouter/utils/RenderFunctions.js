import React from 'react';

// import RenderFunctions from "oa/mrouter/utils/RenderFunctions";
export default class RenderFunctions {
	
}

RenderFunctions.createClassRenderFunction = function(aClass, aStaticProps) {
	//console.log("oa/mrouter/utils/RenderFunctions::createClassRenderFunction");
	
	var propsObject = new Object();
	if(aStaticProps) {
		for(var objectName in aStaticProps) {
			propsObject[objectName] = aStaticProps[objectName];
		}
	}
	
	var returnFunction = function(aThisObject) {
		
		propsObject["references"] = aThisObject.getReferences();
		
		return React.createElement(aClass, propsObject);
	}
	
	return returnFunction;
};