import objectPath from "object-path";

// import WpAdminManager from "dbmcontent/WpAdminManager";
export default class WpAdminManager {
	
	constructor() {
		console.log("dbmcontent/WpAdminManager::constructor");
		
		this._subscribeFunctions = new Array();
		
		this._pageTemplate = "default";
		this._selectedTerms = null;
		
		this._shortcodes = {};
		this._dataObject = {"shortcodes": this._shortcodes};
		this._taxonomies = {};
		
		
		this._callback_pageTemplateChangedBound = this._callback_pageTemplateChanged.bind(this);
		this._callback_termChangedBound = this._callback_termChanged.bind(this);
	}
	
	_getState() {
		var dataObject = {
			"dataObject": this._dataObject,
			"taxonomies": this._taxonomies,
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
		
		if(this._dataObject.shortcodes) {
			this._shortcodes = this._dataObject.shortcodes;
		}
		else {
			this._dataObject.shortcodes = this._shortcodes;
		}
		
		this._broadcastChanges();
	}
	
	setData(aPath, aData) {
		console.log("dbmcontent/WpAdminManager::setData");
		console.log(aPath, aData);
		
		console.log(JSON.stringify(this._dataObject));
		
		objectPath.set(this._dataObject, aPath, aData);
		
		console.log(JSON.stringify(this._dataObject));
		
		this._broadcastChanges();
	}
	
	setInitialPostData(aPostData, aTaxonomies) {
		console.log("dbmcontent/WpAdminManager::setInitialPostData");
		
		this._taxonomies = aTaxonomies;
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
	
	termAdded(aId, aTaxonomy) {
		console.log("dbmcontent/WpAdminManager::termAdded");
		
		if(!this._selectedTerms[aTaxonomy]) {
			this._selectedTerms[aTaxonomy] = new Array();
		}
		
		var currentArray = this._taxonomies[aTaxonomy];
		if(currentArray) {
			var isFound = false;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentTerm = currentArray[i];
				if(currentTerm.id === aId) {
					this._selectedTerms[aTaxonomy].push(currentTerm);
					isFound = true;
					break;
				}
			}
			
			if(isFound) {
				this._broadcastChanges();
			}
			else {
				console.warn("Term with id " + aId + " for taxonomy " + aTaxonomy + " doesn't exist. Can't add.");
			}
		}
	}
	
	termRemoved(aId, aTaxonomy) {
		console.log("dbmcontent/WpAdminManager::termRemoved");
		
		var currentArray = this._selectedTerms[aTaxonomy];
		if(currentArray) {
			var isFound = false;
			var currentArrayLength = currentArray.length;
			for(var i = 0; i < currentArrayLength; i++) {
				var currentTerm = currentArray[i];
				if(currentTerm.id === aId) {
					currentArray.splice(i, 1);
					isFound = true;
					break;
				}
			}
			
			if(isFound) {
				this._broadcastChanges();
			}
			else {
				console.warn("Term with id " + aId + " for taxonomy " + aTaxonomy + " is not active. Can't remove.");
			}
		}
	}
	
	addShortcode(aId, aType, aData) {
		console.log("dbmcontent/WpAdminManager::addShortcode");
		console.log(aId, aType, aData);
		
		var dataObject = {
			"type": aType,
			"data": aData
		}
		
		this._shortcodes[aId] = dataObject;
		this._broadcastChanges();
	}
	
	_callback_termChanged(aEvent) {
		console.log("dbmcontent/WpAdminManager::_callback_termChanged");
		
		var targetElement = aEvent.currentTarget;
		var name = targetElement.name;
		var regExp = new RegExp("^tax_input\\[(.*)\\]\\[\\]$");
		var taxonomy = name.replace(regExp, "$1");
		var value = parseInt(targetElement.value, 10);
		var checked = targetElement.checked;
		
		if(checked) {
			this.termAdded(value, taxonomy);
		}
		else {
			this.termRemoved(value, taxonomy);
		}
		
		//console.log(taxonomy, value, checked);
		
	}
}