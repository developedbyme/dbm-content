import React from 'react';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

//import ContentCreatorSingleItem from "oa/react/ContentCreatorSingleItem";
export default class ContentCreatorSingleItem extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
	}
	
	_getChildToClone() {
		
		var data = this.getSourcedProp("data");
		var contentCreator = this.getSourcedProp("contentCreator");
		
		if(!contentCreator) {
			console.error("Content creator is not set.", this);
			return <div>Content creator is not set</div>;
		}
		
		var returnArray = new Array();
		contentCreator(data, 0, this.getReferences(), returnArray);
		
		if(returnArray.length === 0) {
			console.error("Content creator didn't return any node.", this);
			return <div>Content creator didnt return any object</div>;
		}
		if(returnArray.length > 1) {
			console.error("Content creator returned more than 1 item.", this);
		}
		
		return returnArray[0];
	}
}
