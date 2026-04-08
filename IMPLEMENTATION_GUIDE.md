# QUICK START GUIDE - Portal Status, Freeze & Reminders

## ✅ What Was Implemented

### 1. PORTAL STATUS FIELD (Database)
- New column: `portal_status` in `intern_accounts` table
- Values: `pending_activation`, `active`, `frozen`
- Default: `pending_activation` for new registrations

### 2. PORTAL FREEZE SYSTEM (Automatic)
- **Automatically freezes** interns with overdue unpaid invoices
- Runs **2x daily** (9 AM & 3 PM)
- Blocks: Task submission, material access, certificate requests
- **Automatically unfreezes** when payment is recorded

### 3. PAYMENT REMINDERS (Email)
- Sent **4 days before** invoice due date
- Sends to **both manager & intern**
- Professional HTML emails with invoice details
- Warns about portal freeze consequences
- Runs **daily at 9 AM**

---

## 🚀 Quick Commands

### Test Portal Freeze
```bash
php artisan portal:freeze-overdue
```
*Lists how many interns were frozen*

### Send Payment Reminders
```bash
php artisan invoice:send-reminders
```
*Sends to all invoices due in 4 days*

### Custom Reminder Days
```bash
php artisan invoice:send-reminders --days=3
```
*Send reminders for invoices due in 3 days*

---

## 📂 New Files Created

**Migrations:**
- `database/migrations/2026_04_08_000001_add_portal_status_to_intern_accounts.php`

**Middleware:**
- `app/Http/Middleware/CheckInternPortalFreeze.php`

**Services:**
- `app/Services/PortalFreezeService.php`
- `app/Services/PaymentReminderService.php`

**Commands:**
- `app/Console/Commands/EnforcePortalFreeze.php`

---

## 🔧 Modified Files

1. `app/Models/InternAccount.php` - Added portal_status & helpers
2. `app/Http/Controllers/InternPublicRegistrationController.php` - Set pending_activation
3. `app/Http/Controllers/manager_controllers/InvoiceController.php` - Unfreeze on payment
4. `app/Console/Commands/SendInvoiceReminders.php` - Enhanced reminders
5. `app/Console/Kernel.php` - Added scheduler

---

## 📋 How to Use in Code

### Check if Frozen
```php
$intern = InternAccount::find($id);

if ($intern->isFrozen()) {
    // Portal is frozen
}
```

### Freeze/Unfreeze
```php
$intern->freeze();    // Freeze portal
$intern->unfreeze();  // Unfreeze portal
$intern->activate();  // Activate from pending
```

### Query Frozen Interns
```php
$frozen = InternAccount::frozen()->get();
$active = InternAccount::active()->get();
$pending = InternAccount::pendingActivation()->get();
```

### Get Freeze Status
```php
$service = new PortalFreezeService();
$status = $service->getInternStatus($internId);
// Returns: overdue_amount, is_frozen, invoice_details, etc.
```

### Send Manual Reminder
```php
$reminderService = new PaymentReminderService();
$reminderService->sendInvoiceReminder($invoiceId);
```

---

## 📊 Database Migration

Run this to add the new field:
```bash
php artisan migrate
```

This will:
- Add `portal_status` column
- Set default to 'pending_activation'
- Migrate existing rows to 'active' (backward compatible)
- Create index for performance

---

## ⏰ Scheduler Setup

Add to your crontab (production):
```bash
* * * * * cd /path/to/app && php artisan schedule:run
```

This will automatically run:
- **9 AM**: Check & freeze overdue portals + send payment reminders
- **3 PM**: Check & freeze overdue portals again

---

## 🧪 Testing Steps

1. **Create test invoice** due tomorrow
2. **Run**: `php artisan invoice:send-reminders`
   → Should see reminder email sent
3. **Wait for morning** or run: `php artisan portal:freeze-overdue`
   → If due date passed, portal should freeze
4. **Record payment** in manager panel
   → Portal should auto-unfreeze
5. **Check intern dashboard**
   → Should be able to submit tasks again

---

## 📧 Email Recipients

### Manager Receives:
- Invoice details (amount, due date, intern name)
- Days until due
- ⚠️ Warning about portal freeze
- Link/details to record payment

### Intern Receives:
- Invoice details (amount, due date)
- Days until due
- ⚠️ Warning that portal will freeze
- List of blocked features if frozen
- Instructions to contact manager

---

## 🔒 Security Notes

- All freeze/unfreeze actions are logged
- Email sending uses Laravel Mail (configurable)
- Service layer isolates business logic
- Middleware prevents API/view access
- Transaction-safe payment recording
- Error handling prevents crashes

---

## 📝 Important Notes

1. **Existing Interns**: Set to `active` automatically (no disruption)
2. **New Registrations**: Start as `pending_activation`
3. **Portal Freeze**: Automatic, no manual intervention needed
4. **Unfreeze**: Automatic when payment received + recorded
5. **Reminders**: Sent regardless of freeze status
6. **Email Config**: Uses Laravel Mail configuration

---

## 🐛 Troubleshooting

### Reminders not sending?
- Check: `php artisan tinker` → `Mail::raw(...)`
- Verify email config in `.env`
- Check logs: `storage/logs/laravel.log`

### Portal not freezing?
- Run manually: `php artisan portal:freeze-overdue`
- Check: Invoice has `due_date < now()` and `remaining_amount > 0`
- Verify: Intern email matches invoice email

### Column doesn't exist?
- Haven't run migration: `php artisan migrate`
- Or need to rollback/remigrate: `php artisan migrate:refresh --step=1000`

---

## Next Phase

Recommend implementing:
1. **Escalation Logic** - Auto-escalate if manager doesn't update status in 8 hours
2. **Task Consolidation** - Merge 3 task models into 1
3. **Supervisor Separation** - Create separate SupervisorAccount
4. **Dashboard Indicators** - Show freeze status in UI

---

Generated: April 8, 2026
Implementation: Complete ✅
