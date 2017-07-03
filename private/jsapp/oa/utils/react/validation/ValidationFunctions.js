// import ValidationFunctions from "oa/utils/react/validation/ValidationFunctions";
export default class ValidationFunctions {

	static noValidation(aCheckValue, aAdditionalData) {
		return true;
	}
	
	static checkboxClicked(aCheckValue, aAdditionalData) {
		return aCheckValue;
	}
	
	static notEmpty(aCheckValue, aAdditionalData) {
		console.log("oa/utils/react/validation/ValidationFunctions::notEmpty");
		
		return (aCheckValue && aCheckValue.length > 0);
	}
	
	static isEmail(aCheckValue, aAdditionalData) {
		
		var re = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;
		return re.test(aCheckValue);
	}
}
