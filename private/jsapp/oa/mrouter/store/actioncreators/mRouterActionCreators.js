export const SET_CURRENT_PAGE = 'M_ROUTER_SET_CURRENT_PAGE';
export const REQUEST_URL = 'M_ROUTER_REQUEST_URL';
export const RECEIVE_URL = 'M_ROUTER_RECEIVE_URL';
export const REQUEST_POST_BY_ID = 'M_ROUTER_REQUEST_POST_BY_ID';
export const RECEIVE_POST_BY_ID = 'M_ROUTER_RECEIVE_POST_BY_ID';
export const REQUEST_POST_RANGE = 'M_ROUTER_REQUEST_POST_RANGE';
export const RECEIVE_POST_RANGE = 'M_ROUTER_RECEIVE_POST_RANGE';
export const REQUEST_API_DATA = 'M_ROUTER_REQUEST_API_DATA';
export const RECEIVE_API_DATA = 'M_ROUTER_RECEIVE_API_DATA';
export const REQUEST_CUSTOMIZER_DATA = 'M_ROUTER_REQUEST_CUSTOMIZER_DATA';
export const RECEIVE_CUSTOMIZER_DATA = 'M_ROUTER_RECEIVE_CUSTOMIZER_DATA';

//import {setCurrentPage} from "./oa/mrouter/store/actioncreators/mRouterActionCreators";
export function setCurrentPage(url) {
	//console.log("oa/mrouter/store/actioncreators/mRouterActionCreators::setCurrentPage");
	//console.log(url);
	
	return (dispatch, getState) => {
		dispatch({ type: SET_CURRENT_PAGE, timeStamp: Date.now(), url: url });
	}
}

//import {requestApiData} from "./oa/mrouter/store/actioncreators/mRouterActionCreators";
export function requestApiData(aPath) {
	//console.log("oa/mrouter/store/actioncreators/mRouterActionCreators::requestApiData");
	//console.log(aPath);

	return (dispatch, getState) => {


		const state = getState();
		const mRouterApiData = state.mRouter.apiData;
		
		if(mRouterApiData[aPath] && mRouterApiData[aPath].data) {
			return;
		}

		dispatch({ type: REQUEST_API_DATA, timeStamp: Date.now(), path: aPath });

		var apiBaseUrl = state.settings.wpApiUrlBase;

		var dataUrl = apiBaseUrl + aPath;


		//MENOTE: promise for async server rendering
		var renderPromiseFunction = function() {};
		if(global.createRenderPromiseFunction) {
			var renderPromiseFunction = global.createRenderPromiseFunction();
		}

		var loadPromise = Promise.race([
			fetch(dataUrl, {credentials: "include", "headers": {"X-WP-Nonce": window.oaWpConfiguration.nonce}}),
			new Promise( (resolve, reject) =>
				setTimeout(() => reject(new Error('request timeout')), 10000)
			)
		])
		.then( (response) => {
			return response.json();
		})
		.then( (data) => {
			renderPromiseFunction();
			dispatch({ type: RECEIVE_API_DATA, data: data.data, timeStamp: Date.now(), path: aPath });
		})
		.catch( (error) => {
			console.error("Loading or setting data crashed.");
			console.log(error);

			renderPromiseFunction();
			dispatch({ type: RECEIVE_API_DATA, error: error, timeStamp: Date.now(), path: aPath });
		});

		return loadPromise;
	}
}

//import {requestUrl} from "./oa/mrouter/store/actioncreators/mRouterActionCreators";
export function requestUrl(url) {
	//console.log("oa/mrouter/store/actioncreators/mRouterActionCreators::requestUrl");
	//console.log(url);

	var dataUrl = url;

	dataUrl += ((dataUrl.indexOf("?") === -1) ? "?" : "&");
	dataUrl += "mRouterData=json";

	//console.log(dataUrl);

	return (dispatch, getState) => {

		const state = getState();
		const mRouterUrls = state.mRouter.data;
		
		if(mRouterUrls[url] && mRouterUrls[url].data) {
			return;
		}

		dispatch({ type: REQUEST_URL, timeStamp: Date.now(), url: url });

		//MENOTE: promise for async server rendering
		var renderPromiseFunction = function() {};
		if(global.createRenderPromiseFunction) {
			var renderPromiseFunction = global.createRenderPromiseFunction();
		}

		var loadPromise = Promise.race([
			fetch(dataUrl, {credentials: "include", "headers": {"X-WP-Nonce": window.oaWpConfiguration.nonce}}),
			new Promise( (resolve, reject) =>
				setTimeout(() => reject(new Error('request timeout')), 10000)
			)
		])
		.then( (response) => {
			return response.json();
		})
		.then( (data) => {
			renderPromiseFunction();
			dispatch({ type: RECEIVE_URL, data: data.data, timeStamp: Date.now(), url: url });
		})
		.catch( (error) => {
			console.error("Loading or setting data crashed.");
			console.log(error);

			renderPromiseFunction();
			dispatch({ type: RECEIVE_URL, error: error, timeStamp: Date.now(), url: url });
		});

		return loadPromise;
	}
}


//import {requestPostById} from "./oa/mrouter/store/actioncreators/mRouterActionCreators";
export function requestPostById(aId) {
	//console.log("oa/mrouter/store/actioncreators/mRouterActionCreators::requestPostById");
	//console.log(aId);

	return (dispatch, getState) => {


		const state = getState();
		const mRouterIdLinks = state.mRouter.idLinks;
		
		if(mRouterIdLinks[aId] && mRouterIdLinks[aId].data) {
			return;
		}

		dispatch({ type: REQUEST_POST_BY_ID, timeStamp: Date.now(), id: aId });

		var apiBaseUrl = state.settings.wpApiUrlBase;

		var dataUrl = apiBaseUrl + "m-router-data/v1/post/" + aId;


		//MENOTE: promise for async server rendering
		var renderPromiseFunction = function() {};
		if(global.createRenderPromiseFunction) {
			var renderPromiseFunction = global.createRenderPromiseFunction();
		}

		var loadPromise = Promise.race([
			fetch(dataUrl, {credentials: "include", "headers": {"X-WP-Nonce": window.oaWpConfiguration.nonce}}),
			new Promise( (resolve, reject) =>
				setTimeout(() => reject(new Error('request timeout')), 10000)
			)
		])
		.then( (response) => {
			return response.json();
		})
		.then( (data) => {
			renderPromiseFunction();
			dispatch({ type: RECEIVE_POST_BY_ID, data: data.data, timeStamp: Date.now(), id: aId });
		})
		.catch( (error) => {
			console.error("Loading or setting data crashed.");
			console.log(error);

			renderPromiseFunction();
			dispatch({ type: RECEIVE_POST_BY_ID, error: error, timeStamp: Date.now(), id: aId });
		});

		return loadPromise;
	}
}


//import {requestCustomizerData} from "./oa/mrouter/store/actioncreators/mRouterActionCreators";
export function requestCustomizerData(aOptions) {
	//console.log("oa/mrouter/store/actioncreators/mRouterActionCreators::requestCustomizerData");
	//console.log(aId);

	return (dispatch, getState) => {
		const state = getState();
		const mRouterIdLinks = state.mRouter.customizerData;
		
		if(mRouterIdLinks[aOptions] && mRouterIdLinks[aOptions].data) {
			return;
		}

		dispatch({ type: REQUEST_CUSTOMIZER_DATA, timeStamp: Date.now(), options: aOptions });

		var apiBaseUrl = state.settings.wpApiUrlBase;

		var dataUrl = apiBaseUrl + "m-router-data/v1/customizer/" + aOptions;


		//MENOTE: promise for async server rendering
		var renderPromiseFunction = function() {};
		if(global.createRenderPromiseFunction) {
			var renderPromiseFunction = global.createRenderPromiseFunction();
		}

		var loadPromise = Promise.race([
			fetch(dataUrl, {credentials: "include", "headers": {"X-WP-Nonce": window.oaWpConfiguration.nonce}}),
			new Promise( (resolve, reject) =>
				setTimeout(() => reject(new Error('request timeout')), 10000)
			)
		])
		.then( (response) => {
			return response.json();
		})
		.then( (data) => {
			renderPromiseFunction();
			dispatch({ type: RECEIVE_CUSTOMIZER_DATA, data: data.data, timeStamp: Date.now(), options: aOptions });
		})
		.catch( (error) => {
			console.error("Loading or setting data crashed.");
			console.log(error);

			renderPromiseFunction();
			dispatch({ type: RECEIVE_CUSTOMIZER_DATA, error: error, timeStamp: Date.now(), options: aOptions });
		});

		return loadPromise;
	}
}

//import {requestPostRange} from "./oa/mrouter/store/actioncreators/mRouterActionCreators";
export function requestPostRange(aPath) {
	//console.log("oa/mrouter/store/actioncreators/mRouterActionCreators::requestPostRange");
	//console.log(aPath);

	return (dispatch, getState) => {
		const state = getState();
		const postRanges = state.mRouter.postRanges;
		
		if(postRanges[aPath] && postRanges[aPath].data) {
			return;
		}

		dispatch({ type: REQUEST_POST_RANGE, timeStamp: Date.now(), path: aPath });

		var apiBaseUrl = state.settings.wpApiUrlBase;

		var dataUrl = apiBaseUrl + "m-router-data/v1/" + aPath;
		
		var currentLanguage = window.oaWpConfiguration.currentLanguage;
		if(currentLanguage) {
			if(dataUrl.indexOf("?") === -1) {
				dataUrl += "?";
			}
			else {
				dataUrl += "&";
			}
			dataUrl += "language=" + currentLanguage;
		}

		//MENOTE: promise for async server rendering
		var renderPromiseFunction = function() {};
		if(global.createRenderPromiseFunction) {
			var renderPromiseFunction = global.createRenderPromiseFunction();
		}

		var loadPromise = Promise.race([
			fetch(dataUrl, {credentials: "include", "headers": {"X-WP-Nonce": window.oaWpConfiguration.nonce}}),
			new Promise( (resolve, reject) =>
				setTimeout(() => reject(new Error('request timeout')), 10000)
			)
		])
		.then( (response) => {
			return response.json();
		})
		.then( (data) => {
			renderPromiseFunction();
			dispatch({ type: RECEIVE_POST_RANGE, data: data.data, timeStamp: Date.now(), path: aPath });
		})
		.catch( (error) => {
			console.error("Loading or setting data crashed.");
			console.log(error);

			renderPromiseFunction();
			dispatch({ type: RECEIVE_POST_RANGE, error: error, timeStamp: Date.now(), path: aPath });
		});

		return loadPromise;
	}
}
