import React, {Component} from 'react';
import { connect } from 'react-redux';

import PropTypes from 'prop-types';

import SourceData from "oa/reference/SourceData";

//import OaBaseComponent from "oa/react/OaBaseComponent";
//import {OaBaseComponentReduxConnected} from "oa/react/OaBaseComponent";
export default class OaBaseComponent extends Component {

	constructor (props) {
		super(props);
		this.state = {
		
		};
		
		this._mainElementType = "div";
		this._classNames = new Array();
		
		this._propsToCheckForUpdate = null;
		this._statesToCheckForUpdate = null;
	}
	
	getReferences() {
		return this.context.references;
	}
	
	getReference(aPath) {
		return this.context.references.getObject(aPath);
	}
	
	getPropWithDefault(aPropName, aDefaultValue) {
		if(this.props[aPropName] !== undefined) {
			return this.props[aPropName];
		}
		return aDefaultValue;
	}
	
	resolveSourcedData(aData) {
		if(aData instanceof SourceData) {
			return aData.getSource(this);
		}
		
		return aData;
	}
	
	resolveSourcedDataInStateChange(aData, aNewPropsAndState) {
		if(aData instanceof SourceData) {
			return aData.getSourceInStateChange(this, aNewPropsAndState);
		}
		
		return aData;
	}
	
	getSourcedProp(aPropName) {
		return this.resolveSourcedData(this.props[aPropName]);
	}
	
	getSourcedPropInStateChange(aPropName, aNewPropsAndState) {
		return this.resolveSourcedDataInStateChange(this.props[aPropName], aNewPropsAndState);
	}
	
	_createPropsToCheckIfNeeded() {
		if(this._propsToCheckForUpdate === null) {
			this._propsToCheckForUpdate = new Array();
		}
		
		return this._propsToCheckForUpdate;
	}
	
	_addPropToCheck(aName) {
		this._createPropsToCheckIfNeeded().push(aName);
		
		return this;
	}
	
	_createStatesToCheckIfNeeded() {
		if(this._statesToCheckForUpdate === null) {
			this._statesToCheckForUpdate = new Array();
		}
		
		return this._statesToCheckForUpdate;
	}
	
	_addStateToCheck(aName) {
		this._createStatesToCheckIfNeeded().push(aName);
		
		return this;
	}
	
	_addMainElementClassName(aName) {
		this._classNames.push(aName);
	}
	
	_getMainElementType() {
		if(this.props.overrideMainElementType) {
			return this.props.overrideMainElementType;
		}
		return this._mainElementType;
	}
	
	_getMainElementClassNames() {
		var returnArray = new Array();
		
		if(this.props.className) {
			returnArray.push(this.props.className);
		}
		
		var currentArray = this._classNames;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			returnArray.push(currentArray[i]);
		}
		
		return returnArray;
	}
	
	_copyPassthroughProps(aReturnObject) {
		for(var objectName in this.props) {
			//MENOTE: copy all data attributes
			if(objectName.indexOf("data-") === 0) {
				aReturnObject[objectName] = this.props[objectName];
			}
			
			//MENOTE: pass through id and name
			if(objectName === "name" || objectName === "id") {
				aReturnObject[objectName] = this.props[objectName];
			}
			
			//MENOTE: pass through style
			if(objectName === "style") {
				aReturnObject[objectName] = this.props[objectName];
			}
		}
	}
	
	_getMainElementProps() {
		var returnObject = new Object();
		
		var classNames = this._getMainElementClassNames();
		
		if(classNames.length > 0) {
			returnObject["className"] = classNames.join(" ");
		}
		
		this._copyPassthroughProps(returnObject);
		
		return returnObject;
	}
	
	shouldComponentUpdate(aNextProps, aNextStates) {
		//console.log("oa/react/OaBaseComponent::shouldComponentUpdate");
		
		var returnValue = true;
		
		if(this._propsToCheckForUpdate !== null || this._statesToCheckForUpdate !== null) {
			returnValue = false;
			
			if(this._propsToCheckForUpdate !== null && !returnValue && aNextProps) {
				var currentArray = this._propsToCheckForUpdate;
				var currentArrayLength = currentArray.length;
				for(var i = 0; i < currentArrayLength; i++) {
					var currentPropName = currentArray[i];
				
					if(this.props[currentPropName] != aNextProps[currentPropName]) {
						returnValue = true;
						break;
					}
				}
			}
		
		
			if(this._statesToCheckForUpdate !== null && !returnValue && aNextStates) {
				var currentArray = this._statesToCheckForUpdate;
				var currentArrayLength = currentArray.length;
				for(var i = 0; i < currentArrayLength; i++) {
					var currentStateName = currentArray[i];
					
					if(this.state[currentStateName] != aNextStates[currentStateName]) {
						returnValue = true;
						break;
					}
				}
			}
		}
		
		return returnValue;
	}
	
	componentWillMount() {
		//console.log("oa/react/OaBaseComponent.componentWillMount");
		
	}

	componentDidMount() {
		//console.log("oa/react/OaBaseComponent.componentDidMount");
	}

	componentWillUnmount() {
		//console.log("oa/react/OaBaseComponent.componentWillUnmount");
	}
	
	_renderContentElement() {
		
		console.warn("oa/react/OaBaseComponent::_renderContentElement should have been overridden.");
		
		return null;
	}
	
	_prepareRender() {
		
	}
	
	_renderMainElement() {
		console.warn("oa/react/OaBaseComponent::_renderMainElement should be changed to use wrapper.");
		console.log(this);
		
		return React.createElement(this._getMainElementType(), this._getMainElementProps(),
			this._renderContentElement()
		);
	}
	
	_renderSafe() {
		
		this._prepareRender();
		
		var mainElement = this._renderMainElement();
		
		if(mainElement && mainElement.type === "wrapper") {
			var renderArguments = [this._getMainElementType(), this._getMainElementProps()];
			
			if(mainElement.props.children) {
				if(Array.isArray(mainElement.props.children)) {
					renderArguments = renderArguments.concat(mainElement.props.children);
				}
				else {
					renderArguments.push(mainElement.props.children);
				}
			}
			
			mainElement = React.createElement.apply(React, renderArguments);
		}
		
		return mainElement;
		
	}
	
	render() {
		var returnObject;
		if(OaBaseComponent.CATCH_RENDER_ERRORS) {
			try {
				returnObject = this._renderSafe();
			}
			catch(aError) {
				var errorProperties = new Array();
				if(aError.fileName) {
					errorProperties.push("fileName: " + aError.fileName);
				}
				if(aError.sourceURL) {
					errorProperties.push("sourceURL: " + aError.sourceURL);
				}
				if(aError.lineNumber) {
					errorProperties.push("lineNumber: " + aError.lineNumber);
				}
				if(aError.line) {
					errorProperties.push("lineNumber: " + aError.line);
				}
				if(aError.stack) {
					errorProperties.push("stack: " + aError.stack);
				}
		
				var errorString = aError.message +" (" + errorProperties.join(", ") + ")";
		
				console.error(this, aError, errorString);
				
				returnObject = <div className="react-error">
					<div>Component had an error while rendering</div>
					<div className="description">{errorString}</div>
				</div>;
			}
		}
		else {
			returnObject = this._renderSafe();
		}
		
		return returnObject;
	}
	
	static mapStateToProps(state, myProps) {
		//console.log("oa/react/OaBaseComponent.mapStateToProps (static)");
		//console.log(state, myProps);
		
		return {
			
		};
	};
}

OaBaseComponent.CATCH_RENDER_ERRORS = true;

OaBaseComponent.contextTypes = {
	"references": PropTypes.object
};

export let OaBaseComponentReduxConnected = connect(OaBaseComponent.mapStateToProps)(OaBaseComponent);
