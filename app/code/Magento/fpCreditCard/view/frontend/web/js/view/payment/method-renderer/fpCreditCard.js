/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'underscore',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'mage/validation',
        'mage/translate'
    ],
    function ($,_, Component, creditCardData, cardNumberValidator, $t) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_fpCreditCard/payment/form',
                transactionResult: '',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardVerificationNumber: '',
                selectedCardType: null
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'transactionResult',
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'selectedCardType'
                    ]);
                return this;
            },

            initialize: function() {
                var self = this;
                this._super();
                //Set credit card number to credit card data object
                this.creditCardNumber.subscribe(function(value) {
                    var result;
                    self.selectedCardType(null);
 
                    if (value == '' || value == null) {
                        return false;
                    }
                    result = cardNumberValidator(value);
 
                    if (!result.isPotentiallyValid || !result.isValid) {
                        return false;
                    }
                    if (result.card !== null) {
                        self.selectedCardType(result.card.type);
                        creditCardData.creditCard = result.card;
                    }
 
                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                    }
                });
 
                //Set expiration year to credit card data object
                this.creditCardExpYear.subscribe(function(value) {
                    creditCardData.expirationYear = value;
                });
 
                //Set expiration month to credit card data object
                this.creditCardExpMonth.subscribe(function(value) {
                    creditCardData.expirationYear = value;
                });
 
                //Set cvv code to credit card data object
                this.creditCardVerificationNumber.subscribe(function(value) {
                    creditCardData.cvvCode = value;
                });
            },

            isActive: function () {
                return true;
            },
 
            getCcAvailableTypes: function() {
                return window.checkoutConfig.payment.fpCreditCard.availableTypes['Magentopayment'];
            },
            
            creditCardNumber: function() {
                return window.checkoutConfig.payment.fpCreditCard.creditCardNumber['Magentopayment'];
            },

            getCcMonths: function() {
                return window.checkoutConfig.payment.fpCreditCard.months['Magentopayment'];
            },
 
            getCcYears: function() {
                return window.checkoutConfig.payment.fpCreditCard.years['Magentopayment'];
            },

 
            hasCvv: function() {
                return window.checkoutConfig.payment.fpCreditCard.hasVerification['Magentopayment'];
            },
 
            getCcAvailableTypesValues: function() {
                return _.map(this.getCcAvailableTypes(), function(value, key) {
                    return {
                        'value': key,
                        'type': value
                    }
                });
            },
            getCcMonthsValues: function() {
                return _.map(this.getCcMonths(), function(value, key) {
                    return {
                        'value': key,
                        'month': value
                    }
                });
            },
            getCcYearsValues: function() {
                return _.map(this.getCcYears(), function(value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },

            hasVerification: function() {
                return _.map(this.hasCvv(), function(value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },

            getCode: function() {
                return 'fpCreditCard';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    
                    'additional_data': {
                        'card_number' : this.creditCardNumber(),
                        'card_exp_month' : this.creditCardExpMonth(),
                        'card_exp_year' : this.creditCardExpYear(),
                        'card_cvv' : this.creditCardVerificationNumber(),
                        'card_type' : this.creditCardType()
                    }
                };
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.fpCreditCard.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            }
            
        });
    }
);