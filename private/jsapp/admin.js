console.log("admin-main.js");

import ReactModuleCreator from "oa/ReactModuleCreator";
import GenericReactClassModuleCreator from "oa/GenericReactClassModuleCreator";
import EditorManager from "oa/admin/editor/EditorManager";
import ShortcodeManager from "oa/admin/editor/shortcodes/ShortcodeManager";
import WpAdminManager from "dbmcontent/WpAdminManager";

import DbmContentController from "dbmcontent/admin/DbmContentController";

if(!window.OA) {
	window.OA = new Object();
}

if(!window.OA.externallyAvailableClasses) {
	window.OA.externallyAvailableClasses = new Object();
}

if(!window.OA.mceEditorMananger) {
	window.OA.mceEditorMananger = new EditorManager();
}

if(!window.OA.mceShortcodeMananger) {
	window.OA.mceShortcodeMananger = new ShortcodeManager();
}

if(!window.OA.reactModuleCreator) {
	window.OA.reactModuleCreator = new ReactModuleCreator();
}

if(!window.OA.wpAdminManager) {
	window.OA.wpAdminManager = new WpAdminManager();
}

window.OA.reactModuleCreator.registerModule("dbmContentController", (new GenericReactClassModuleCreator()).setClass(DbmContentController));

document.addEventListener("DOMContentLoaded", function(event) {
	//console.log("admin-main.js DOMContentLoaded");
	if(oaWpAdminData.screen["base"] === "post") {
		window.OA.mceShortcodeMananger.registerViews();
		
		window.OA.wpAdminManager.setInitialPostData(oaWpAdminData.postData);
		window.OA.wpAdminManager.startPostInteractions();
	}
	
});