// import PageSelector from "./oa/mrouter/data/PageSelector";
export default class PageSelector {
	
	constructor() {
		this.qualify = null;
		this.createPage = null;
		this.debugName = "Unnamed page selector";
	}
	
	setDebugName(aName) {
		this.debugName = aName;
	}
	
	setQualifierFunction(aFunction) {
		this.qualify = aFunction;
		
		return this;
	}
	
	setCreatePageFunction(aFunction) {
		this.createPage = aFunction;
		
		return this;
	}
	
	static create(aQualifyFunction, aCreateFunction, aDebugName) {
		var newPageSelector = new PageSelector();
		
		newPageSelector.setQualifierFunction(aQualifyFunction);
		newPageSelector.setCreatePageFunction(aCreateFunction);
		if(aDebugName) {
			newPageSelector.setDebugName(aDebugName);
		}
		
		return newPageSelector
	}
}