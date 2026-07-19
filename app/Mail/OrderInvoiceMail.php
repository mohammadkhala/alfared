<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The invoice, sent once the sale is actually settled — on delivery for cash
 * orders, or on payment for card orders. Deliberately not sent when the order
 * is placed: a COD order isn't a completed sale yet, and invoicing it early
 * means issuing a credit note whenever one is cancelled or returned.
 *
 * The email carries a readable summary and links to the printable invoice
 * rather than attaching a PDF: there's no PDF library here, and the ones that
 * run on shared hosting mangle Arabic letter shaping badly enough to make the
 * document worse than no document.
 */
class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $lang = 'ar') {}

    public function envelope(): Envelope
    {
        $subject = match ($this->lang) {
            'he'    => "חשבונית להזמנה #{$this->order->order_number} — אבנא אלפריד",
            'en'    => "Invoice for order #{$this->order->order_number} — Alfared",
            default => "فاتورة طلبك #{$this->order->order_number} — أبناء الفريد",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
            with: ['order' => $this->order, 'lang' => $this->lang],
        );
    }
}
