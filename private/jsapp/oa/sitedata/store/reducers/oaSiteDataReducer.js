import {
	REQUEST_OA_SITE_DATA,
	RECEIVE_OA_SITE_DATA,
	REQUEST_MENU,
	RECEIVE_MENU
} from '../actioncreators/oaSiteDataActionCreators';

//import oaSiteDataReducer from "oa/sitedata/store/reducers/oaSiteDataReducer";
export default function oaSiteDataReducer(state, action) {
	//console.log("oa/sitedata/store/reducers/oaSiteDataReducer.oaSiteDataReducer");
	//console.log(state, action);
	
    var newState = new Object();
    for(var objectName in state) {
  	  newState[objectName] = state[objectName];
    }
	
	if(!newState["menuPositions"]) newState["menuPositions"] = new Object();
	if(!newState["menus"]) newState["menus"] = new Object();
	if(!newState["data"]) newState["data"] = new Object();

	switch (action.type) {
		case REQUEST_OA_SITE_DATA:
			newState.data[action.path] = {"status": 0, "data": null};
			return newState;
		case RECEIVE_OA_SITE_DATA:
			if(action.data) {
				newState.data[action.path] = {"status": 1, "data": action.data};
			}
			else {
				newState.data[action.path] = {"status": -1};
			}
			return newState;
		
		case REQUEST_MENU:
			newState.menus[action.position] = {"status": 0, "data": null};
			return newState;
			
		case RECEIVE_MENU:
			if(action.data) {
				newState.menus[action.position] = {"status": 1, "data": action.data};
			}
			else {
				newState.menus[action.position] = {"status": -1};
			}
			return newState;
		default:
			return newState;
	}
};