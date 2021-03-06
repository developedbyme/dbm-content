import React from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

import ReferencesHolder from "oa/reference/ReferencesHolder";

//import SortableTable from "oa/react/table/SortableTable";
export default class SortableTable extends OaBaseComponent {

	constructor (props) {
		super(props);
		
		this.state["selectedIndex"] = 0;
		this.state["sortOrder"] = 1;
		
		this._shouldSort = true;
		
		this._mainElementType = "table";
		
		this._references = new ReferencesHolder();
		
		this._references.addObject("sort/compare/string", this._caseInsensitiveCompareFunction);
		this._references.addObject("sort/compare/number", this._defaultCompareFunction);
		//this._references.addObject("sort/compare/date", null);
		
		this._headItemClickedBound = this._headItemClicked.bind(this);
	}
	
	getTableAsRowArrays() {
		var returnArray = new Array();
		
		var headerRowData = new Array();
		
		var currentArray2 = this.props.headerData;
		var currentArray2Length = currentArray2.length;
		for(var j = 0; j < currentArray2Length; j++) {
			var currentHeader = currentArray2[j];
		
			headerRowData.push(currentHeader["label"]);
		
		}
		
		returnArray.push(headerRowData);
		
		var currentArray = this._selectRows();
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			
			var currentRow = currentArray[i];
			var currentRowData = new Array();
			
			for(var j = 0; j < currentArray2Length; j++) {
			
				var currentHeader = currentArray2[j];
			
				currentRowData.push(this._getRowItemData(j, currentHeader["key"], currentRow));
			
			}
			
			returnArray.push(currentRowData);
		}
		
		return returnArray;
	}
	
	_headItemClicked(aIndex) {
		console.log("oa/react/table/SortableTable::_headItemClicked");
		
		var newState = new Object();
		
		if(this.state["selectedIndex"] === aIndex) {
			this.state["sortOrder"] *= -1;
		}
		else {
			this.state["selectedIndex"] = aIndex;
			this.state["sortOrder"] = 1;
		}
		
		this.setState(newState);
	}
	
	componentWillMount() {
		//console.log("oa/react/table/SortableTable::componentWillMount");
		
	}

	componentDidMount() {
		//console.log("oa/react/table/SortableTable::componentDidMount");
	}

	componentWillUnmount() {
		//console.log("oa/react/table/SortableTable::componentWillUnmount");
	}
	
	_renderHeadElement() {
		//console.log("oa/react/table/SortableTable::_renderHeadElement");
		
		if(!this.props.headerData) {
			console.warn("Table doesn't have any header data");
			return null;
		}
		
		var contentCreator = this._references.getObject("contentCreators/table/head");
		var rowContentCreator = this._references.getObject("contentCreators/table/headRow");
		var rowItemContentCreator = this._references.getObject("contentCreators/table/headRowItem");
		
		if(contentCreator !== null && rowContentCreator !== null) {
			
			var rowItems = new Array();
			
			var currentArray = this.props.headerData;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				
				var currentData = currentArray[i];
				
				var clickCallback = null;
				if(this._shouldSort && (currentData["sortFunction"] !== "none")) {
					clickCallback = this._headItemClickedBound;
				}
				
				rowItems.push(rowItemContentCreator({"index": i, "data": currentData, "selected": (this.state.selectedIndex === i), "sortOrder": this.state.sortOrder, "clickCallback": clickCallback}, "header-row-item-" + i, this._references));
			}
			
			var rowElement = rowContentCreator({"data": currentArray, "children": rowItems}, "row", this._references);
			var returnElement = contentCreator({"children": [rowElement]}, "head", this._references);
			return returnElement;
		}
		
		return null;
	}
	
	_defaultCompareFunction(aA, aB) {
		if(aA < aB) {
			return -1;
		}
		else if(aA > aB) {
			return 1;
		}
		else {
			return 0;
		}
	}
	
	_caseInsensitiveCompareFunction(aA, aB) {
		
		aA = (aA+"").toLowerCase();
		aB = (aB+"").toLowerCase();
		
		if(aA < aB) {
			return -1;
		}
		else if(aA > aB) {
			return 1;
		}
		else {
			return 0;
		}
	}
	
	_defaultSortFunction(aCompareFunction, aA, aB) {
		//console.log("oa/react/table/SortableTable::_defaultSortFunction");
		//console.log(aA, aB);
		
		var currentIndex = this.state["selectedIndex"];
		var currentHeader = this.props.headerData[currentIndex];
		
		var aData = this._getRowItemData(currentIndex, currentHeader["key"], aA);
		var bData = this._getRowItemData(currentIndex, currentHeader["key"], aB);
		
		return aCompareFunction(aData, bData);
	}
	
	_sortRows(aReturnArray) {
		//console.log("oa/react/table/SortableTable::_defaultSortFunction");
		
		var currentIndex = this.state["selectedIndex"];
		var currentHeader = this.props.headerData[currentIndex];
		
		var compareFunction = null;
		
		if(currentHeader.sortFunction) {
			compareFunction = this._references.getObject("sort/compare/" + currentHeader.sortFunction);
		}
		if(!compareFunction) {
			compareFunction = this._defaultCompareFunction;
		}
		
		aReturnArray.sort(this._defaultSortFunction.bind(this, compareFunction));
		
		if(this.state["sortOrder"] === -1) {
			aReturnArray.reverse();
		}
	}
	
	_selectRows() {
		
		var returnArray = new Array()
		returnArray = returnArray.concat(this.props.rows);
		
		if(this._shouldSort) {
			this._sortRows(returnArray);
		}
		
		return returnArray;
	}
	
	_getRowItemData(aIndex, aKey, aRowData) {
		return aRowData[aKey];
	}
	
	_getFormattedRowItemData(aIndex, aKey, aRowData) {
		//console.log("oa/react/table/SortableTable::_getFormattedRowItemData");
		return this._getRowItemData(aIndex, aKey, aRowData);
	}
	
	_getRowItemsData(aRowData) {
		//console.log("oa/react/table/SortableTable::_getRowItemsData");
		
		var returnArray = new Array();
		
		var currentArray = this.props.headerData;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			
			var currentHeader = currentArray[i];
			
			returnArray.push(this._getFormattedRowItemData(i, currentHeader["key"], aRowData));
			
		}
		
		return returnArray;
	}
	
	_renderBodyElement() {
		//console.log("oa/react/table/SortableTable::_renderBodyElement");
		
		if(!this.props.rows) {
			return null;
		}
		
		var contentCreator = this._references.getObject("contentCreators/table/body");
		var rowContentCreator = this._references.getObject("contentCreators/table/bodyRow");
		var rowItemContentCreator = this._references.getObject("contentCreators/table/bodyRowItem");
		
		if(contentCreator !== null && rowContentCreator !== null) {
			
			var rows = new Array();
			
			var currentArray = this._selectRows();
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				
				var currentRow = currentArray[i];
				
				var rowItems = new Array();
				
				var currentArray2 = this._getRowItemsData(currentRow);
				var currentArray2Length = currentArray2.length;
				for(var j = 0; j < currentArray2Length; j++) {
					var currentData = currentArray2[j];
					rowItems.push(rowItemContentCreator({"index": j, "rowIndex": i, "data": currentData, "headerData": this.props.headerData[j]}, "body-row-item-" + j, this._references));
				}
				
				var currentRow = rowContentCreator({"data": currentRow, "children": rowItems}, "row-" + i, this._references);
				rows.push(currentRow);
			}
			
			var returnElement = contentCreator({"children": rows}, "body", this._references);
			return returnElement;
		}
		
		return null;
	}
	
	_renderContentElement() {
		//console.log("oa/react/table/SortableTable::_renderContentElement");
		
		var returnArray = new Array();
		
		returnArray.push(this._renderHeadElement());
		returnArray.push(this._renderBodyElement());
		
		return returnArray;
	}
	
	_prepareRender() {
		this._references.setParent(this.getReferences());
	}
}
