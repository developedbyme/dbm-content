import React from 'react';

var TWEEN = require('tween.js');

import OaBaseComponent from "oa/react/OaBaseComponent";

//import OpenCloseExpandableArea from "oa/react/interaction/OpenCloseExpandableArea";
export default class OpenCloseExpandableArea extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this.state["open"] = false;
		this.state["height"] = 0;
		this.state["envelope"] = 0;
		
		this._callback_sizeChangedBound = this._callback_sizeChanged.bind(this);
	}
	
	_updateHeight() {
		//console.log("oa/react/interaction/OpenCloseExpandableArea::_updateHeight");
		
		var currentHeight = this.refs.heightElement.clientHeight;
		
		if(currentHeight !== this.state["height"]) {
			this.setState({"height": currentHeight});
		}
	}
	
	_callback_sizeChanged(aEvent) {
		//console.log("oa/react/interaction/OpenCloseExpandableArea::_callback_sizeChanged");
		
		this._updateHeight();
	}
	
	componentWillReceiveProps(aNextProps) {
		//console.log("oa/react/interaction/OpenCloseExpandableArea::componentWillReceiveProps");
		//console.log(aNextProps);
		
		var open = this.resolveSourcedData(aNextProps["open"]);
		
		if(open !== this.state["open"]) {
			var tweenParameters = {"envelope": this.state.envelope};
			var updateFunction = (function() {
				this.setState(tweenParameters);
			}).bind(this);
			
			var newEnvelope = open ? 1 : 0;
			
			this.setState({"open": open});
			this._tween = new TWEEN.Tween(tweenParameters).to({"envelope": newEnvelope}, 1000*0.4).easing(TWEEN.Easing.Quadratic.Out).onUpdate(updateFunction).start();
		}
	}
	
	componentDidMount() {
		//console.log("oa/react/interaction/OpenCloseExpandableArea::componentDidMount");
		
		this._updateHeight();
		
		window.addEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	componentDidUpdate() {
		//console.log("oa/react/interaction/OpenCloseExpandableArea::componentDidUpdate");
		
		this._updateHeight();
	}
	
	componentWillUnmount() {
		//console.log("oa/react/interaction/OpenCloseExpandableArea::componentWillUnmount");
		
		window.removeEventListener("resize", this._callback_sizeChangedBound, false);
	}
	
	_renderMainElement() {
		
		var height = this.state["height"]*this.state["envelope"];
		var styleObject = {"height": height};
		
		return <wrapper>
			<div className="animation-element no-overflow" style={styleObject}>
				<div ref="heightElement">{this.props.children}</div>
			</div>
		</wrapper>
	}
}
