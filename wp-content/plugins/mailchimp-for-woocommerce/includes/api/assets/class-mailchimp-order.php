<?php

/**
 * Created by Vextras.
 *
 * Name: Ryan Hungate
 * Email: ryan@mailchimp.com
 * Date: 3/8/16
 * Time: 2:16 PM
 */
class MailChimp_WooCommerce_Order
{
    protected $id = null;
    protected $customer = null;
    protected $campaign_id = null;
    protected $financial_status = null;
    protected $fulfillment_status = null;
    protected $currency_code = null;
    protected $order_total = null;
    protected $tax_total = null;
    protected $shipping_total = null;
    protected $updated_at_foreign = null;
    protected $processed_at_foreign = null;
    protected $cancelled_at_foreign = null;
    protected $shipping_address = null;
    protected $billing_address = null;
    protected $lines = array();

    /**
     * @return array
     */
    public function getValidation()
    {
        return array(
            'id' => 'required|string',
            'customer' => 'required',
            'campaign_id' => 'string',
            'financial_status' => 'string',
            'fulfillment_status' => 'string',
            'currency_code' => 'required|currency_code',
            'order_total' => 'required|numeric',
            'tax_total' => 'numeric',
            'processed_at_foreign' => 'date',
            'updated_at_foreign' => 'date',
            'cancelled_at_foreign' => 'date',
            'lines' => 'required|array',
        );
    }

    /**
     * @param $id
     * @return MailChimp_WooCommerce_Order
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param MailChimp_WooCommerce_Customer $customer
     * @return MailChimp_WooCommerce_Order
     */
    public function setCustomer(MailChimp_WooCommerce_Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return null|MailChimp_WooCommerce_Customer
     */
    public function getCustomer()
    {
        if (empty($this->customer)) {
            $this->customer = new MailChimp_WooCommerce_Customer();
        }
        return $this->customer;
    }

    /**
     * @param MailChimp_WooCommerce_LineItem $item
     * @return $this
     */
    public function addItem(MailChimp_WooCommerce_LineItem $item)
    {
        $this->lines[] = $item;
        return $this;
    }

    /**
     * @return array
     */
    public function items()
    {
        return $this->lines;
    }

    /**
     * @return null
     */
    public function getCampaignId()
    {
        return $this->campaign_id;
    }

    /**
     * @param null $campaign_id
     * @return MailChimp_WooCommerce_Order
     */
    public function setCampaignId($campaign_id)
    {
        $this->campaign_id = $campaign_id;

        return $this;
    }

    /**
     * @return null
     */
    public function getFinancialStatus()
    {
        return $this->financial_status;
    }

    /**
     * @param null $financial_status
     * @return Order
     */
    public function setFinancialStatus($financial_status)
    {
        $this->financial_status = $financial_status;

        return $this;
    }

    /**
     * @return null
     */
    public function getFulfillmentStatus()
    {
        return $this->fulfillment_status;
    }

    /**
     * @param null $fulfillment_status
     * @return MailChimp_WooCommerce_Order
     */
    public function setFulfillmentStatus($fulfillment_status)
    {
        $this->fulfillment_status = $fulfillment_status;

        return $this;
    }

    /**
     * @return null
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param null $currency_code
     * @return MailChimp_WooCommerce_Order
     */
    public function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderTotal()
    {
        return $this->order_total;
    }

    /**
     * @param mixed $order_total
     * @return MailChimp_WooCommerce_Order
     */
    public function setOrderTotal($order_total)
    {
        $this->order_total = $order_total;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaxTotal()
    {
        return $this->tax_total;
    }

    /**
     * @param mixed $tax_total
     * @return MailChimp_WooCommerce_Order
     */
    public function setTaxTotal($tax_total)
    {
        $this->tax_total = $tax_total;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingTotal()
    {
        return $this->shipping_total;
    }

    /**
     * @param mixed $shipping_total
     * @return MailChimp_WooCommerce_Order
     */
    public function setShippingTotal($shipping_total)
    {
        $this->shipping_total = $shipping_total;

        return $this;
    }

    /**
     * @param \DateTime $time
     * @return $this
     */
    public function setProcessedAt(\DateTime $time)
    {
        $this->processed_at_foreign = $time->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null
     */
    public function getProcessedAt()
    {
        return $this->processed_at_foreign;
    }

    /**
     * @param \DateTime $time
     * @return $this
     */
    public function setCancelledAt(\DateTime $time)
    {
        $this->cancelled_at_foreign = $time->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null
     */
    public function getCancelledAt()
    {
        return $this->cancelled_at_foreign;
    }

    /**
     * @param \DateTime $time
     * @return $this
     */
    public function setUpdatedAt(\DateTime $time)
    {
        $this->updated_at_foreign = $time->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null
     */
    public function getUpdatedAt()
    {
        return $this->updated_at_foreign;
    }

    /**
     * @param MailChimp_WooCommerce_Address $address
     * @return $this
     */
    public function setShippingAddress(MailChimp_WooCommerce_Address $address)
    {
        $this->shipping_address = $address;

        return $this;
    }

    /**
     * @return MailChimp_WooCommerce_Address
     */
    public function getShippingAddress()
    {
        if (empty($this->shipping_address)) {
            $this->shipping_address = new MailChimp_WooCommerce_Address('shipping');
        }
        return $this->shipping_address;
    }

    /**
     * @param MailChimp_WooCommerce_Address $address
     * @return $this
     */
    public function setBillingAddress(MailChimp_WooCommerce_Address $address)
    {
        $this->billing_address = $address;

        return $this;
    }

    /**
     * @return MailChimp_WooCommerce_Address
     */
    public function getBillingAddress()
    {
        if (empty($this->billing_address)) {
            $this->billing_address = new MailChimp_WooCommerce_Address('billing');
        }
        return $this->billing_address;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return mailchimp_array_remove_empty(array(
            'id' => (string) $this->getId(),
            'customer' => $this->getCustomer()->toArray(),
            'campaign_id' => (string) $this->getCampaignId(),
            'financial_status' => (string) $this->getFinancialStatus(),
            'fulfillment_status' => (string) $this->getFulfillmentStatus(),
            'currency_code' => (string) $this->getCurrencyCode(),
            'order_total' => floatval($this->getOrderTotal()),
            'tax_total' => floatval($this->getTaxTotal()),
            'shipping_total' => floatval($this->getShippingTotal()),
            'processed_at_foreign' => (string) $this->getProcessedAt(),
            'cancelled_at_foreign' => (string) $this->getCancelledAt(),
            'updated_at_foreign' => (string) $this->getUpdatedAt(),
            'shipping_address' => $this->getShippingAddress()->toArray(),
            'billing_address' => $this->getBillingAddress()->toArray(),
            'lines' => array_map(function ($item) {
                /** @var MailChimp_WooCommerce_LineItem $item */
                return $item->toArray();
            }, $this->items()),
        ));
    }

    /**
     * @param array $data
     * @return MailChimp_WooCommerce_Order
     */
    public function fromArray(array $data)
    {
        $singles = array(
            'id', 'campaign_id', 'financial_status', 'fulfillment_status',
            'currency_code', 'order_total', 'tax_total', 'processed_at_foreign',
            'cancelled_at_foreign', 'updated_at_foreign'
        );

        foreach ($singles as $key) {
            if (array_key_exists($key, $data)) {
                $this->$key = $data[$key];
            }
        }

        if (array_key_exists('shipping_address', $data) && is_array($data['shipping_address'])) {
            $this->shipping_address = (new MailChimp_WooCommerce_Address())->fromArray($data['shipping_address']);
        }

        if (array_key_exists('billing_address', $data) && is_array($data['billing_address'])) {
            $this->billing_address = (new MailChimp_WooCommerce_Address())->fromArray($data['billing_address']);
        }

        if (array_key_exists('lines', $data) && is_array($data['lines'])) {
            $this->lines = array();
            foreach ($data['lines'] as $line_item) {
                $this->lines[] = (new MailChimp_WooCommerce_LineItem())->fromArray($line_item);
            }
        }

        return $this;
    }
}
