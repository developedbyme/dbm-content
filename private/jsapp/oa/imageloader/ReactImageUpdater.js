import ImageUpdater from "oa/imageloader/ImageUpdater";

//import ReactImageUpdater from "oa/imageloader/ReactImageUpdater";
export default class ReactImageUpdater extends ImageUpdater {
	
	constructor() {
		//console.log("oa/imageloader/ReactImageUpdater::constructor");
		
		super();
		
		this._component = null;
	}
	
	setComponent(aCompoennt) {
		//console.log("oa/imageloader/ReactImageUpdater::setComponent");
		
		this._component = aCompoennt;
		
		return this;
	}
	
	_setRenderData(aRenderedData) {
		//console.log("oa/imageloader/ReactImageUpdater::_setRenderData");
		
		this._component.setState({
			"imageStatus": 1,
			"renderedImage": aRenderedData
		})
	}
	
	static create(aComponent, aElement, aData, aSettings, aOwner) {
		//console.log("oa/imageloader/ReactImageUpdater::create");
		
		var newReactImageUpdater = new ReactImageUpdater();
		
		newReactImageUpdater.setComponent(aComponent);
		newReactImageUpdater.setupData(aElement, aData, aSettings);
		newReactImageUpdater.setOwner(aOwner);
		
		return newReactImageUpdater;
	}
}