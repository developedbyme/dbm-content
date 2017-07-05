import React from 'react';

import OaBaseComponent from "oa/react/OaBaseComponent";

// import DbmContentController from "dbmcontent/admin/DbmContentController";
export default class DbmContentController extends OaBaseComponent {

	constructor(props) {
		super(props);
		
		this.state["dataObject"] = new Object();
		
		this._adminUpdateBound = this._adminUpdate.bind(this);
	}
	
	componentWillMount() {
		console.log("dbmcontent/admin/DbmContentController::componentWillMount");
		
		if(this.props.metaFields && this.props.metaFields.dbm_content) {
			
			var dataObject = JSON.parse(this.props.metaFields.dbm_content);
			
			window.OA.wpAdminManager.subscribe(this._adminUpdateBound);
			window.OA.wpAdminManager.setDataObject(dataObject);
			
		}
	}
	
	_adminUpdate(aNewData) {
		console.log("dbmcontent/admin/DbmContentController::_adminUpdate");
		
		this.setState({"dataObject": aNewData["dataObject"]});
	}
	
	_renderMainElement() {
		console.log("dbmcontent/admin/DbmContentController::_renderMainElement");
		
		return <wrapper>
			<div>Status display</div>
			<input type="text" name="dbm_content" value={JSON.stringify(this.state["dataObject"])}/>
		</wrapper>;
	}
}
