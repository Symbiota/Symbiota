<?php

class UserUtil {
	const COLLECTION_ADMIN = 'CollAdmin';

	/**
	 * Checks if user has Collection Admin permissions on given
	 * collection id.
	 *
	 * @param int $collId Collection id
	 * @return bool
	 **/
	static function isCollectionAdmin(int $collId): bool {
		global $USER_RIGHTS, $IS_ADMIN;
		return $IS_ADMIN || in_array($collId, $USER_RIGHTS[self::COLLECTION_ADMIN] ?? []);
	}

	/**
	 * If passed boolean is false then include accessDenied page
	 * and hard exit;
	 *
	 * @param bool $authCheck Represents if user has permissons or not
	 * @return void
	 **/
	static function checkAccess(bool $authCheck): void {
		global $SERVER_ROOT;
		if(!$authCheck) {
			include($SERVER_ROOT . '/includes/accessDenied.php');
			exit;
		}
	}

	/**
	 * Checks if user is a collectionAdmin for given collId
	 * If then are not then php will include accessDenied.php
	 * page and hard exit.
	 *
	 * @param int $collId Collection id
	 * @return void
	 **/
	static function isCollectionAdminOrDenyAcess(int $collId): void {
		self::checkAccess(self::isCollectionAdmin($collId));
	}
}
