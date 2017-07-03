import PostPreview from "oa/mrouter/components/PostPreview";

//import createMRouterContentCreators from "oa/mrouter/setup/createMRouterContentCreators";
export default function createMRouterContentCreators(aReferenceHolder) {
	//console.log("oa/mrouter/setup/createMRouterContentCreators::createMRouterContentCreators");
	
	aReferenceHolder.addObject("contentCreatorClasses/mRouter/postPreview", PostPreview);
}