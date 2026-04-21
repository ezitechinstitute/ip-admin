@component('mail::message')
# Withdrawal Request Rejected ❌

Hello,

Unfortunately, your withdrawal request has been rejected by the admin.

**Withdrawal Details:**
- **Amount:** PKR {{ number_format($amount) }}
- **Bank:** {{ $bank }}
- **Date:** {{ $date }}

**Reason for Rejection:**
{{ $reason }}

If you believe this was rejected by mistake or have any questions, please contact the admin for further assistance.

You can submit a new withdrawal request if needed.

Thanks,  
{{ config('app.name') }}
@endcomponent
