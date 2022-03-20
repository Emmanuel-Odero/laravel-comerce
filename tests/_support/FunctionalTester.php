<?php

use Actions\CleanAction;
use Codeception\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\Customer\Models\Customer;
use Webkul\User\Models\Admin;

/**
 * Inherited methods.
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends Actor
{
    use _generated\FunctionalTesterActions, CleanAction;

    /**
     * Set the logged in user to the admin identity.
     *
     * @param  \Webkul\User\Models\Admin|null  $admin
     * @return \Webkul\User\Models\Admin
     *
     * @throws \Exception
     */
    public function loginAsAdmin(Admin $admin = null): Admin
    {
        $I = $this;

        if (! $admin) {
            $admin = $I->grabRecord(Admin::class, ['email' => 'admin@example.com']);
        }

        if (! $admin) {
            throw new Exception(
                'Admin user not found in database. Please ensure Seeders are executed'
            );
        }

        Auth::guard('admin')->login($admin);

        $I->seeAuthentication('admin');

        return $admin;
    }

    /**
     * Set the logged in user to the customer identity.
     *
     * @param  \Webkul\User\Models\Customer|null  $customer
     * @return \Webkul\Customer\Models\Customer
     *
     * @throws \Exception
     */
    public function loginAsCustomer(Customer $customer = null): Customer
    {
        $I = $this;

        if (! $customer) {
            $customer = $I->have(Customer::class);
        }

        Auth::guard('customer')->login($customer);

        $I->seeAuthentication('customer');

        return $customer;
    }

    /**
     * On admin route.
     *
     * @param  string  $name
     * @param  array  $params
     * @param  bool  $routeCheck
     * @return void
     */
    public function amOnAdminRoute(string $name, array $params = [], bool $routeCheck = true)
    {
        $I = $this;
        $I->amOnRoute($name, $params);

        if ($routeCheck) {
            $I->seeCurrentRouteIs($name);
        }

        $routes = Route::getRoutes();
        $middlewares = $routes->getByName($name)->middleware();
        $I->assertContains('admin', $middlewares, 'check that admin middleware is applied');
    }

    /**
     * Set specific `Webkul/Core` configuration keys to a given value.
     *
     * TODO: Change method as soon as there is a method to set core config data.
     *
     * @param  array  $data
     * @return void
     */
    public function setConfigData($data): void
    {
        foreach ($data as $key => $value) {
            if (DB::table('core_config')->where('code', '=', $key)->exists()) {
                DB::table('core_config')
                    ->where('code', '=', $key)
                    ->update(['value' => $value]);
            } else {
                DB::table('core_config')->insert([
                    'code'         => $key,
                    'value'        => $value,
                    'channel_code' => null,
                    'locale_code'  => null,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Use default theme.
     *
     * @return void.
     */
    public function useDefaultTheme(): void
    {
        $channel = core()->getCurrentChannel();

        if ($channel->theme !== 'default') {
            $channel->update(['theme' => 'default']);
        }
    }
}
