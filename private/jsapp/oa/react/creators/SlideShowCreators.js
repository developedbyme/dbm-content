import React from 'react';

import EqualRowsGrid from "oa/react/grid/EqualRowsGrid";
import Loop from "oa/react/create/Loop";
import InjectChildren from "oa/react/InjectChildren";
import ReferenceInjection from "oa/react/ReferenceInjection";
import PreviewLoader from "oa/react/PreviewLoader";
import ContentCreatorSingleItem from "oa/react/ContentCreatorSingleItem";
import SourceData from "oa/reference/SourceData";
import ClientWidth from "oa/react/ClientWidth";

import SliderControl from "oa/react/slider/SliderControl";
import SliderDisplay from "oa/react/slider/SliderDisplay";

//import SlideShowCreators from "oa/react/creators/SlideShowCreators";
export default class SlideShowCreators {
	
	static _contentCreator_slideshowItem(aData, aKeyIndex, aReferences, aReturnArray) {
		
		var items = aReferences.getObject("internal/slideShowItems");
		var numberOfItems = items.length;
		
		var index = aData.index;
		var times = Math.floor(index/numberOfItems);
		index = index-(times*numberOfItems);
		
		var itemData = items[index];
		
		aReturnArray.push(<ContentCreatorSingleItem key={"item-" + aKeyIndex} data={itemData} contentCreator={SourceData.create("reference", "contentCreator/internal/slideShowItem")} />);
	}
	
	static simpleSlideShow(aDataArray, aItemWidth, aSpacing, aClassNames, aContentCreator) {
		return <ReferenceInjection injectData={{"contentCreator/internal/slideShowItem": aContentCreator, "internal/slideShowItems": aDataArray}} v="2">
			<SliderControl numberOfItems={aDataArray.length}>
				<ClientWidth>
					<SliderDisplay contentCreator={SlideShowCreators._contentCreator_slideshowItem} itemWidth={aItemWidth} spacing={aSpacing} className={aClassNames} />
				</ClientWidth>
			</SliderControl>
		</ReferenceInjection>;
	}
}