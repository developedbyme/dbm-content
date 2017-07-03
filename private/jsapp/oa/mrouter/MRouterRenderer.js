//import MRouterRenderer from "oa/mrouter/MRouterRenderer";
export default class MRouterRenderer  {

	constructor() {
		
		this._renderPath = null;
		this._permalink = null;
		this._storeController = null;
		this._rootNode = null;
		
		this._timeoutId = -1;
		this._checkForRenderBound = this._checkForRender.bind(this);
	}
	
	setup(aRenderPath, aPermalink, aStoreController, aRootNode) {
		this._renderPath = aRenderPath;
		this._permalink = aPermalink;
		this._storeController = aStoreController;
		this._rootNode = aRootNode;
		
		return this;
	}
	
	_checkForRender() {
		if(this._storeController.getLoadingPaths().length > 0) {
			this._timeoutId = setTimeout(this._checkForRenderBound, 1);
		}
		else {
			var paths = this._storeController.getPaths();
			var seoRender = this._rootNode.innerHTML;
			console.log(seoRender);
			
			var loadPromise = fetch(this._renderPath, {
				"method": "POST",
				"body": JSON.stringify({"paths": paths, "permalink": this._permalink,"seoRender": seoRender}),
				"credentials": "include", 
				"headers": {
					"X-WP-Nonce": window.oaWpConfiguration.nonce,
					'Content-Type': 'application/json'
				}
			});
		}
	}
	
	startCheckingForRender() {
		console.log("oa/mrouter/MRouterRenderer::startCheckingForRender");
		
		this._timeoutId = setTimeout(this._checkForRenderBound, 1);
	}
}
