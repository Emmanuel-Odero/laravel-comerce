<?php

use Illuminate\Support\Facades\Route;
use Webkul\CartRule\Http\Controllers\CartRuleController;
use Webkul\CartRule\Http\Controllers\CartRuleCouponController;
use Webkul\CatalogRule\Http\Controllers\CatalogRuleController;
use Webkul\Core\Http\Controllers\SubscriptionController;
use Webkul\Marketing\Http\Controllers\CampaignController;
use Webkul\Marketing\Http\Controllers\EventController;
use Webkul\Marketing\Http\Controllers\TemplateController;

/**
 * Marketing routes.
 */
Route::group(['middleware' => ['web', 'admin', 'admin_locale'], 'prefix' => config('app.admin_url')], function () {
    Route::prefix('promotions')->group(function () {
        /**
         * Cart rules routes.
         */
        Route::get('cart-rules', [CartRuleController::class, 'index'])->defaults('_config', [
            'view' => 'admin::marketing.promotions.cart-rules.index',
        ])->name('admin.cart-rules.index');

        Route::get('cart-rules/create', [CartRuleController::class, 'create'])->defaults('_config', [
            'view' => 'admin::marketing.promotions.cart-rules.create',
        ])->name('admin.cart-rules.create');

        Route::post('cart-rules/create', [CartRuleController::class, 'store'])->defaults('_config', [
            'redirect' => 'admin.cart-rules.index',
        ])->name('admin.cart-rules.store');

        Route::get('cart-rules/copy/{id}', [CartRuleController::class, 'copy'])->defaults('_config', [
            'view' => 'admin::marketing.promotions.cart-rules.edit',
        ])->name('admin.cart-rules.copy');

        Route::get('cart-rules/edit/{id}', [CartRuleController::class, 'edit'])->defaults('_config', [
            'view' => 'admin::marketing.promotions.cart-rules.edit',
        ])->name('admin.cart-rules.edit');

        Route::post('cart-rules/edit/{id}', [CartRuleController::class, 'update'])->defaults('_config', [
            'redirect' => 'admin.cart-rules.index',
        ])->name('admin.cart-rules.update');

        Route::post('cart-rules/delete/{id}', [CartRuleController::class, 'destroy'])->name('admin.cart-rules.delete');

        /**
         * Cart rule coupons routes.
         */
        Route::get('cart-rule-coupons/{id}', [CartRuleCouponController::class, 'index'])->name('admin.cart-rules-coupons.index');

        Route::post('cart-rule-coupons/{id}', [CartRuleCouponController::class, 'store'])->name('admin.cart-rules-coupons.store');

        Route::post('cart-rule-coupons/mass-delete', [CartRuleCouponController::class, 'massDelete'])->name('admin.cart-rule-coupons.mass-delete');

        /**
         * Catalog rules routes.
         */
        Route::get('catalog-rules', [CatalogRuleController::class, 'index'])->defaults('_config', [
            'view' => 'admin::marketing.promotions.catalog-rules.index',
        ])->name('admin.catalog-rules.index');

        Route::get('catalog-rules/create', [CatalogRuleController::class, 'create'])->defaults('_config', [
            'view' => 'admin::marketing.promotions.catalog-rules.create',
        ])->name('admin.catalog-rules.create');

        Route::post('catalog-rules/create', [CatalogRuleController::class, 'store'])->defaults('_config', [
            'redirect' => 'admin.catalog-rules.index',
        ])->name('admin.catalog-rules.store');

        Route::get('catalog-rules/edit/{id}', [CatalogRuleController::class, 'edit'])->defaults('_config', [
            'view' => 'admin::marketing.promotions.catalog-rules.edit',
        ])->name('admin.catalog-rules.edit');

        Route::post('catalog-rules/edit/{id}', [CatalogRuleController::class, 'update'])->defaults('_config', [
            'redirect' => 'admin.catalog-rules.index',
        ])->name('admin.catalog-rules.update');

        Route::post('catalog-rules/delete/{id}', [CatalogRuleController::class, 'destroy'])->name('admin.catalog-rules.delete');

        /**
         * Campaigns routes.
         */
        Route::get('campaigns', [CampaignController::class, 'index'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.campaigns.index',
        ])->name('admin.campaigns.index');

        Route::get('campaigns/create', [CampaignController::class, 'create'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.campaigns.create',
        ])->name('admin.campaigns.create');

        Route::post('campaigns/create', [CampaignController::class, 'store'])->defaults('_config', [
            'redirect' => 'admin.campaigns.index',
        ])->name('admin.campaigns.store');

        Route::get('campaigns/edit/{id}', [CampaignController::class, 'edit'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.campaigns.edit',
        ])->name('admin.campaigns.edit');

        Route::post('campaigns/edit/{id}', [CampaignController::class, 'update'])->defaults('_config', [
            'redirect' => 'admin.campaigns.index',
        ])->name('admin.campaigns.update');

        Route::post('campaigns/delete/{id}', [CampaignController::class, 'destroy'])->name('admin.campaigns.delete');

        /**
         * Emails templates routes.
         */
        Route::get('email-templates', [TemplateController::class, 'index'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.templates.index',
        ])->name('admin.email-templates.index');

        Route::get('email-templates/create', [TemplateController::class, 'create'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.templates.create',
        ])->name('admin.email-templates.create');

        Route::post('email-templates/create', [TemplateController::class, 'store'])->defaults('_config', [
            'redirect' => 'admin.email-templates.index',
        ])->name('admin.email-templates.store');

        Route::get('email-templates/edit/{id}', [TemplateController::class, 'edit'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.templates.edit',
        ])->name('admin.email-templates.edit');

        Route::post('email-templates/edit/{id}', [TemplateController::class, 'update'])->defaults('_config', [
            'redirect' => 'admin.email-templates.index',
        ])->name('admin.email-templates.update');

        Route::post('email-templates/delete/{id}', [TemplateController::class, 'destroy'])->name('admin.email-templates.delete');

        /**
         * Events routes.
         */
        Route::get('events', [EventController::class, 'index'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.events.index',
        ])->name('admin.events.index');

        Route::get('events/create', [EventController::class, 'create'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.events.create',
        ])->name('admin.events.create');

        Route::post('events/create', [EventController::class, 'store'])->defaults('_config', [
            'redirect' => 'admin.events.index',
        ])->name('admin.events.store');

        Route::get('events/edit/{id}', [EventController::class, 'edit'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.events.edit',
        ])->name('admin.events.edit');

        Route::post('events/edit/{id}', [EventController::class, 'update'])->defaults('_config', [
            'redirect' => 'admin.events.index',
        ])->name('admin.events.update');

        Route::post('events/delete/{id}', [EventController::class, 'destroy'])->name('admin.events.delete');

        /**
         * Admin store front settings route.
         */
        Route::get('/subscribers', [SubscriptionController::class, 'index'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.subscribers.index',
        ])->name('admin.customers.subscribers.index');

        /**
         * Destroy a newsletter subscription item.
         */
        Route::post('subscribers/delete/{id}', [SubscriptionController::class, 'destroy'])->name('admin.customers.subscribers.delete');

        Route::get('subscribers/edit/{id}', [SubscriptionController::class, 'edit'])->defaults('_config', [
            'view' => 'admin::marketing.email-marketing.subscribers.edit',
        ])->name('admin.customers.subscribers.edit');

        Route::put('subscribers/update/{id}', [SubscriptionController::class, 'update'])->defaults('_config', [
            'redirect' => 'admin.customers.subscribers.index',
        ])->name('admin.customers.subscribers.update');
    });
});
