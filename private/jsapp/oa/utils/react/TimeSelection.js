import React from "react";

// import TimeSelection from "oa/utils/react/TimeSelection";
export default class TimeSelection extends React.Component {

	constructor( props ) {
		super( props );
		
		this.state = {
			
		};
		
		this._callback_changeHourBound = this._callback_changeHour.bind(this);
		this._callback_changeMinuteBound = this._callback_changeMinute.bind(this);
	}
		
	componentWillMount() {
		
	}
	
	_callback_changeHour(aEvent) {
		console.log("TimeSelection::_callback_changeHour");
		console.log(aEvent);
		console.log(aEvent.target.value);
		
		var newCompoundValue = null;
		var newValue = aEvent.target.value;
		if(newValue !== "") {
			
			newCompoundValue = newValue + ":";
			
			if(this.props.minute === "") {
				newCompoundValue += "00";
			}
			else {
				newCompoundValue += this.props.minute;
			}
		}
		
		console.log(newCompoundValue);
		
		this.getReferences().getObject("value/" + this.props.valueName).updateValue(this.props.valueName, newCompoundValue);
	
	}
	
	_callback_changeMinute(aEvent) {
		console.log("TimeSelection::_callback_changeMinute");
		console.log(aEvent);
		console.log(aEvent.target.value);
		
		var newCompoundValue = null;
		var newValue = aEvent.target.value;
		if(newValue !== "") {
			
			newCompoundValue = ":" + newValue;
			
			if(this.props.hour === "") {
				newCompoundValue = "00" + newCompoundValue;
			}
			else {
				newCompoundValue = this.props.hour + newCompoundValue;
			}
		}
		
		console.log(newCompoundValue);
		this.getReferences().getObject("value/" + this.props.valueName).updateValue(this.props.valueName, newCompoundValue);
	}

	render() {
		//console.log("TimeSelection::render");
		
		var hourOptions = new Array(24);
		for(var i = 0; i < 24; i++) {
			
			var value = i.toString(10);
			if(i < 10) {
				value = "0" + value;
			}
			
			hourOptions[i] = <option key={value} value={value}>{value}</option>;
		}
		
		var minuteOptions = new Array(60);
		for(var i = 0; i < 60; i++) {
			
			var value = i.toString(10);
			if(i < 10) {
				value = "0" + value;
			}
			
			minuteOptions[i] = <option key={value} value={value}>{value}</option>;
		}
		
		return (
			<span className="time-selection">
				<select onChange={this._callback_changeHourBound} value={this.props.hour}>
					<option value="">-</option>
					{hourOptions}
				</select>
				<span>:</span>
				<select onChange={this._callback_changeMinuteBound} value={this.props.minute}>
					<option value="">-</option>
					{minuteOptions}
				</select>
			</span>
		);
	}

}
