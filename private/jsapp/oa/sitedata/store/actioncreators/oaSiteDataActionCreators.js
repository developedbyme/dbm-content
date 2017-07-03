//import {REQUEST_MENU, RECEIVE_MENU} from "oa/sitedata/store/actioncreators/oaSiteDataActionCreators";
export const REQUEST_OA_SITE_DATA = 'REQUEST_OA_SITE_DATA';
export const RECEIVE_OA_SITE_DATA = 'RECEIVE_OA_SITE_DATA';
export const REQUEST_MENU = 'REQUEST_MENU';
export const RECEIVE_MENU = 'RECEIVE_MENU';

//import {requestOaSiteDataMenu} from "oa/sitedata/store/actioncreators/oaSiteDataActionCreators";
export function requestOaSiteDataMenu(aMenuPosition) {
	//console.log("oa/sitedata/store/actioncreators/oaSiteDataActionCreators::requestOaSiteDataMenu");
	//console.log(aMenuPosition);
	
	return (dispatch, getState) => {
		
		const state = getState();
		const menus = state.oaSiteData.menus;
		
		if(menus[aMenuPosition] && menus[aMenuPosition].data) {
			return;
		}
		
		dispatch({ type: REQUEST_MENU, timeStamp: Date.now(), "position": aMenuPosition });
		
		var dataUrl = state.settings.wpApiUrlBase + "m-router-data/v1/menu/" + aMenuPosition;
		
		//MENOTE: promise for async server rendering
		var renderPromiseFunction = function() {};
		if(global.createRenderPromiseFunction) {
			var renderPromiseFunction = global.createRenderPromiseFunction();
		}
		
		var loadPromise = Promise.race([
			fetch(dataUrl, {credentials: "include"}),
			new Promise( (resolve, reject) =>
				setTimeout(() => reject(new Error('request timeout')), 10000)
			)
		])
		.then( (response) => {
			return response.json();
		})
		.then( (data) => {
			renderPromiseFunction();
			dispatch({ type: RECEIVE_MENU, data: data.data, timeStamp: Date.now(), "position": aMenuPosition });
		})
		.catch( (error) => {
			renderPromiseFunction();
			console.log(error);
			dispatch({ type: RECEIVE_MENU, error: error, timeStamp: Date.now(), "position": aMenuPosition });
		});

		return loadPromise;
	}
}

//import {requestOaSiteData} from "oa/sitedata/store/actioncreators/oaSiteDataActionCreators";
export function requestOaSiteData(aPath) {
	//console.log("oa/sitedata/store/actioncreators/oaSiteDataActionCreators::requestOaSiteData");
	//console.log(aPath);
	
	return (dispatch, getState) => {
		
		dispatch({ type: REQUEST_OA_SITE_DATA, timeStamp: Date.now(), "path": aPath });
		
		const state = getState();
		const data = state.oaSiteData.data;
		
		if(data[aPath].data) {
			return;
		}
		
		var dataUrl = state.settings.wpApiUrlBase + aPath;
		
		//MENOTE: promise for async server rendering
		var renderPromiseFunction = function() {};
		if(global.createRenderPromiseFunction) {
			var renderPromiseFunction = global.createRenderPromiseFunction();
		}
		
		var loadPromise = Promise.race([
			fetch(dataUrl, {credentials: "include"}),
			new Promise( (resolve, reject) =>
				setTimeout(() => reject(new Error('request timeout')), 10000)
			)
		])
		.then( (response) => {
			return response.json();
		})
		.then( (data) => {
			renderPromiseFunction();
			dispatch({ type: RECEIVE_OA_SITE_DATA, data: data, timeStamp: Date.now(), "path": aPath });
		})
		.catch( (error) => {
			renderPromiseFunction();
			console.log(error);
			dispatch({ type: RECEIVE_OA_SITE_DATA, error: error, timeStamp: Date.now(), "path": aPath });
		});

		return loadPromise;
	}
}

