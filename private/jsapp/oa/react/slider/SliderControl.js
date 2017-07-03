import React from 'react';

import PropTypes from 'prop-types';

import OaDataManipulationBaseObject from "oa/react/OaDataManipulationBaseObject";

import ReferencesHolder from "oa/reference/ReferencesHolder";

var TWEEN = require('tween.js');

//import SliderControl from "oa/react/slider/SliderControl";
export default class SliderControl extends OaDataManipulationBaseObject {

	constructor (props) {
		super(props);
		
		this.state["position"] = 0;
		
		this._items = new Array();
		
		this._references = new ReferencesHolder();
		
		this._references.addObject("sliderControl", this);
		
		this._tween = null;
		this._intervalLength = 7;
		this._intervalId = -1;
		
		this._animateToNextPositionBound = this._animateToNextPosition.bind(this);
	}
	
	getReferences() {
		return this._references;
	}
	
	getChildContext() {
		//console.log("oa/react/slider/SliderControl::getReferences");
		return {"references": this._references};
	}
	
	slideToClosestIndex(aIndex) {
		this._resetInterval();
		this._cancelAnimation();
		
		var position = this.state.position;
		var numberOfItems = this.props.numberOfItems;
		var times = Math.ceil(position/numberOfItems);
		
		if(isNaN(times)) {
			console.error("Closest position can't be retreived.", this);
			return;
		}
		
		
		var nextIndex = times*numberOfItems+aIndex;
		var previousIndex = (times-1)*numberOfItems+aIndex;
		
		if(Math.abs(nextIndex-position) == Math.abs(previousIndex-position)) {
			
			var localPosition = position-(Math.floor(position/numberOfItems)*numberOfItems);
			if(aIndex < localPosition) {
				this._animateToPosition(previousIndex, 0.8);
			}
			else {
				this._animateToPosition(nextIndex, 0.8);
			}
		}
		else if(Math.abs(nextIndex-position) < Math.abs(previousIndex-position)) {
			this._animateToPosition(nextIndex, 0.8);
		}
		else {
			this._animateToPosition(previousIndex, 0.8);
		}
	}
	
	_stopInterval() {
		//console.log("oa/react/slider/SliderControl::_stopInterval");
		
		if(this._intervalId !== -1) {
			clearInterval(this._intervalId);
			this._intervalId = -1;
		}
	}
	
	_startInterval() {
		//console.log("oa/react/slider/SliderControl::_startInterval");
		
		if(this._intervalLength !== -1 && this._intervalId === -1) {
			this._intervalId = setInterval(this._animateToNextPositionBound, this._intervalLength*1000)
		}
	}
	
	_resetInterval() {
		this._stopInterval();
		this._startInterval();
	}
	
	_cancelAnimation() {
		//METODO
	}
	
	_animateToPosition(aPosition, aTime) {
		var tweenParameters = {"position": this.state.position};
		var updateFunction = (function() {
			this.setState(tweenParameters);
		}).bind(this);

		this._tween = new TWEEN.Tween(tweenParameters).to({"position": aPosition}, 1000*aTime).easing(TWEEN.Easing.Quadratic.Out).onUpdate(updateFunction).start();
	}
	
	_animateToNextPosition() {
		//console.log("oa/react/slider/SliderControl::_animateToNextPosition");
		
		var nextPosition = Math.floor(this.state.position+1);
		
		this._animateToPosition(nextPosition, 0.8);
	}
	
	componentWillUnmount() {
		//console.log("oa/react/slider/SliderControl::componentWillUnmount");
		
		this._stopInterval();
	}
	
	componentDidMount() {
		//console.log("oa/react/slider/SliderControl::componentDidMount");
		
		if(this.props.interval) {
			this._intervalLength = this.props.interval;
		}
		this._startInterval();
	}
	
	_manipulateProps(aReturnObject) {
		//console.log("oa/react/slider/SliderControl::_manipulateProps");
		
		aReturnObject["position"] = this.state["position"];
		
		return aReturnObject;
	}
	
	_prepareRender() {
		super._prepareRender();
		
		this._references.setParent(this.context.references);
	}
}

SliderControl.childContextTypes = {
	"references": PropTypes.object
};

