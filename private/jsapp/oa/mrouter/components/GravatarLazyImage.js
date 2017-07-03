import React from 'react';

import OaLazyImage from "oa/react/OaLazyImage";

//import GravatarLazyImage from "oa/mrouter/components/GravatarLazyImage";
export default class GravatarLazyImage extends OaLazyImage {

	constructor (props) {
		super(props);
	}
	
	_getImageData() {
		
		var sizeData = {"full": {"width": 256, "height": 256, "url": "http://2.gravatar.com/avatar/" + this.props.hash + "?s=256&d=mm&r=g"}}
		
		return sizeData;
	}
}