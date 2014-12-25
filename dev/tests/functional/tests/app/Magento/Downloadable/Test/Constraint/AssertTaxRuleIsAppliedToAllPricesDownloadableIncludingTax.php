<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Downloadable\Test\Constraint;

/**
 * Checks that prices incl tax on category, product and cart pages are equal to specified in dataset.
 */
class AssertTaxRuleIsAppliedToAllPricesDownloadableIncludingTax
    extends AbstractAssertTaxRuleIsAppliedToAllPricesDownloadable
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Get prices on category page.
     *
     * @param $productName
     * @param array $actualPrices
     * @return array
     */
    public function getCategoryPrices($productName, $actualPrices)
    {
        $priceBlock = $this->catalogCategoryView->getListProductBlock()->getProductPriceBlock($productName);
        $actualPrices['category_price_excl_tax'] = null;
        $actualPrices['category_price_incl_tax'] = $priceBlock->getEffectivePrice();

        return $actualPrices;
    }

    /**
     * Get product view prices.
     *
     * @param array $actualPrices
     * @return array
     */
    public function getProductPagePrices($actualPrices)
    {
        $viewBlock = $this->catalogProductView->getViewBlock();
        $actualPrices['product_view_price_excl_tax'] = null;
        $actualPrices['product_view_price_incl_tax'] = $viewBlock->getPriceBlock()->getEffectivePrice();

        return $actualPrices;
    }

    /**
     * Get totals.
     *
     * @param $actualPrices
     * @return array
     */
    public function getTotals($actualPrices)
    {
        $totalsBlock = $this->checkoutCart->getTotalsBlock();
        $actualPrices['subtotal_excl_tax'] = null;
        $actualPrices['subtotal_incl_tax'] = $totalsBlock->getSubtotal();
        $actualPrices['discount'] = $totalsBlock->getDiscount();
        $actualPrices['shipping_excl_tax'] = $totalsBlock->getShippingPrice();
        $actualPrices['shipping_incl_tax'] = $totalsBlock->getShippingPriceInclTax();
        $actualPrices['tax'] = $totalsBlock->getTax();
        $actualPrices['grand_total_excl_tax'] = $totalsBlock->getGrandTotalExcludingTax();
        $actualPrices['grand_total_incl_tax'] = $totalsBlock->getGrandTotalIncludingTax();

        return $actualPrices;
    }
}
