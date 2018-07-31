<?php

class WsSoFlexibiliteDelivery extends SoFlexibiliteDelivery
{
	public function getWebserviceParameters()
	{
		$webserviceParameters = [
			'table' => 'socolissimo_delivery_info',
			'primary' => 'id_order',
			'objectsNodeName' => 'delivery_infos',
			'objectNodeName' => 'delivery_info',
			'fields' => [
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
		];
		foreach ($webserviceParameters['fields'] as $field => $value) {
			$webserviceParameters['fields'][$field]['sqlId'] = $field;
		}

		return $webserviceParameters;
	}
}
