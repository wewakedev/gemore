<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Models\Contact;
use App\Mail\ContactFormSubmitted;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['category', 'activeVariants'])
            ->active()
            ->featured()
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->featured()
            ->orderBy('sort_order')
            ->get();

        return view('home', compact('featuredProducts', 'categories'));
    }

    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
        ]);

        try {
            // Save contact data to database
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'new',
            ]);

            // Send confirmation email to the user
            Mail::to($contact->email)->send(new ContactFormSubmitted($contact));

            // Log the successful submission
            Log::info('Contact form submission saved and email sent', [
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you! We\'ve received your message and sent you a confirmation email. We\'ll get back to you soon.',
            ]);
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message. Please try again later.',
            ], 500);
        }
    }

    public function orderConfirmation(Request $request)
    {
        $request->validate([
            'orderNumber' => 'required|string',
            'customerName' => 'required|string',
            'customerEmail' => 'required|email',
            'items' => 'required|array',
        ]);

        try {
            // Here you would send the order confirmation email
            // For now, we'll just log it and return success
            Log::info('Order confirmation', $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Order confirmation sent successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Order confirmation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send order confirmation',
            ], 500);
        }
    }
} 