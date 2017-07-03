import React from 'react';

import PageSelector from "../data/PageSelector";

// import PageSelectorCreator from "oa/mrouter/utils/PageSelectorCreator";
export default class PageSelectorCreator {
	
	constructor() {
		console.warn("oa/mrouter/utils/PageSelectorCreator is a static class and should not be instanciated");
	}
	
	static createLoadingStatusPage(aStatus, aPageComponent, aReference, aDebugName) {
		var qualifyFunction = function(aMRouterLoadingData) {
			return (aMRouterLoadingData.status === aStatus);
		}
		
		var createFunction = function(aMRouterLoadingData) {
			return React.createElement(aPageComponent, {"loadingData": aMRouterLoadingData, "references": aReference});
		}
		
		if(!aDebugName) {
			aDebugName = "Loading status " + aStatus;
		}
		
		return PageSelector.create(qualifyFunction, createFunction, aDebugName);
	}
	
	static createTemplateSelection(aTemplateSelectionName, aTemplateSelectionValue, aPageComponent, aReference, aDebugName) {
		var qualifyFunction = function(aMRouterLoadingData) {
			if(aMRouterLoadingData.status === 1) {
				return (aMRouterLoadingData.data.templateSelection[aTemplateSelectionName] === aTemplateSelectionValue);
			}
			return false;
		}
		
		var createFunction = function(aMRouterLoadingData) {
			return React.createElement(aPageComponent, {"pageData": aMRouterLoadingData.data, "references": aReference});
		}
		
		if(!aDebugName) {
			aDebugName = "Tempalte selection " + aTemplateSelectionName + " = " + aTemplateSelectionValue;
		}
		
		return PageSelector.create(qualifyFunction, createFunction, aDebugName);
	}
	
	static createPageTemplateSelection(aTemplateFile, aPageComponent, aReference, aDebugName) {
		var qualifyFunction = function(aMRouterLoadingData) {
			if(aMRouterLoadingData.status === 1) {
				var returnValue = (aMRouterLoadingData.data.templateSelection["is_page"] === true) && (aMRouterLoadingData.data.queriedData.meta._wp_page_template) && (aMRouterLoadingData.data.queriedData.meta._wp_page_template[0] === aTemplateFile);
				return returnValue;
			}
			return false;
		}
		
		var createFunction = function(aMRouterLoadingData) {
			return React.createElement(aPageComponent, {"pageData": aMRouterLoadingData.data, "references": aReference});
		}
		
		if(!aDebugName) {
			aDebugName = "Page tempalte selection " + aTemplateFile;
		}
		
		return PageSelector.create(qualifyFunction, createFunction, aDebugName);
	}
	
	static createCustomPostTypeSelection(aCustomPostType, aPageComponent, aReference, aDebugName) {
		var qualifyFunction = function(aMRouterLoadingData) {
			if(aMRouterLoadingData.status === 1) {
				var returnValue = (aMRouterLoadingData.data.templateSelection["is_single"] === true) &&  (aMRouterLoadingData.data.templateSelection["post_type"] === aCustomPostType);
				return returnValue;
			}
			return false;
		}
		
		var createFunction = function(aMRouterLoadingData) {
			return React.createElement(aPageComponent, {"pageData": aMRouterLoadingData.data, "references": aReference});
		}
		
		if(!aDebugName) {
			aDebugName = "Custom post type selection " + aCustomPostType;
		}
		
		return PageSelector.create(qualifyFunction, createFunction, aDebugName);
	}
	
	static createList(aPageComponent, aReference, aDebugName) {
		//console.log("oa/mrouter/utils/PageSelectorCreator::createList");
		//console.log(aPageComponent, aReference, aDebugName);
		
		var qualifyFunction = function(aMRouterLoadingData) {
			if(aMRouterLoadingData.status === 1) {
				return (aMRouterLoadingData.data.posts.length > 0);
			}
			return false;
		}
		
		var createFunction = function(aMRouterLoadingData) {
			return React.createElement(aPageComponent, {"pageData": aMRouterLoadingData.data, "references": aReference});
		}
		
		if(!aDebugName) {
			aDebugName = "List";
		}
		
		return PageSelector.create(qualifyFunction, createFunction, aDebugName);
	}
	
	static createDefaultPage(aPageComponent, aReference, aDebugName) {
		var qualifyFunction = function(aMRouterLoadingData) {
			return (aMRouterLoadingData.status === 1);
		}
		
		var createFunction = function(aMRouterLoadingData) {
			return React.createElement(aPageComponent, {"pageData": aMRouterLoadingData.data, "references": aReference});
		}
		
		if(!aDebugName) {
			aDebugName = "Loading status " + aStatus;
		}
		
		return PageSelector.create(qualifyFunction, createFunction, aDebugName);
	}
	
	static createCatchAllPage(aPageComponent, aReference, aDebugName) {
		var qualifyFunction = function(aMRouterLoadingData) {
			return true;
		}
		
		var createFunction = function(aMRouterLoadingData) {
			return React.createElement(aPageComponent, {"loadingData": aMRouterLoadingData, "references": aReference});
		}
		
		if(!aDebugName) {
			aDebugName = "Catch all";
		}
		
		return PageSelector.create(qualifyFunction, createFunction, aDebugName);
	}
}