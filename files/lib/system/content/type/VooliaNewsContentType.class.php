<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use news\data\category\NewsCategory;
use news\data\category\NewsCategoryNodeTree;
use news\data\news\NewsCategoryList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class VooliaNewsContentType extends AbstractContentType {

	protected $icon = 'icon-archive';

	public $objectType = 'de.codequake.cms.content.type.voolia.news';

	public function validate($data) {
		if (empty($data['categoryIDs'])) throw new UserInputException('categoryIDs', 'empty');
	}

	public function getFormTemplate() {
		$excludedCategoryIDs = array_diff(NewsCategory::getAccessibleCategoryIDs(), NewsCategory::getAccessibleCategoryIDs(array(
			'canAddNews'
		)));
		$categoryTree = new NewsCategoryNodeTree('de.voolia.news.category', 0, false, $excludedCategoryIDs);
		$categoryList = $categoryTree->getIterator();
		$categoryList->setMaxDepth(0);
		WCF::getTPL()->assign('categoryList', $categoryList);
		return 'vooliaNewsContentType';
	}

	public function getOutput(Content $content) {
		$type = $content-type != '' ? $content-type : 'standard';
		$list = new NewsCategoryList($data['categoryIDs']);
		$list->sqlLimit = $content->limit;
		$list->readObjects();
		$list = $list->getObjects();
		WCF::getTPL()->assign(array(
			'objects' => $list,
			'type' => $type
		));
		return WCF::getTPL()->fetch('vooliaNewsContentTypeOutput', 'cms');
	}
}
