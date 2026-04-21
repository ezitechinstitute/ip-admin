@component('mail::message')
# Withdrawal Request Approved ✅

Hello,

Great news! Your withdrawal request has been approved by the admin.

**Withdrawal Details:**
- **Amount:** PKR {{ number_format($amount) }}
- **Bank:** {{ $bank }}
- **Account Number:** {{ $accountNumber }}
- **Account Holder:** {{ $accountHolder }}
- **Date:** {{ $date }}
- **Status:** Approved

The amount will be transferred to your account shortly. You will receive another notification once the payment has been processed and credited to your account.

Thanks,  
{{ config('app.name') }}
@endcomponent
