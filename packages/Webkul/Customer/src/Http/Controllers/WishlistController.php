<?php

namespace Webkul\Customer\Http\Controllers;

use Cart;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\Product\Repositories\ProductRepository;

class WishlistController extends Controller
{
    /**
     * Contains route related configuration.
     *
     * @var array
     */
    protected $_config;

    /**
     * Current customer.
     *
     * @var \Webkul\Customer\Models\Customer
     */
    protected $currentCustomer;

    /**
     * Product repository instance.
     *
     * @var \Webkul\Customer\Repositories\WishlistRepository
     */
    protected $wishlistRepository;

    /**
     * Wishlist repository instance.
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\WishlistRepository  $wishlistRepository
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @return void
     */
    public function __construct(
        WishlistRepository $wishlistRepository,
        ProductRepository $productRepository
    ) {
        $this->_config = request('_config');

        $this->wishlistRepository = $wishlistRepository;

        $this->productRepository = $productRepository;

        $this->currentCustomer = auth()->guard('customer')->user();
    }

    /**
     * Displays the listing resources if the customer having items in wishlist.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (! core()->getConfigData('general.content.shop.wishlist_option')) {
            abort(404);
        }

        return view($this->_config['view'], [
            'items' => $this->wishlistRepository->getCustomerWishlist(),
            'isSharingEnabled' => $this->isSharingEnabled(),
            'isWishlistShared' => $this->currentCustomer->isWishlistShared(),
            'wishlistSharedLink' => $this->currentCustomer->getWishlistSharedLink()
        ]);
    }

    /**
     * Function to add item to the wishlist.
     *
     * @param  int  $itemId
     * @return \Illuminate\Http\Response
     */
    public function add($itemId)
    {
        $product = $this->productRepository->findOneByField('id', $itemId);

        if (! $product->status)
            return redirect()->back();

        $data = [
            'channel_id'  => core()->getCurrentChannel()->id,
            'product_id'  => $itemId,
            'customer_id' => $this->currentCustomer->id,
        ];

        $checked = $this->wishlistRepository->findWhere([
            'channel_id'  => core()->getCurrentChannel()->id,
            'product_id'  => $itemId,
            'customer_id' => $this->currentCustomer->id,
        ]);

        if ($product->parent && $product->parent->type !== 'configurable') {
            $product = $this->productRepository->findOneByField('id', $product->parent_id);
            $data['product_id'] = $product->id;
        }

        if ($checked->isEmpty()) {
            if ($this->wishlistRepository->create($data)) {
                session()->flash('success', trans('customer::app.wishlist.success'));

                return redirect()->back();
            } else {
                session()->flash('error', trans('customer::app.wishlist.failure'));

                return redirect()->back();
            }
        } else {
            $this->wishlistRepository->findOneWhere([
                'product_id' => $data['product_id']
            ])->delete();

            session()->flash('success', trans('customer::app.wishlist.removed'));

            return redirect()->back();
        }
    }

    /**
     * Share wishlist.
     *
     * @return \Illuminate\Http\Response
     */
    public function share()
    {
        if ($this->isSharingEnabled()) {
            $data = $this->validate(request(), [
                'shared' => 'required|boolean'
            ]);

            $updateCounts = $this->currentCustomer->wishlist_items()->update(['shared' => $data['shared']]);

            if ($updateCounts && $updateCounts > 0) {
                session()->flash('success', __('shop::app.customer.account.wishlist.update-message'));

                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    /**
     * View of shared wishlist.
     *
     * @return \Illuminate\Http\Response
     */
    public function shared(CustomerRepository $customerRepository)
    {
        if (
            ! $this->isSharingEnabled()
            || ! request()->hasValidSignature()
            || ! core()->getConfigData('general.content.shop.wishlist_option')
        ) {
            abort(404);
        }

        $customer = $customerRepository->find(request()->get('id'));

        $items = $customer->wishlist_items()->where('shared', 1)->get();

        if ($customer && $items->isNotEmpty()) {
            return view($this->_config['view'], compact('customer', 'items'));
        }

        /**
         * All remaining cases should be aborted with 404 page.
         */
        abort(404);
    }

    /**
     * Function to remove item to the wishlist.
     *
     * @param  int  $itemId
     * @return \Illuminate\Http\Response
     */
    public function remove($itemId)
    {
        $customerWishlistItems = $this->currentCustomer->wishlist_items;

        foreach ($customerWishlistItems as $customerWishlistItem) {
            if ($itemId == $customerWishlistItem->id) {
                $this->wishlistRepository->delete($itemId);

                session()->flash('success', trans('customer::app.wishlist.removed'));

                return redirect()->back();
            }
        }

        session()->flash('error', trans('customer::app.wishlist.remove-fail'));

        return redirect()->back();
    }

    /**
     * Function to move item from wishlist to cart.
     *
     * @param  int  $itemId
     * @return \Illuminate\Http\Response
     */
    public function move($itemId)
    {
        $wishlistItem = $this->wishlistRepository->findOneWhere([
            'id'          => $itemId,
            'customer_id' => $this->currentCustomer->id,
        ]);

        if (! $wishlistItem) {
            abort(404);
        }

        try {
            $result = Cart::moveToCart($wishlistItem);

            if ($result) {
                session()->flash('success', trans('shop::app.customer.account.wishlist.moved'));
            } else {
                session()->flash('info', trans('shop::app.checkout.cart.integrity.missing_options'));

                return redirect()->route('shop.productOrCategory.index', $wishlistItem->product->url_key);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            report($e);

            session()->flash('warning', $e->getMessage());

            return redirect()->route('shop.productOrCategory.index',  $wishlistItem->product->url_key);
        }
    }

    /**
     * Function to remove all of the items items in the customer's wishlist.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeAll()
    {
        $wishlistItems = $this->currentCustomer->wishlist_items;

        if ($wishlistItems->count() > 0) {
            foreach ($wishlistItems as $wishlistItem) {
                $this->wishlistRepository->delete($wishlistItem->id);
            }
        }

        session()->flash('success', trans('customer::app.wishlist.remove-all-success'));

        return redirect()->back();
    }

    /**
     * Is sharing enabled.
     *
     * @return bool
     */
    public function isSharingEnabled(): bool
    {
        return (bool) core()->getConfigData('customer.settings.wishlist.share')
            ? true
            : false;
    }
}
