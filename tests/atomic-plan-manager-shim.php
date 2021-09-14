<?php

class Atomic_Plan_Manager {
	public const FREE_PLAN_SLUG       = 'free';
	public const BUSINESS_PLAN_SLUG   = 'business';
	public const ECOMMERCE_PLAN_SLUG  = 'ecommerce';

	// Default to free, don't make assumptions about having a business or ecom plan on atomic.
	private static $current_plan_slug = 'free';

	public static function current_plan_slug() {
		return self::$current_plan_slug;
	}

	public static function set_current_plan_slug( $slug ) {
		self::$current_plan_slug = $slug;
	}
}
