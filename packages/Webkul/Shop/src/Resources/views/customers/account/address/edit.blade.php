@extends('shop::customers.account.index')

@section('page_title')
    {{ __('shop::app.customer.account.address.edit.page-title') }}
@endsection

@section('account-content')
    <div class="account-layout">
        <div class="account-head mb-15">
            <span class="back-icon">
                <a href="{{ route('customer.address.index') }}"><i class="icon icon-menu-back"></i></a>
            </span>

            <span class="account-heading">{{ __('shop::app.customer.account.address.edit.title') }}</span>

            <span></span>
        </div>

        {!! view_render_event('bagisto.shop.customers.account.address.edit.before', ['address' => $address]) !!}

        <form id="customer-address-form" method="post" action="{{ route('customer.address.update', $address->id) }}" @submit.prevent="onSubmit">

            <div class="account-table-content">
                @method('PUT')

                @csrf

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.before', ['address' => $address]) !!}

                <div class="control-group" :class="[errors.has('company_name') ? 'has-error' : '']">
                    <label for="company_name">{{ __('shop::app.customer.account.address.edit.company_name') }}</label>

                    <input
                        class="control"
                        type="text"
                        name="company_name"
                        value="{{ old('company_name') ?: $address->company_name }}"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.edit.company_name') }}&quot;">

                    <span
                        class="control-error"
                        v-text="errors.first('company_name')"
                        v-if="errors.has('company_name')">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.company_name.after') !!}

                <div class="control-group" :class="[errors.has('first_name') ? 'has-error' : '']">
                    <label for="first_name" class="required">{{ __('shop::app.customer.account.address.create.first_name') }}</label>

                    <input
                        class="control"
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') ?: $address->first_name }}"
                        v-validate="'required'"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.first_name') }}&quot;">

                    <span
                        class="control-error"
                        v-text="errors.first('first_name')"
                        v-if="errors.has('first_name')">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.first_name.after') !!}

                <div class="control-group" :class="[errors.has('last_name') ? 'has-error' : '']">
                    <label for="last_name" class="required">{{ __('shop::app.customer.account.address.create.last_name') }}</label>

                    <input
                        class="control"
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') ?: $address->last_name }}"
                        v-validate="'required'"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.last_name') }}&quot;">

                    <span
                        class="control-error"
                        v-text="errors.first('last_name')"
                        v-if="errors.has('last_name')">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.last_name.after') !!}

                <div class="control-group" :class="[errors.has('vat_id') ? 'has-error' : '']">
                    <label for="vat_id">{{ __('shop::app.customer.account.address.create.vat_id') }}
                        <span class="help-note">{{ __('shop::app.customer.account.address.create.vat_help_note') }}</span>
                    </label>

                    <input
                        class="control"
                        type="text"
                        name="vat_id"
                        value="{{ old('vat_id') ?: $address->vat_id }}"
                        v-validate="''"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.vat_id') }}&quot;">

                    <span
                        class="control-error"
                        v-text="errors.first('vat_id')"
                        v-if="errors.has('vat_id')">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.vat_id.after') !!}

                <?php $addresses = explode(PHP_EOL, $address->address1); ?>

                <div class="control-group {{ $errors->has('address1.*') ? 'has-error' : '' }}">
                    <label for="address_0" class="required">{{ __('shop::app.customer.account.address.edit.street-address') }}</label>

                    <input
                        class="control"
                        id="address_0"
                        type="text"
                        name="address1[]"
                        value="{{ $addresses[0] ?? '' }}"
                        v-validate="'required'"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.street-address') }}&quot;">

                    <span class="control-error">{{ $errors->first('address1.*') }}</span>
                </div>

                @if (core()->getConfigData('customer.settings.address.street_lines') && core()->getConfigData('customer.settings.address.street_lines') > 1)
                    <div class="control-group" style="margin-top: -25px;">
                        @for ($i = 1; $i < core()->getConfigData('customer.settings.address.street_lines'); $i++)
                            <input
                                class="control"
                                id="address_{{ $i }}"
                                type="text"
                                name="address1[{{ $i }}]"
                                value="{{ $addresses[$i] ?? '' }}">
                        @endfor
                    </div>
                @endif

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.street-addres.after') !!}

                <div class="control-group" :class="[errors.has('city') ? 'has-error' : '']">
                    <label for="city" class="required">{{ __('shop::app.customer.account.address.create.city') }}</label>

                    <input
                        class="control"
                        type="text"
                        name="city"
                        value="{{ old('city') ?: $address->city }}"
                        v-validate="'required|regex:^[a-zA-Z \-]*$'"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.city') }}&quot;">

                    <span
                        class="control-error"
                        v-text="errors.first('city')"
                        v-if="errors.has('city')">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.create.after') !!}

                @include ('shop::customers.account.address.country-state', ['countryCode' => old('country') ?? $address->country, 'stateCode' => old('state') ?? $address->state])

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.country-state.after') !!}

                <div class="control-group" :class="[errors.has('postcode') ? 'has-error' : '']">
                    <label for="postcode" class="{{ core()->isPostCodeRequired() ? 'required' : '' }}">{{ __('shop::app.customer.account.address.create.postcode') }}</label>

                    <input
                        type="text"
                        class="control"
                        name="postcode"
                        v-validate="'{{ core()->isPostCodeRequired() ? 'required' : '' }}'"
                        value="{{ old('postcode') ?: $address->postcode }}"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.postcode') }}&quot;">

                    <span
                        class="control-error"
                        v-text="errors.first('postcode')"
                        v-if="errors.has('postcode')">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.postcode.after') !!}

                <div class="control-group" :class="[errors.has('phone') ? 'has-error' : '']">
                    <label for="phone" class="required">{{ __('shop::app.customer.account.address.create.phone') }}</label>

                    <input
                        class="control"
                        type="text"
                        name="phone"
                        value="{{ old('phone') ?: $address->phone }}"
                        v-validate="'required'"
                        data-vv-as="&quot;{{ __('shop::app.customer.account.address.create.phone') }}&quot;">

                    <span
                        class="control-error"
                        v-text="errors.first('phone')"
                        v-if="errors.has('phone')">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.customers.account.address.edit_form_controls.after', ['address' => $address]) !!}

                <div class="control-group">
                    <span class="checkbox">
                        <input
                            class="control"
                            id="default_address"
                            type="checkbox"
                            name="default_address"
                            {{ $address->default_address ? 'checked' : '' }}>

                        <label class="checkbox-view" for="default_address"></label>

                        {{ __('shop::app.customer.account.address.default-address') }}
                    </span>
                </div>

                <div class="button-group">
                    <button class="btn btn-primary btn-lg" type="submit">
                        {{ __('shop::app.customer.account.address.create.submit') }}
                    </button>
                </div>
            </div>
        </form>

        {!! view_render_event('bagisto.shop.customers.account.address.edit.after', ['address' => $address]) !!}
    </div>
@endsection
