<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): YetiForce.com.
 * *********************************************************************************** */

class Accounts_DetailView_Model extends Vtiger_DetailView_Model
{

	/**
	 * Function to get the detail view links (links and widgets)
	 * @param <array> $linkParams - parameters which will be used to calicaulate the params
	 * @return <array> - array of link models in the format as below
	 *                   array('linktype'=>list of link models);
	 */
	public function getDetailViewLinks($linkParams)
	{
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$recordModel = $this->getRecord();
		$linkModelList = parent::getDetailViewLinks($linkParams);
		$moduleModel = $this->getModule();
		$recordId = $recordModel->getId();

		if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'DetailTransferOwnership')) {
			$massActionLink = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
				'linkurl' => 'javascript:Vtiger_Detail_Js.triggerTransferOwnership("index.php?module=' . $moduleModel->getName() . '&view=MassActionAjax&mode=transferOwnership")',
				'linkicon' => 'glyphicon glyphicon-user'
			);
			$linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}
		return $linkModelList;
	}

	function getDetailViewRelatedLinks()
	{
		$recordModel = $this->getRecord();
		$moduleName = $recordModel->getModuleName();
		$parentModuleModel = $this->getModule();
		$this->getWidgets();
		$relatedLinks = [];

		if ($parentModuleModel->isSummaryViewSupported() && $this->widgetsList) {
			$relatedLinks = [[
				'linktype' => 'DETAILVIEWTAB',
				'linklabel' => vtranslate('LBL_RECORD_SUMMARY', $moduleName),
				'linkKey' => 'LBL_RECORD_SUMMARY',
				'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showDetailViewByMode&requestMode=summary',
				'linkicon' => '',
				'related' => 'Summary'
			]];
		}
		//link which shows the summary information(generally detail of record)
		$relatedLinks[] = [
			'linktype' => 'DETAILVIEWTAB',
			'linklabel' => vtranslate('LBL_RECORD_DETAILS', $moduleName),
			'linkKey' => 'LBL_RECORD_DETAILS',
			'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showDetailViewByMode&requestMode=full',
			'linkicon' => '',
			'related' => 'Details'
		];

		if ($moduleName == 'Leads') {
			$showPSTab = vtlib_isModuleActive('OutsourcedProducts') || vtlib_isModuleActive('Products') || vtlib_isModuleActive('Services') || vtlib_isModuleActive('OSSOutsourcedServices');
		}
		if ($moduleName == 'Accounts') {
			$showPSTab = vtlib_isModuleActive('OutsourcedProducts') || vtlib_isModuleActive('Products') || vtlib_isModuleActive('Services') || vtlib_isModuleActive('OSSOutsourcedServices') || vtlib_isModuleActive('Assets') || vtlib_isModuleActive('OSSSoldServices');
		}
		if ('Contacts' != $moduleName && $showPSTab) {
			$relatedLinks[] = array(
				'linktype' => 'DETAILVIEWTAB',
				'linklabel' => vtranslate('LBL_RECORD_SUMMARY_PRODUCTS_SERVICES', $moduleName),
				'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showRelatedProductsServices&requestMode=summary',
				'linkicon' => '',
				'linkKey' => 'LBL_RECORD_SUMMARY',
				'related' => 'ProductsAndServices',
				'countRelated' => true
			);
		}
		$modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
		if ($parentModuleModel->isCommentEnabled() && $modCommentsModel->isPermitted('DetailView')) {
			$relatedLinks[] = array(
				'linktype' => 'DETAILVIEWTAB',
				'linklabel' => 'ModComments',
				'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showAllComments&type=' . $modCommentsModel::getDefaultViewComments(),
				'linkicon' => '',
				'related' => 'Comments',
				'countRelated' => true
			);
		}

		if ($parentModuleModel->isTrackingEnabled()) {
			$relatedLinks[] = array(
				'linktype' => 'DETAILVIEWTAB',
				'linklabel' => 'LBL_UPDATES',
				'linkurl' => $recordModel->getDetailViewUrl() . '&mode=showRecentActivities&page=1',
				'linkicon' => '',
				'related' => 'Updates'
			);
		}

		$relationModels = $parentModuleModel->getRelations();

		foreach ($relationModels as $relation) {
			//TODO : Way to get limited information than getting all the information
			$link = array(
				'linktype' => 'DETAILVIEWRELATED',
				'linklabel' => $relation->get('label'),
				'linkurl' => $relation->getListUrl($recordModel),
				'linkicon' => '',
				'relatedModuleName' => $relation->get('relatedModuleName')
			);
			$relatedLinks[] = $link;
		}

		return $relatedLinks;
	}
}
