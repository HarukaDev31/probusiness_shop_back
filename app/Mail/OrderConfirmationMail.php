<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $order;
    public $orderId;
    public $logo_header;
    public $logo_footer;

    /**
     * Create a new message instance.
     */
    public function __construct($customer, $order, $orderId, $logo_header, $logo_footer)
    {
        $this->customer = $customer;
        $this->order = $order;
        $this->orderId = $orderId;
        $this->logo_header = $logo_header;
        $this->logo_footer = $logo_footer;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Confirmación de pedido #' . $this->orderId)
            ->view('emails.order_confirmation')
            ->with([
                'customer' => $this->customer,
                'order' => $this->order,
                'logo_header' => $this->logo_header,
                'logo_footer' => $this->logo_footer,
            ]);
    }
}
