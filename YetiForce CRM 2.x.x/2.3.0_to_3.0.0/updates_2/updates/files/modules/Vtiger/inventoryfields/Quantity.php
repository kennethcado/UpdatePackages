<?php

/**
 * Inventory Quantity Field Class
 * @package YetiForce.Fields
 * @license licenses/License.html
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
class Vtiger_Quantity_InventoryField extends Vtiger_Basic_InventoryField
{

	protected $name = 'Quantity';
	protected $defaultLabel = 'LBL_QUANTITY';
	protected $defaultValue = '1';
	protected $columnName = 'qty';
	protected $dbType = 'decimal(25,3) NOT NULL DEFAULT 0';
	protected $customColumn = [
		'qtyparam' => 'tinyint(1) NOT NULL DEFAULT 0',
	];

	/**
	 * Getting value to display
	 * @param type $value
	 * @return type
	 */
	public function getDisplayValue($value)
	{
		return Vtiger_Functions::formatDecimal($value);
	}
}
