import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import OaReduxDataLoader from "oa/react/OaReduxDataLoader";
import PostDataInjection from "oa/react/PostDataInjection";

//import IdLoader from "oa/react/IdLoader";
export default class IdLoader extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/IdLoader::_removeUsedProps");
		
		delete aReturnObject["id"];
		delete aReturnObject["preview"];
		
		return aReturnObject;
	}
	
	_getChildToClone(aReturnObject) {
		//console.log("oa/react/IdLoader::_getChildToClone");
		
		var inputName = this.props.input ? this.props.input : "id";
		var id = this.props[inputName];
		
		var loadData = {
			"type": "M-ROUTER-POST-BY-ID",
			"path": id
		};
		
		return <OaReduxDataLoader loadData={{"postData": loadData}}>
			<PostDataInjection>
				{this._getInputChild()}
			</PostDataInjection>
		</OaReduxDataLoader>
	}
}
