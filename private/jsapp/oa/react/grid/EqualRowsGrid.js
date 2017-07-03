import React from 'react';

import Grid from "oa/react/grid/Grid";

//import EqualRowsGrid from "oa/react/grid/EqualRowsGrid";
export default class EqualRowsGrid extends Grid {

	constructor (props) {
		super(props);
	}
	
	_getRowLength() {
		if(this.props.itemsPerRow) {
			return 1*this.props.itemsPerRow;
		}
		return EqualRowsGrid.DEFAULT_ITEMS_PER_ROW;
	}
	
	_adjustEndRows(aRowsData) {
		//METODO: even out ending
	}
	
	_getRowClassNames(aRowsData) {
		
		//METODO: add  classes depending on length
		//METODO: add classes depending on full row
		
		return super._getRowClassNames(aRowsData);
	}
	
	_updateSplit(aRowItems, aReturnArray) {
		
		if(aRowItems.length == this._getRowLength()) {
			aReturnArray.push(this._createRowData(aRowItems));
			return new Array();
		}
		
		return aRowItems;
	}
}

EqualRowsGrid.DEFAULT_ITEMS_PER_ROW = 4;