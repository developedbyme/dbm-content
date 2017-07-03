import OaSiteDataMenuItem from "oa/sitedata/components/OaSiteDataMenuItem";

//import createOaSiteDataContentCreators from "oa/sitedata/setup/createOaSiteDataContentCreators";
export default function createOaSiteDataContentCreators(aReferenceHolder) {
	//console.log("oa/sitedata/setup/createOaSiteDataContentCreators::createOaSiteDataContentCreators");
	
	aReferenceHolder.addObject("contentCreatorClasses/oaSiteData/menuItem", OaSiteDataMenuItem);
}