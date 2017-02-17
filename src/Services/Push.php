<?php

namespace salyangoz\pazaryeriparasut\Services;

use salyangoz\pazaryeriparasut\Models\Customer;
use salyangoz\pazaryeriparasut\Models\Order;
use salyangoz\pazaryeriparasut\Models\Product;
use salyangoz\pazaryeriparasut\ParasutAdapter;
use Illuminate\Support\Facades\Log;

class Push extends ParasutAdapter
{

    public function __construct(array $config)
    {

        parent::__construct($config);

    }

    public function start()
    {
        $waitingOrders = Order::waiting()->get();

        foreach ($waitingOrders as $order)
        {
            $this->pushOrder($order);
            usleep(300000);
        }
    }

    private function pushOrder(Order $order)
    {

        if($this->pushCustomer($order->customer)>0)
        {
            $this->pushInvoice($order);
        }

    }

    private function pushProduct(Product $product)
    {
        if(!$product->parasut_id)
        {

            $response = $this->parasut->make('product')->create(
                ['name'=>$product->name]
            );


            if(isset($response['product']['id']))
            {
                $product->parasut_id = $response['product']['id'];
            }
        }

        $product->save();

        return $product->parasut_id;
    }

    private function pushInvoice(Order $order)
    {

        $details            =  [
            'item_type'     => 'invoice',
            'description'   => substr($order->description, 0, 255),
            'issue_date'    => date('d-m-Y'),
            'category_id'   => config("pazaryeri-parasut.parasut_category_id"),
            'payment_status'=> 'paid',
            'contact_id'    => $order->customer->parasut_id
        ];

        foreach ($order->orderProduct()->get() as $item)
        {
            $details['details_attributes'][] =
                [
                    'product_id'        => $this->pushProduct($item->product),
                    'quantity'          => $item->quantity,
                    'discount_value'    => 0,
                    'discount_type'     => 'amount',
                    'unit_price'        => ($item->price) / (1 + 0.18),
                    'vat_rate'          => 0.18 * 100,
                ];
        }

        $invoice = $this->parasut->make('sale')->create($details);

        if($invoiceID = $invoice['sales_invoice']['id'])
        {
			try{
				$status = $this->parasut->make('sale')->paid($invoiceID,
					["amount"    =>  $order->amount,
					 "date"      =>  date('Y-m-d'),
					 "account_id"=>  Config('pazaryeri-parasut.parasut_account_id')]
				);

				if($status['status'] == "success")
				{
					$order->parasut_id = $invoiceID;
					$order->save();

					return $order->id;
				}
				else
				{
					$this->parasut->make('sale')->delete($invoiceID);
				}
			}catch(\Exception $e)
			{
				echo $e->getMessage();	
				$this->parasut->make('sale')->delete($invoiceID);
			}
        }

        return false;
    }

    private function pushCustomer(Customer $customer)
    {

        $details =  [
            'contact_type'  =>  $customer->type == "Customer" ? "person" : "company",
            'name' => $customer->name,
            'email' => $customer->email,
            'tax_number' => $customer->tax_number ? $customer->tax_number : $customer->tc,
            'tax_office' => $customer->tax_office,
            'address_attributes' => [
                'address' => $customer->invoice_address,
                'phone' => $customer->phone,
            ],
            'city'     =>$customer->city,
            'district' =>$customer->district
        ];

        if(!$customer->parasut_id)
        {

            $contact = $this->parasut->make('contact')->create(
                $details
            );

        }
        else
        {
            $contact = $this->parasut->make('contact')->update($customer->parasut_id,
                $details
            );
        }

        $customer->parasut_id = $contact['contact']['id'];
        $customer->save();

        return $customer->parasut_id;
    }
}
