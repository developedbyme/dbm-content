import objectPath from "object-path";

// import WpPostFunctions from "oa/mrouter/utils/WpPostFunctions";
export default class WpPostFunctions {
	
	static createPostDataFromTerm(aTerm) {
		var returnObject = new Object();
		
		returnObject.id = aTerm.id;
		returnObject.permalink = aTerm.permalink;
		returnObject.title = aTerm.name;
		returnObject.excerpt = aTerm.description;
		returnObject.content = "";
		returnObject.image = null;
		returnObject.meta = aTerm.meta;
		returnObject.acf = null;
		returnObject.type = "taxonomy";
		
		return returnObject;
	}
}