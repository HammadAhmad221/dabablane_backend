<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase;
    public $invoice;
    public $config;

    public function __construct(Purchase $purchase, Invoice $invoice, Configuration $config = null)
    {
        $this->purchase = $purchase;
        $this->invoice = $invoice;
        $this->config = $config ?? new Configuration([
            'billing_email' => 'contact@dabablane.com',
            'contact_email' => 'contact@dabablane.com',
            'contact_phone' => '+212615170064',
            'invoice_prefix' => 'DABA-INV-',
        ]);
    }

    // public function build()
    // {
    //     $email = $this->from(config('mail.from.address'), config('mail.from.name'))
    //         ->replyTo($this->config->billing_email, 'Service Facturation')
    //         ->subject('Votre facture #' . $this->invoice->invoice_number)
    //         ->markdown('emails.invoice');

    //     // Only attach PDF if it exists
    //     if (Storage::exists($this->invoice->pdf_path)) {
    //         $email->attach(Storage::path($this->invoice->pdf_path), [
    //             'as' => 'facture-' . $this->invoice->invoice_number . '.pdf',
    //             'mime' => 'application/pdf',
    //         ]);
    //     }

    //     return $email;
    // }

    public function build()
    {
        $email = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->config->billing_email, 'Service Facturation')
            ->subject('Votre facture #' . $this->invoice->invoice_number)
            ->markdown('emails.invoice');

        // Build the full storage path from public path
        // $storagePath = 'public/uploads/' . $this->invoice->pdf_path;
        $pdfFullPath = storage_path('app/public/' . $this->invoice->pdf_path);

        if (file_exists($pdfFullPath)) {
            $email->attach($pdfFullPath, [
                'as' => 'facture-' . $this->invoice->invoice_number . '.pdf',
                'mime' => 'application/pdf',
            ]);
        } else {
            \Log::warning('PDF not found for attachment: ' . $pdfFullPath);
        }


        // $storagePath = storage_path('app/public/uploads/invoices/' . $this->invoice->pdf_path);
        // $fullPath = storage_path('app/' . $storagePath);

        // // Only attach PDF if it exists
        // if (file_exists($fullPath)) {
        //     $email->attach($fullPath, [
        //         'as' => 'facture-' . $this->invoice->invoice_number . '.pdf',
        //         'mime' => 'application/pdf',
        //     ]);
        // } else {
        //     \Log::warning('PDF file not found for attachment: ' . $fullPath);
        // }

        return $email;
    }

    // public function build()
    // {
    //     $email = $this->from(config('mail.from.address'), config('mail.from.name'))
    //         ->replyTo($this->config->billing_email, 'Service Facturation')
    //         ->subject('Votre facture #' . $this->invoice->invoice_number)
    //         ->markdown('emails.invoice');

    //     // Convert public path to storage path for attachment
    //     $storagePath = 'public/' . str_replace('storage/', '', $this->invoice->pdf_path);

    //     // Only attach PDF if it exists
    //     if (Storage::exists($storagePath)) {
    //         $email->attach(Storage::path($storagePath), [
    //             'as' => 'facture-' . $this->invoice->invoice_number . '.pdf',
    //             'mime' => 'application/pdf',
    //         ]);
    //     } else {
    //         \Log::warning('PDF file not found for attachment: ' . $storagePath);
    //     }

    //     return $email;
    // }

    // public function build()
    // {
    //     return $this->from($this->config->billing_email, env('APP_NAME', 'DabaBlane'))
    //         ->subject('Votre facture #' . $this->invoice->invoice_number)
    //         ->markdown('emails.invoice')
    //         ->attach(Storage::path($this->invoice->pdf_path), [
    //             'as' => 'facture-' . $this->invoice->invoice_number . '.pdf',
    //             'mime' => 'application/pdf',
    //         ]);
    // }
}