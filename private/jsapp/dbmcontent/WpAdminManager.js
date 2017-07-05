import objectPath from "object-path";

// import WpAdminManager from "dbmcontent/WpAdminManager";
export default class WpAdminManager {
	
	constructor() {
		console.log("dbmcontent/WpAdminManager::constructor");
		
		this._subscribeFunctions = new Array();
		
		this._pageTemplate = "default";
		this._selectedTerms = null;
		
		this._dataObject = {};
		
		this._callback_pageTemplateChangedBound = this._callback_pageTemplateChanged.bind(this);
		this._callback_termChangedBound = this._callback_termChanged.bind(this);
	}
	
	_getState() {
		var dataObject = {
			"dataObject": this._dataObject,
			"postData": {
				"terms": this._selectedTerms
			}
		}
		
		return dataObject;
	}
	
	subscribe(aSubscribeFunction) {
		console.log("dbmcontent/WpAdminManager::subscribe");
		
		this._subscribeFunctions.push(aSubscribeFunction);
		aSubscribeFunction(this._getState());
	}
	
	_broadcastChanges() {
		console.log("dbmcontent/WpAdminManager::_broadcastChanges");
		
		var dataObject = this._getState();
		
		var currentArray = this._subscribeFunctions;
		var currentArrayLength = currentArray.length;
		for(var i = 0; i < currentArrayLength; i++) {
			var currentFunction = currentArray[i];
			
			currentFunction(dataObject);
		}
	}
	
	setDataObject(aDataObject) {
		console.log("dbmcontent/WpAdminManager::setDataObject");
		
		this._dataObject = aDataObject;
		this._broadcastChanges();
	}
	
	setData(aPath, aData) {
		objectPath.set(this._dataObject, aPath, aData);
		this._broadcastChanges();
	}
	
	setInitialPostData(aPostData) {
		console.log("dbmcontent/WpAdminManager::setInitialPostData");
		
		//METODO
		this._selectedTerms = aPostData.terms;
		if(aPostData.meta["_wp_page_template"]) {
			this._pageTemplate = aPostData.meta["_wp_page_template"];
		}
		
		this._broadcastChanges();
	}
	
	startPostInteractions() {
		jQuery('#page_template').on('change', this._callback_pageTemplateChangedBound);
		
		jQuery('.categorychecklist input').on('change', this._callback_termChangedBound);
		
		/*
			'change #parent_id':									'_change_parent',
			'change #post-formats-select input':					'_change_format',
		*/
	}
	
	_callback_pageTemplateChanged(aEvent) {
		console.log("dbmcontent/WpAdminManager::_callback_pageTemplateChanged");
		
		var value = aEvent.currentTarget.options[aEvent.currentTarget.selectedIndex].value;
		
		//METODO
		console.log(value);
		
		this._pageTemplate = value;
	}
	
	_callback_formatChanged(aEvent) {
		console.log("dbmcontent/WpAdminManager::_callback_formatChanged");
	}
	
	_callback_parentChanged(aEvent) {
		console.log("dbmcontent/WpAdminManager::_callback_parentChanged");
	}
	
	_callback_termChanged(aEvent) {
		console.log("dbmcontent/WpAdminManager::_callback_termChanged");
		
		var targetElement = aEvent.currentTarget;
		var name = targetElement.name;
		var regExp = new RegExp("^tax_input\\[(.*)\\]\\[\\]$");
		var taxonomy = name.replace(regExp, "$1");
		var value = targetElement.value;
		var checked = targetElement.checked;
		
		console.log(taxonomy, value, checked);
		
	}
}