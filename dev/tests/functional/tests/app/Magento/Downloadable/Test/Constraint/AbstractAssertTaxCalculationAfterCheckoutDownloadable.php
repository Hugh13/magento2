<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Downloadable\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Checkout\Test\Page\CheckoutOnepageSuccess;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Page\OrderView;
use Mtf\Fixture\InjectableFixture;
use Magento\Tax\Test\Constraint\AbstractAssertTaxCalculationAfterCheckout;

/**
 * Checks that prices excl tax on order review and customer order pages are equal to specified in dataset.
 */
class AbstractAssertTaxCalculationAfterCheckoutDownloadable extends AbstractAssertTaxCalculationAfterCheckout
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that prices on order review and customer order pages are equal to specified in dataset.
     *
     * @param array $prices
     * @param InjectableFixture $product
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param OrderView $orderView
     * @return void
     */
    public function processAssert(
        array $prices,
        InjectableFixture $product,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        OrderView $orderView
    ) {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->orderView = $orderView;

        $checkoutCart->getProceedToCheckoutBlock()->proceedToCheckout();
        $checkoutOnepage->getBillingBlock()->clickContinue();
        $checkoutOnepage->getPaymentMethodsBlock()->selectPaymentMethod(['method' => 'check_money_order']);
        $checkoutOnepage->getPaymentMethodsBlock()->clickContinue();
        $actualPrices = [];
        $actualPrices = $this->getReviewPrices($actualPrices, $product);
        $actualPrices = $this->getReviewTotals($actualPrices);
        $prices = $this->preparePrices($prices);
        //Order review prices verification
        $message = 'Prices on order review should be equal to defined in dataset.';
        \PHPUnit_Framework_Assert::assertEquals($prices, $actualPrices, $message);

        $checkoutOnepage->getReviewBlock()->placeOrder();
        $checkoutOnepageSuccess->getSuccessBlock()->getGuestOrderId();
        $checkoutOnepageSuccess->getSuccessBlock()->openOrder();
        $actualPrices = [];
        $actualPrices = $this->getOrderPrices($actualPrices, $product);
        $actualPrices = $this->getOrderTotals($actualPrices);

        //Frontend order prices verification
        $message = 'Prices on order view page should be equal to defined in dataset.';
        \PHPUnit_Framework_Assert::assertEquals($prices, $actualPrices, $message);
    }
}
