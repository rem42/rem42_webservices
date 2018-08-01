<?php

class WsSoFlexibiliteDelivery extends SoFlexibiliteDelivery
{
	static $webserviceParameters = [
		'objectSqlId' => 'id_order',
		'retrieveData' => [
			'className' => 'WsSoFlexibiliteDelivery',
			'retrieveMethod' => 'getWebserviceObjectList',
			'params' => [],
			'table' => 'socolissimo_delivery_info',
		],
		'fields' => [
			'id' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
			'id_order' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
			'id_cart' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
			'id_customer' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
			'id_point' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
			'firstname' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'lastname' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'company' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'telephone' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32],
			'email' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128],
			'type' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'libelle' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'indice' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'postcode' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'city' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'country' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'address1' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'address2' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'lieudit' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'informations' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
			'reseau' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
		],
		'objectsNodeName' => 'delivery_infos',
		'objectNodeName' => 'delivery_info',
	];

	public function getWebserviceParameters()
	{
		$webserviceParameters = self::$webserviceParameters;
		foreach ($webserviceParameters['fields'] as $field => $value) {
			$webserviceParameters['fields'][$field]['sqlId'] = $field;
		}

		return $webserviceParameters;
	}

	/**
	 * Returns webservice object list.
	 *
	 * @param string $sql_join
	 * @param string $sql_filter
	 * @param string $sql_sort
	 * @param string $sql_limit
	 *
	 * @return array|null
	 * @throws PrestaShopDatabaseException
	 */
	public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$assoc = Shop::getAssoTable($this->def['table']);
		$class_name = WebserviceRequest::$ws_current_classname;
		$vars = get_class_vars($class_name);
		if ($assoc !== false) {
			if ($assoc['type'] !== 'fk_shop') {
				$multi_shop_join = ' LEFT JOIN `'._DB_PREFIX_.bqSQL($this->def['table']).'_'.bqSQL($assoc['type']).'`
										AS `multi_shop_'.bqSQL($this->def['table']).'`
										ON (main.`'.bqSQL($this->def['primary']).'` = `multi_shop_'.bqSQL($this->def['table']).'`.`'.bqSQL($this->def['primary']).'`)';
				$sql_filter = 'AND `multi_shop_'.bqSQL($this->def['table']).'`.id_shop = '.Context::getContext()->shop->id.' '.$sql_filter;
				$sql_join = $multi_shop_join.' '.$sql_join;
			} else {
				$vars = get_class_vars($class_name);
				foreach ($vars['shopIDs'] as $id_shop) {
					$or[] = '(main.id_shop = '.(int)$id_shop.(isset($this->def['fields']['id_shop_group']) ? ' OR (id_shop = 0 AND id_shop_group='.(int)Shop::getGroupFromShop((int)$id_shop).')' : '').')';
				}

				$prepend = '';
				if (count($or)) {
					$prepend = 'AND ('.implode('OR', $or).')';
				}
				$sql_filter = $prepend.' '.$sql_filter;
			}
		}
		$query = '
		SELECT DISTINCT main.`'.bqSQL($this->def['primary']).'` FROM `'._DB_PREFIX_.bqSQL($this->def['table']).'` AS main
		'.$sql_join.'
		WHERE 1 '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}
}
