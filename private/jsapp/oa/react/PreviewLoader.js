import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import OaReduxDataLoader from "oa/react/OaReduxDataLoader";
import PostDataInjection from "oa/react/PostDataInjection";

//import PreviewLoader from "oa/react/PreviewLoader";
export default class PreviewLoader extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_removeUsedProps(aReturnObject) {
		//console.log("oa/react/PreviewLoader::_removeUsedProps");
		
		delete aReturnObject["input"];
		delete aReturnObject["preview"];
		
		return aReturnObject;
	}
	
	_getChildToClone(aReturnObject) {
		//console.log("oa/react/PreviewLoader::_getChildToClone");
		
		var inputName = this.props.input ? this.props.input : "preview";
		var preview = this.props[inputName];
		
		if(!preview) {
			console.error("Preview is not set.", this);
			return null;
		}
		
		var id = preview.id;
		
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
