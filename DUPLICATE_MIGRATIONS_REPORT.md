# 🔴 DUPLICATE MIGRATIONS REPORT - March 27, 2026

## Summary
**Total Duplicates Found: 13 FILES** (9 duplicate table creations + 4 problematic additions)

---

## 📋 ALL DUPLICATES BY TABLE

### 1. **certificate_templates** ⚠️ CRITICAL
| File | Date | Primary Key | Warning |
|------|------|------------|---------|
| ❌ **DELETE** | 2026_03_15_142115 | `id` | Old schema: type, template_path |
| ✅ **KEEP** | 2026_03_16_005851 | `id` | New schema: title, content, manager_id |

**Note:** 2026_03_27_061224 patches the old one - can be deleted after removing 2026_03_15_142115

---

### 2. **supervisor_leaves**
| File | Date | Primary Key | Note |
|------|------|------------|------|
| ❌ **DELETE** | 2026_02_10_075912 | `id('leave_id')` | Old syntax |
| ✅ **KEEP** | 2026_03_11_101626 | `bigIncrements('leave_id')` | Proper syntax |

**Schema:** Both identical (supervisor_id, name, email, from_date, to_date, reason, days, leave_status)

---

### 3. **employee_leaves**
| File | Date | Primary Key | Note |
|------|------|------------|------|
| ❌ **DELETE** | 2026_02_10_080316 | `id('leave_id')` | Old syntax |
| ✅ **KEEP** | 2026_03_11_101626 | `bigIncrements('leave_id')` | Proper syntax |

**Schema:** Identical to supervisor_leaves

---

### 4. **admin_settings**
| File | Date | Syntax | Note |
|------|------|--------|------|
| ❌ **DELETE** | 2026_02_14_070251 | `$table->id()` | Older style |
| ✅ **KEEP** | 2026_03_11_101626 | `bigIncrements('id')` | Standard approach |

**Schema:** IDENTICAL (system_logo, smtp_host, SMTP config, notifications, pagination, interview_timeout, internship_duration, JSON fields)

---

### 5. **password_otp_resets**
| File | Date | Syntax | Note |
|------|------|--------|------|
| ❌ **DELETE** | 2026_02_15_151457 | `$table->id()` | Old pattern |
| ✅ **KEEP** | 2026_03_11_101626 | `bigIncrements('id')` | Proper pattern |

**Schema:** IDENTICAL (id, email, otp, created_at)

---

### 6. **knowledge_bases**
| File | Date | Syntax | Note |
|------|------|--------|------|
| ❌ **DELETE** | 2026_02_12_145209 | `$table->id()` | Old pattern |
| ✅ **KEEP** | 2026_03_11_101626 | `bigIncrements('id')` | Proper pattern |

**Schema:** IDENTICAL

---

### 7. **manager_roles**
| File | Date | FK Inline | Note |
|------|------|----------|------|
| ❌ **DELETE** | 2026_02_26_190048 | ✓ YES | Foreign key defined inline ⚠️ |
| ✅ **KEEP** | 2026_03_11_101626 | ✗ NO | Better separation |
| ✅ **KEEP** | 2026_03_11_101629 | - | FK added separately |

**Reason:** March migration uses safer pattern - FK constraints added in separate migration

---

### 8. **offer_letter_templates**
| File | Date | FK Inline | Note |
|------|------|----------|------|
| ❌ **DELETE** | 2026_02_28_044058 | ✓ YES | Inline foreign key ⚠️ |
| ✅ **KEEP** | 2026_03_11_101626 | ✗ NO | Clean separation |
| ✅ **KEEP** | 2026_03_11_101629 | - | FK added separately |

**Reason:** Same as manager_roles - safer pattern

---

### 9. **transactions** 🔴 CRITICAL - SCHEMA CHANGE
| File | Date | Schema | Status |
|------|------|--------|--------|
| ❌ **DELETE** | 2026_03_11_101626 | Old: amount, emails, amounts | Outdated |
| ✅ **KEEP** | 2026_03_13_150346 | New: invoice_id, type, method, notes, payment_date | Complete redesign |

**Patches to DELETE (redundant if using new schema):**
- ❌ 2026_03_13_182036_add_invoice_id_to_transactions_table.php
- ❌ 2026_03_13_183002_add_inv_id_to_transactions_table.php
- ❌ 2026_03_13_191045_add_payment_date_to_transactions_table.php

---

### 10. **invoices approval_status** ⚠️
| File | Date | Default Value | Note |
|------|------|----------------|------|
| ✅ **KEEP** | 2026_03_13_153834 | 'pending' | First version (correct) |
| ❌ **DELETE** | 2026_03_13_183425 | 'approved' | Duplicate (wrong default) |

**Issue:** Same column added twice with different defaults

---

## 🗑️ DELETE THESE 13 FILES

1. `2026_03_15_142115_create_certificate_templates_table.php`
2. `2026_02_10_075912_create_supervisor_leaves_table.php`
3. `2026_02_10_080316_create_employee_leaves_table.php`
4. `2026_02_14_070251_create_admin_settings_table.php`
5. `2026_02_26_190048_create_manager_roles_table.php`
6. `2026_02_28_044058_create_offer_letter_templates_table.php`
7. `2026_02_15_151457_create_password_otp_resets_table.php`
8. `2026_02_12_145209_create_knowledge_bases_table.php`
9. `2026_03_11_101626_create_transactions_table.php` ← OLD SCHEMA
10. `2026_03_13_182036_add_invoice_id_to_transactions_table.php` ← REDUNDANT
11. `2026_03_13_183002_add_inv_id_to_transactions_table.php` ← REDUNDANT
12. `2026_03_13_191045_add_payment_date_to_transactions_table.php` ← REDUNDANT
13. `2026_03_13_183425_add_approval_status_to_invoices_table.php` ← DUPLICATE

---

## ✅ KEEP THESE PATTERNS

### Core March 11 Rebuild (All)
- `2026_03_11_101626_create_*.php` (All table creations)
- `2026_03_11_101629_add_foreign_keys_*.php` (All FK additions)

### Latest Version Migrations
- `2026_03_13_150346_create_transactions_table.php` (NEW schema)
- `2026_03_13_153834_add_approval_status_to_invoices_table.php` (First/correct version)

### Certificate Patch
- `2026_03_27_061224_add_missing_columns_to_certificate_templates_table.php` (Can stay for safety)

### Other Modifications
- All other alter/modify migrations dated after March 11

---

## 🚨 WHY THIS MATTERS FOR LIVE DEPLOYMENT

### Problem 1: Migration Conflicts
When deploying, if migration files with the same timestamp (2026_03_11_101626) are executed:
- Second migration tries to create table that already exists
- Laravel checks `if (!Schema::hasTable())` and skips it
- But if one migration fails partway, the table might be partially created
- Next migration on same table will fail

### Problem 2: Old Schema Gets Used
If older migration (Feb) runs before newer one (March):
- Database gets old schema (e.g., `type, template_path` instead of `title, content`)
- Application expects new schema
- Fatal column not found errors

### Problem 3: Duplicate Patches
Patches for transactions trying to add columns that don't exist:
- If old transactions table deleted, patches fail
- Causes entire migration to rollback

---

## ✅ SOLUTION: DELETE THE 13 FILES LISTED ABOVE

After deletion:
1. Database stays clean (no conflicting schemas)
2. Only latest/best versions run
3. Foreign keys properly separated
4. Live deployment won't fail
5. Consistent schema across environments
