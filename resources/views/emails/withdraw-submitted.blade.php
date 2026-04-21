@component('mail::message')
# Withdrawal Request Submitted

Hello Admin,

A new withdrawal request has been submitted and requires your review.

**Request Details:**
- **Amount:** PKR {{ number_format($amount) }}
- **Bank:** {{ $bank }}
- **Account Holder:** {{ $accountHolder }}
- **Submitted by:** {{ $managerName }}
- **Description:** {{ $description }}

@component('mail::button', ['url' => route('admin.withdraw')])
Review Request
@endcomponent

Please review and approve or reject this request at your earliest convenience.

Thanks,  
{{ config('app.name') }}
@endcomponent
