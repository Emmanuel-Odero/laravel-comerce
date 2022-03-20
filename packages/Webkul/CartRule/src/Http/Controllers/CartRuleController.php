<?php

namespace Webkul\CartRule\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\CartRuleDataGrid;
use Webkul\CartRule\Repositories\CartRuleRepository;

class CartRuleController extends Controller
{
    /**
     * Initialize _config, a default request parameter with route.
     *
     * @param array
     */
    protected $_config;

    /**
     * To hold cart repository instance.
     *
     * @var \Webkul\CartRule\Repositories\CartRuleRepository
     */
    protected $cartRuleRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\CartRule\Repositories\CartRuleRepository       $cartRuleRepository
     * @return void
     */
    public function __construct(
        CartRuleRepository $cartRuleRepository
    ) {
        $this->_config = request('_config');

        $this->cartRuleRepository = $cartRuleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(CartRuleDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Copy a given Cart Rule id. Always make the copy is inactive so the
     * user is able to configure it before setting it live.
     *
     * @param  int  $cartRuleId
     * @return \Illuminate\View\View
     */
    public function copy(int $cartRuleId): View
    {
        $originalCartRule = $this->cartRuleRepository
            ->findOrFail($cartRuleId)
            ->load('channels')
            ->load('customer_groups');

        $copiedCartRule = $originalCartRule
            ->replicate()
            ->fill([
                'status' => 0,
                'name'   => __('admin::app.copy-of') . $originalCartRule->name,
            ]);

        $copiedCartRule->save();

        foreach ($copiedCartRule->channels as $channel) {
            $copiedCartRule->channels()->save($channel);
        }

        foreach ($copiedCartRule->customer_groups as $group) {
            $copiedCartRule->customer_groups()->save($group);
        }

        return view($this->_config['view'], [
            'cartRule' => $copiedCartRule,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        try {
            $this->validate(request(), [
                'name'                => 'required',
                'channels'            => 'required|array|min:1',
                'customer_groups'     => 'required|array|min:1',
                'coupon_type'         => 'required',
                'use_auto_generation' => 'required_if:coupon_type,==,1',
                'coupon_code'         => 'required_if:use_auto_generation,==,0|unique:cart_rule_coupons,code',
                'starts_from'         => 'nullable|date',
                'ends_till'           => 'nullable|date|after_or_equal:starts_from',
                'action_type'         => 'required',
                'discount_amount'     => 'required|numeric',
            ]);

            $data = request()->all();

            $this->cartRuleRepository->create($data);

            session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Cart Rule']));

            return redirect()->route($this->_config['redirect']);
        } catch (ValidationException $e) {
            if ($firstError = collect($e->errors())->first()) {
                session()->flash('error', $firstError[0]);
            }
        }

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cartRule = $this->cartRuleRepository->findOrFail($id);

        return view($this->_config['view'], compact('cartRule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate(request(), [
                'name'                => 'required',
                'channels'            => 'required|array|min:1',
                'customer_groups'     => 'required|array|min:1',
                'coupon_type'         => 'required',
                'use_auto_generation' => 'required_if:coupon_type,==,1',
                'starts_from'         => 'nullable|date',
                'ends_till'           => 'nullable|date|after_or_equal:starts_from',
                'action_type'         => 'required',
                'discount_amount'     => 'required|numeric',
            ]);

            $cartRule = $this->cartRuleRepository->findOrFail($id);

            if ($cartRule->coupon_type) {
                if ($cartRule->cart_rule_coupon) {
                    $this->validate(request(), [
                        'coupon_code' => 'required_if:use_auto_generation,==,0|unique:cart_rule_coupons,code,' . $cartRule->cart_rule_coupon->id,
                    ]);
                } else {
                    $this->validate(request(), [
                        'coupon_code' => 'required_if:use_auto_generation,==,0|unique:cart_rule_coupons,code',
                    ]);
                }
            }

            $cartRule = $this->cartRuleRepository->update(request()->all(), $id);

            session()->flash('success', trans('admin::app.response.update-success', ['name' => 'Cart Rule']));

            return redirect()->route($this->_config['redirect']);
        } catch (ValidationException $e) {
            if ($firstError = collect($e->errors())->first()) {
                session()->flash('error', $firstError[0]);
            }
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->cartRuleRepository->findOrFail($id);

        try {
            $this->cartRuleRepository->delete($id);

            return response()->json(['message' => trans('admin::app.response.delete-success', ['name' => 'Cart Rule'])]);
        } catch (Exception $e) {}

        return response()->json(['message' => trans('admin::app.response.delete-failed', ['name' => 'Cart Rule'])], 400);
    }
}
