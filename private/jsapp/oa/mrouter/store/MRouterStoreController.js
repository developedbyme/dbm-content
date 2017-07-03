// import MRouterStoreController from "oa/mrouter/store/MRouterStoreController";
export default class MRouterStoreController {
	
	constructor() {
		console.log("oa/mrouter/store/MRouterStoreController::constructor");
		console.log(">>>>>>>>>>>>>>>", this);
		
		this._loadingPaths = new Array();
		this._paths = new Array();
		
		this._store = null;
		
		this._encodeLoadedDataBound = this._encodeLoadedData.bind(this);
	}
	
	setStore(aStore) {
		this._store = aStore;
		
		return  this;
	}
	
	getPaths() {
		return this._paths;
	}
	
	getLoadingPaths() {
		return this._loadingPaths;
	}
	
	_performDispatch(aType, aPath, aData) {
		this._store.dispatch({
			"type": aType,
			"path": aPath,
			"data": aData,
			"timeStamp": Date.now()
		});
	}
	
	_load(aPath) {
		var loadPromise = fetch(aPath, {credentials: "include", "headers": {"X-WP-Nonce": window.oaWpConfiguration.nonce}});
		return loadPromise;
	}
	
	_encodeLoadedData(aResponse) {
		//console.log("oa/mrouter/store/MRouterStoreController::_encodeLoadedData");
		
		var returnObject = aResponse.json();
		
		return returnObject;
	}
	
	_removeLoadingPath(aPath) {
		var currentIndex = this._loadingPaths.indexOf(aPath);
		if(currentIndex !== -1) {
			this._loadingPaths.splice(currentIndex, 1);
		}
		console.log(this._loadingPaths);
	}
	
	_dataLoaded(aPath, aData) {
		//console.log("oa/mrouter/store/MRouterStoreController::_dataLoaded");
		
		this._performDispatch(MRouterStoreController.LOADED, aPath, aData);
		this._removeLoadingPath(aPath);
	}
	
	_loadingError(aPath, aError) {
		//console.log("oa/mrouter/store/MRouterStoreController::_loadingError");
		
		this._performDispatch(MRouterStoreController.ERROR_LOADING, aPath, aError);
		this._removeLoadingPath(aPath);
	}
	
	_createLoader(aPath) {
		
		var currentState = this._store.getState();
		var apiBaseUrl = currentState.settings.wpApiUrlBase;
		var dataUrl = apiBaseUrl + aPath;
		
		var loadPromise = this._load(dataUrl).then(this._encodeLoadedDataBound)
		.then( (data) => {
			this._dataLoaded(aPath, data.data);
			//dispatch({ type: MRouterStoreController.RECEIVE_API_DATA, data: data.data, timeStamp: Date.now(), path: aPath });
		})
		.catch( (error) => {
			console.error("Loading or setting data crashed.");
			console.log(error);
			this._loadingError(aPath, error);
			//dispatch({ type: MRouterStoreController.RECEIVE_API_DATA, error: error, timeStamp: Date.now(), path: aPath });
		});
		
		return loadPromise;
	}
	
	_performRequest(aPath) {
		//console.log("oa/mrouter/store/MRouterStoreController::_performRequest");
		
		this._performDispatch(MRouterStoreController.ENSURE_LOAD_DATA_EXISTS, aPath, null);
		
		if(this._paths.indexOf(aPath) === -1) {
			this._paths.push(aPath);
		}
		
		var currentState = this._store.getState();
		var currentLoadData = currentState.mRouter.apiData[aPath];
		
		if(currentLoadData.status === 0) {
			this._performDispatch(MRouterStoreController.START_LOADING, aPath, null);
			
			this._loadingPaths.push(aPath);
			
			this._createLoader(aPath);
		}
	}
	
	requestApiData(aPath) {
		//console.log("oa/mrouter/store/MRouterStoreController::requestApiData");
		
		this._performRequest(aPath);
		
	}
	
	getPostByIdApiPath(aId) {
		return "m-router-data/v1/post/" + aId;
	}
	
	requestPostById(aId) {
		this._performRequest(this.getPostByIdApiPath(aId));
	}
	
	getPostRangeApiPath(aPath) {
		return "m-router-data/v1/" + aPath;
	}
	
	requestPostRange(aPath) {
		this._performRequest(this.getPostRangeApiPath(aPath));
	}
	
	reduce(state, action) {
		//console.log("oa/mrouter/store/MRouterStoreController::reduce");
		
		var newState = new Object();
		for(var objectName in state) {
			newState[objectName] = state[objectName];
		}
	
		if(!newState["currentPage"]) newState["currentPage"] = null;
		if(!newState["data"]) newState["data"] = new Object();
		if(!newState["idLinks"]) newState["idLinks"] = new Object();
		if(!newState["postData"]) newState["postData"] = new Object();
		if(!newState["postRanges"]) newState["postRanges"] = new Object();
		if(!newState["customizerData"]) newState["customizerData"] = new Object();
		if(!newState["apiData"]) newState["apiData"] = new Object();

		switch (action.type) {
			case MRouterStoreController.SET_CURRENT_PAGE:
				newState["currentPage"] = action.url;
				return newState;
			case MRouterStoreController.REQUEST_URL:
				if(!newState.data[action.url]) {
					newState.data[action.url] = {"status": 2};
				}
				return newState;

			case MRouterStoreController.RECEIVE_URL:
				if(action.data) {
					newState.data[action.url] = {status: 1, data: action.data};
					//METODO: add id link
				}
				else {
					newState.data[action.url] = {status: -1};
				}
				return newState;
			case MRouterStoreController.ENSURE_LOAD_DATA_EXISTS:
				if(!newState.apiData[action.path]) {
					newState.apiData[action.path] = {"status": 0};
				}
				return newState;
			case MRouterStoreController.START_LOADING:
				if(newState.apiData[action.path] && newState.apiData[action.path]["status"] === 0) {
					newState.apiData[action.path]["status"] = 2;
				}
				return newState;
			case MRouterStoreController.LOADED:
				//console.log(action.path, newState.apiData[action.path]);
				newState.apiData[action.path]["status"] = 1;
				newState.apiData[action.path]["data"] = action.data;
				return newState;
			case MRouterStoreController.ERROR_LOADING:
				//console.log(action.path, newState.apiData[action.path]);
				newState.apiData[action.path]["status"] = -1;
				newState.apiData[action.path]["error"] = action.data;
				return newState;
				
			case MRouterStoreController.REQUEST_API_DATA:
				if(!newState.apiData[action.path]) {
					newState.apiData[action.path] = {"status": 2};
				}
				return newState;

			case MRouterStoreController.RECEIVE_API_DATA:
				if(action.data) {
					newState.apiData[action.path] = {status: 1, data: action.data};
					//METODO: add id link
				}
				else {
					newState.apiData[action.path] = {status: -1};
				}
				return newState;
			case MRouterStoreController.REQUEST_POST_BY_ID:
				if(!newState.idLinks[action.id]) {
					newState.idLinks[action.id] = {"status": 2};
				}
				return newState;

			case MRouterStoreController.RECEIVE_POST_BY_ID:
				if(action.data) {
				
					newState.idLinks[action.id] = {status: 1, url: action.data.url};
					newState.postData[action.data.url] = {status: 1, data: action.data.data};
				}
				else {
					newState.idLinks[action.id] = {status: -1};
				}
				return newState;
			case MRouterStoreController.REQUEST_CUSTOMIZER_DATA:
				if(!newState.customizerData[action.options]) {
					newState.customizerData[action.options] = {"status": 2};
				}
				return newState;

			case MRouterStoreController.RECEIVE_CUSTOMIZER_DATA:
				if(action.data) {
					newState.customizerData[action.options] = {status: 1, data: action.data};
				}
				else {
					newState.customizerData[action.options] = {status: -1};
				}
				return newState;
			case MRouterStoreController.REQUEST_POST_RANGE:
				if(!newState.postRanges[action.path]) {
					newState.postRanges[action.path] = {"status": 2};
				}
				return newState;

			case MRouterStoreController.RECEIVE_POST_RANGE:
				if(action.data) {
					newState.postRanges[action.path] = {status: 1, data: action.data};
				}
				else {
					newState.postRanges[action.path] = {status: -1};
				}
				return newState;
			default:
				return newState;
		}
	}
}

MRouterStoreController.SET_CURRENT_PAGE = "M_ROUTER_SET_CURRENT_PAGE";
MRouterStoreController.REQUEST_URL = "M_ROUTER_REQUEST_URL";
MRouterStoreController.RECEIVE_URL = "M_ROUTER_RECEIVE_URL";
MRouterStoreController.REQUEST_POST_BY_ID = "M_ROUTER_REQUEST_POST_BY_ID";
MRouterStoreController.RECEIVE_POST_BY_ID = "M_ROUTER_RECEIVE_POST_BY_ID";
MRouterStoreController.REQUEST_CUSTOMIZER_DATA = "M_ROUTER_REQUEST_CUSTOMIZER_DATA";
MRouterStoreController.RECEIVE_CUSTOMIZER_DATA = "M_ROUTER_RECEIVE_CUSTOMIZER_DATA";
MRouterStoreController.REQUEST_POST_RANGE = "M_ROUTER_REQUEST_POST_RANGE";
MRouterStoreController.RECEIVE_POST_RANGE = "M_ROUTER_RECEIVE_POST_RANGE";
MRouterStoreController.REQUEST_API_DATA = "M_ROUTER_REQUEST_API_DATA";
MRouterStoreController.RECEIVE_API_DATA = "M_ROUTER_RECEIVE_API_DATA";

MRouterStoreController.ENSURE_LOAD_DATA_EXISTS = "M_ROUTER_ENSURE_LOAD_DATA_EXISTS";
MRouterStoreController.START_LOADING = "M_ROUTER_START_LOADING";
MRouterStoreController.LOADED = "M_ROUTER_LOADED";
MRouterStoreController.ERROR_LOADING = "M_ROUTER_ERROR_LOADING";