import React from 'react';

import OaLazyImage from "oa/react/OaLazyImage";

//import MRouterLazyImage from "oa/mrouter/components/MRouterLazyImage";
export default class MRouterLazyImage extends OaLazyImage {

	constructor (props) {
		super(props);
	}
	
	_getImageData() {
		
		if(!this.props.data) {
			console.warn("Image doesn't have any data.", this);
			
			return null;
		}
		
		return this.props.data.sizes;
	}
}
