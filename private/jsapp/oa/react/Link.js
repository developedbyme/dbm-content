import React from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

//import Link from "oa/react/Link";
export default class Link extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._mainElementType = "a"
	}
	
	_copyPassthroughProps(aReturnObject) {
		if(this.props.href) {
			aReturnObject["href"] = this.props.href;
		}
		if(this.props.target) {
			aReturnObject["target"] = this.props.target;
		}
		if(this.props.onClick) {
			aReturnObject["onClick"] = this.props.onClick;
		}
	}
	
	_renderMainElement() {
		return <wrapper>
			{this.props.children}
		</wrapper>;
	}
}
