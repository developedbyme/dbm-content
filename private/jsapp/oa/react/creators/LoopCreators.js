import React from 'react';

import EqualRowsGrid from "oa/react/grid/EqualRowsGrid";
import Loop from "oa/react/create/Loop";
import InjectChildren from "oa/react/InjectChildren";
import ReferenceInjection from "oa/react/ReferenceInjection";
import PreviewLoader from "oa/react/PreviewLoader";
import ContentCreatorSingleItem from "oa/react/ContentCreatorSingleItem";
import SourceData from "oa/reference/SourceData";
import PostDataInjection from "oa/react/PostDataInjection";

//import LoopCreators from "oa/react/creators/LoopCreators";
export default class LoopCreators {
	
	static loop(aDataArray, aHolder, aContentCreator, aSpacingContentCreator) {
		return <Loop input={aDataArray} contentCreator={aContentCreator} spacingContentCreator={aSpacingContentCreator} v="2">
			<InjectChildren>
				{aHolder}
			</InjectChildren>
		</Loop>;
	}
	
	static _contentCreator_previewLoader(aData, aKeyIndex, aReferences, aReturnArray) {
		aReturnArray.push(<PreviewLoader preview={aData} >
			<ContentCreatorSingleItem data={SourceData.create("reference", "mRouter/postData")} contentCreator={SourceData.create("reference", "contentCreator/internal/previewLoaderContent")} />
		</PreviewLoader>);
	}
	
	static previewLoaderLoop(aDataArray, aHolder, aContentCreator, aSpacingContentCreator) {
		return <ReferenceInjection injectData={{"contentCreator/internal/previewLoaderContent": aContentCreator}} v="2">
			<Loop input={aDataArray} contentCreator={LoopCreators._contentCreator_previewLoader} spacingContentCreator={aSpacingContentCreator} v="2">
				<InjectChildren>
					{aHolder}
				</InjectChildren>
			</Loop>
		</ReferenceInjection>;
	}
	
	static loopToEqualRows(aDataArray, aNumberOfRows, aRowClassNames, aContentCreator) {
		//console.log("oa/react/create/LoopCreators::_removeUsedProps");
		
		return <Loop input={aDataArray} contentCreator={aContentCreator} v="2">
			<EqualRowsGrid itemsPerRow={aNumberOfRows} rowClassName={aRowClassNames} />
		</Loop>;
	}
	
	static _contentCreator_postInjection(aData, aKeyIndex, aReferences, aReturnArray) {
		aReturnArray.push(<PostDataInjection postData={aData} >
			<ContentCreatorSingleItem data={SourceData.create("reference", "mRouter/postData")} contentCreator={SourceData.create("reference", "contentCreator/internal/postInjection")} />
		</PostDataInjection>);
	}
	
	static postLoop(aDataArray, aHolder, aContentCreator) {
		return <ReferenceInjection injectData={{"contentCreator/internal/postInjection": aContentCreator}} v="2">
			<Loop input={aDataArray} contentCreator={LoopCreators._contentCreator_postInjection} v="2">
				{aHolder}
			</Loop>
		</ReferenceInjection>;
	}
	
	static _contentCreator_acfRowInjection(aData, aKeyIndex, aReferences, aReturnArray) {
		aReturnArray.push(<ReferenceInjection injectData={{"mRouter/postData/acfRow": aData}} v="2">
			<ContentCreatorSingleItem data={SourceData.create("reference", "mRouter/postData/acfRow")} contentCreator={SourceData.create("reference", "contentCreator/internal/acfRowInjection")} />
		</ReferenceInjection>);
	}
	
	static acfRepeaterLoop(aDataArray, aHolder, aContentCreator) {
		return <ReferenceInjection injectData={{"contentCreator/internal/acfRowInjection": aContentCreator}} v="2">
			<Loop input={aDataArray} contentCreator={LoopCreators._contentCreator_acfRowInjection} v="2">
				{aHolder}
			</Loop>
		</ReferenceInjection>;
	}
}