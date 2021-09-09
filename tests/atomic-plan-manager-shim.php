<?php

class Atomic_Plan_Manager {

	/**
	 * Free plan slug
	 * @var string
	 */
	public const FREE_PLAN_SLUG       = 'free';

	/**
	 * Business plan slug
	 * @var string
	 */
	public const BUSINESS_PLAN_SLUG   = 'business';

	/**
	 * Ecommerce plan slug
	 * @var string
	 */
	public const ECOMMERCE_PLAN_SLUG  = 'ecommerce';

	private static $current_plan_slug = 'free';

	public static function current_plan_slug() {
		return self::$current_plan_slug;
	}

	public static function set_current_plan_slug( $slug ) {
		self::$current_plan_slug;
	}
}
