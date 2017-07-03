import ImageUpdater from "oa/imageloader/ImageUpdater";

//import ImgImageUpdater from "oa/imageloader/ImgImageUpdater";
export default class ImgImageUpdater extends ImageUpdater{
	
	constructor() {
		//console.log("oa/imageloader/ImgImageUpdater::constructor");
		
		super();
	}

	
	updateRatio() {
		//console.log("oa/imageloader/ImgImageUpdater::updateRatio");
		
		switch(this._settings["type"]) {
			case "full-width-auto-height":
				this._element.style.setProperty("width", (this._element.parentNode.clientWidth) + "px", "");
				this._element.style.setProperty("height", (this._element.parentNode.clientWidth/this._imageRatio) + "px", "");
				break;
			case "auto-height":
				this._element.style.setProperty("height", (this._element.clientWidth/this._imageRatio) + "px", "");
				break;
			default:
				console.warn("No ratio type " + this._settings["type"]);
				break;
		}
		
	}
	
	static create(aElement, aData, aSettings, aOwner) {
		//console.log("oa/imageloader/ImgImageUpdater::create");
		
		var newImgImageUpdater = new ImgImageUpdater();
		
		newImgImageUpdater.setupData(aElement, aData, aSettings);
		newImgImageUpdater.setOwner(aOwner);
		
		return newImgImageUpdater;
	}
}