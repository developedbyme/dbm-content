import {
	SET_CURRENT_PAGE,
	REQUEST_URL,
	RECEIVE_URL,
	REQUEST_POST_BY_ID,
	RECEIVE_POST_BY_ID,
	REQUEST_CUSTOMIZER_DATA,
	RECEIVE_CUSTOMIZER_DATA,
	REQUEST_POST_RANGE,
	RECEIVE_POST_RANGE,
	REQUEST_API_DATA,
	RECEIVE_API_DATA
} from '../actioncreators/mRouterActionCreators';

//import mRouterReducer from "./oa/mrouter/store/reducers/mRouterReducer";
export default function mRouterReducer(state, action) {
	//console.log("oa/mrouter/store/reducers/mRouterReducer.mRouterReducer");
	//console.log(state, action);

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
		case SET_CURRENT_PAGE:
			newState["currentPage"] = action.url;
			return newState;
		case REQUEST_URL:
			if(!newState.data[action.url]) {
				newState.data[action.url] = {"status": 2};
			}
			return newState;

		case RECEIVE_URL:
			if(action.data) {
				newState.data[action.url] = {status: 1, data: action.data};
				//METODO: add id link
			}
			else {
				newState.data[action.url] = {status: -1};
			}
			return newState;
		case REQUEST_API_DATA:
			if(!newState.apiData[action.path]) {
				newState.apiData[action.path] = {"status": 2};
			}
			return newState;

		case RECEIVE_API_DATA:
			if(action.data) {
				newState.apiData[action.path] = {status: 1, data: action.data};
				//METODO: add id link
			}
			else {
				newState.apiData[action.path] = {status: -1};
			}
			return newState;
		case REQUEST_POST_BY_ID:
			if(!newState.idLinks[action.id]) {
				newState.idLinks[action.id] = {"status": 2};
			}
			return newState;

		case RECEIVE_POST_BY_ID:
			if(action.data) {
				
				newState.idLinks[action.id] = {status: 1, url: action.data.url};
				newState.postData[action.data.url] = {status: 1, data: action.data.data};
			}
			else {
				newState.idLinks[action.id] = {status: -1};
			}
			return newState;
		case REQUEST_CUSTOMIZER_DATA:
			if(!newState.customizerData[action.options]) {
				newState.customizerData[action.options] = {"status": 2};
			}
			return newState;

		case RECEIVE_CUSTOMIZER_DATA:
			if(action.data) {
				newState.customizerData[action.options] = {status: 1, data: action.data};
			}
			else {
				newState.customizerData[action.options] = {status: -1};
			}
			return newState;
		case REQUEST_POST_RANGE:
			if(!newState.postRanges[action.path]) {
				newState.postRanges[action.path] = {"status": 2};
			}
			return newState;

		case RECEIVE_POST_RANGE:
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
};
