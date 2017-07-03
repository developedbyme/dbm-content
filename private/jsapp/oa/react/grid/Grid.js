import React from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

import FlexRow from "oa/react/grid/FlexRow";

//import Grid from "oa/react/grid/Grid";
export default class Grid extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this._mainElementType = "div";
		
		this._addMainElementClassName("grid");
		
		this._rowClassNames = new Array();
		
		this._rowContentCreatorPath = Grid.DEFAULT_ROW_CONTENT_CREATOR_PATH;
		this._rowSpacingContentCreatorPath = Grid.DEFAULT_ROW_SPACING_CONTENT_CREATOR_PATH;
	}
	
	_adjustEndRows(aRowsData) {
		//MENOTE: do nothing
	}
	
	_getRowClassNames(aRowsData) {
		
		var returnArray = new Array();
		returnArray = returnArray.concat(this._rowClassNames);
		
		if(this.props.rowClassName) {
			returnArray.push(this.props.rowClassName);
		}
		
		return returnArray;
	}
	
	_updateRowSettings(aRowData, aRowIndex, aNumberOfRows) {
		aRowData.className = this._getRowClassNames(aRowData).join(" ");
		aRowData.itemClasses = this.props.itemClasses;
		aRowData.rowIndex = aRowIndex;
		aRowData.numberOfRows = aNumberOfRows;
	}
	
	_updateRowsSettings(aRowsData) {
		var currentArray = aRowsData;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			this._updateRowSettings(currentArray[i], i, currentArrayLength);
		}
	}
	
	_createRowData(aChildren) {
		return {"children": aChildren};
	}
	
	_startSplitUpChildren() {
		//MENOTE: do nothing
	}
	
	_updateSplit(aRowItems, aReturnArray) {
		return aRowItems;
	}
	
	_endSplit(aRowItems, aReturnArray) {
		//console.log("oa/react/grid/Grid::_endSplit");
		
		aReturnArray.push(this._createRowData(aRowItems));
	}
	
	_getChildren() {
		//console.log("oa/react/grid/Grid::_getChildren");
		
		var children = this.props.dynamicChildren ? this.props.dynamicChildren : this.props.children;
		
		if(children) {
			var returnArray = Array.isArray(children) ? children : [children];
			return returnArray;
		}
		
		return null;
	}
	
	_splitUpChildren() {
		//console.log("oa/react/grid/Grid::_splitUpChildren");
		
		var returnArray = new Array();
		
		var currentRowItems = new Array();
		this._startSplitUpChildren();
		
		var currentArray = this._getChildren();
		if(currentArray) {
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentChild = currentArray[i];
				currentRowItems.push(currentChild);
				currentRowItems = this._updateSplit(currentRowItems, returnArray);
			}
		}
		if(currentRowItems.length > 0) {
			this._endSplit(currentRowItems, returnArray);
		}
		
		this._adjustEndRows(returnArray);
		this._updateRowsSettings(returnArray);
		
		return returnArray;
	}
	
	_getRows() {
		//console.log("oa/react/grid/Grid::_getRows");
		
		var references = this.getReferences();
		var rowContentCreator = references.getObject(this.getPropWithDefault("rowContentCreatorPath", this._rowContentCreatorPath));
		var rowSpacingContentCreator = references.getObject(this.getPropWithDefault("rowSpacingContentCreatorPath", this._rowSpacingContentCreatorPath));
		
		var returnArray = new Array();
		
		var currentArray = this._splitUpChildren();
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			returnArray.push(rowContentCreator(currentArray[i], i, references));
			if(i < currentArrayLength-1 && rowSpacingContentCreator) {
				returnArray.push(rowSpacingContentCreator(null, i, references));
			}
		}
		
		return returnArray;
	}

	_renderMainElement() {
		//console.log("oa/react/grid/Grid::_renderMainElement");
		
		return <wrapper>
			{this._getRows()}
		</wrapper>;
	}
	
	static setupDefaultContentCreators(aReferences) {
		aReferences.addObject(Grid.DEFAULT_ROW_CONTENT_CREATOR_PATH, Grid._contentCreator_row);
		aReferences.addObject(Grid.DEFAULT_ROW_SPACING_CONTENT_CREATOR_PATH, Grid._contentCreator_rowSpacing);
	}

	static _contentCreator_row(aData, aKeyIndex, aReferences) {
		return <FlexRow key={"row-" + aKeyIndex} className={aData.className} itemClasses={aData.itemClasses} >
			{aData.children}
		</FlexRow>;
	}

	static _contentCreator_rowSpacing(aData, aKeyIndex, aReferences) {
		return <div key={"spacing-" + aKeyIndex} className="spacing standard" />;
	}
}

Grid.DEFAULT_ROW_CONTENT_CREATOR_PATH = "contentCreators/grid/row";
Grid.DEFAULT_ROW_SPACING_CONTENT_CREATOR_PATH = "contentCreators/grid/rowSpacing";