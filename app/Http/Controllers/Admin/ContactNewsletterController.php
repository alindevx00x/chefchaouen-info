<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\Newsletter;
use App\Services\DynamicMailService;
use Illuminate\Http\Request;

class ContactNewsletterController extends Controller
{
    public function contacts(Request $request)
    {
        $query = ContactSubmission::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $contacts = $query->latest()->paginate(20)->withQueryString();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function replyContact(Request $request, ContactSubmission $contact, DynamicMailService $mailService)
    {
        $data = $request->validate([
            'admin_reply' => 'required|string|min:3|max:4000',
        ]);

        $subject = 'Re: ' . ($contact->subject ?: 'Your inquiry on Chefchaouen.info');
        $html = '<div style="font-family:sans-serif;line-height:1.6;color:#0f172a;">'
            . '<p>Hello ' . e($contact->name) . ',</p>'
            . '<p>' . nl2br(e($data['admin_reply'])) . '</p>'
            . '<hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">'
            . '<p style="font-size:12px;color:#64748b;">Chefchaouen.info | Deeper Than Blue | contact@chefchaouen.info</p>'
            . '</div>';

        $mailResult = $mailService->sendEmail($contact->email, $contact->name, $subject, $html);

        $contact->update([
            'admin_reply' => $data['admin_reply'],
            'replied_at' => now(),
            'status' => 'replied',
        ]);

        return back()->with(
            $mailResult['success'] ? 'success' : 'warning',
            'Reply recorded. ' . $mailResult['message']
        );
    }

    public function destroyContact(ContactSubmission $contact)
    {
        $contact->delete();
        return back()->with('success', 'Contact submission deleted.');
    }

    public function exportContactsCsv()
    {
        $rows = ContactSubmission::latest()->get();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="chefchaouen-contacts-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name', 'Email', 'Inquiry Type', 'Subject', 'Message', 'Status', 'Admin Reply', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->name,
                    $row->email,
                    $row->inquiry_type,
                    $row->subject,
                    $row->message,
                    $row->status,
                    $row->admin_reply,
                    $row->created_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function newsletters()
    {
        $subscribers = Newsletter::latest()->paginate(25);
        return view('admin.newsletters.index', compact('subscribers'));
    }

    public function destroyNewsletter(Newsletter $newsletter)
    {
        $newsletter->delete();
        return back()->with('success', 'Subscriber removed.');
    }

    public function exportNewslettersCsv()
    {
        $rows = Newsletter::latest()->get();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="chefchaouen-newsletters-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Email', 'Name', 'Status', 'Source', 'Subscribed At']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->email,
                    $row->name,
                    $row->status,
                    $row->source,
                    $row->created_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
