# Task and Leave System Consolidation Guide

## Overview

This document explains the consolidation of three overlapping task systems and three parallel leave systems into unified service layers. This consolidation provides:

- **Single interface** for querying across multiple task/leave types
- **Backward compatibility** - existing models and tables unchanged
- **Gradual migration path** - no database changes required initially
- **Reduced code duplication** - controllers can use services instead of handling multiple models

## Task System Consolidation

### Previous State (3 Systems)
```
Task                 InternTask               ProjectTask
├── tasks table       ├── intern_tasks table   ├── project_tasks table
├── supervisor_id     ├── assigned_by (int)    ├── assigned_by (int)
├── intern_id         ├── eti_id (varchar)     ├── eti_id (varchar)
└── project_id        └── project_id           └── project_id
```

### New Approach: TaskManagementService

**Location:** `app/Services/TaskManagementService.php`

#### Usage Examples:

```php
// In controller
use App\Services\TaskManagementService;

// Get all tasks for an intern (across all 3 systems)
$service = new TaskManagementService();
$tasks = $service->getInternTasks($internId, [
    'status' => 'pending',
    'project_id' => 5
]);

// Get tasks assigned by a manager
$managerTasks = $service->getManagerTasks($managerId, [
    'status' => 'submitted'
]);

// Get overdue tasks
$overdue = $service->getOverdueTasks($days = 0); // 0 = due today or past

// Get statistics
$stats = $service->getTaskStatistics($managerId);
// Returns: ['general_tasks' => 5, 'intern_tasks' => 8, 'project_tasks' => 3, 'total' => 16]

// Update task status (abstracted across types)
$service->updateTaskStatus($taskId, 'approved', 'general');
$service->updateTaskStatus($taskId, 'submitted', 'intern');
$service->updateTaskStatus($taskId, 'approved', 'project');
```

#### Format Returned:

All tasks are returned in a unified format:
```php
[
    'id' => 123,
    'task_type' => 'general|intern|project',
    'title' => 'Task Title',
    'description' => 'Task description',
    'status' => 'pending|submitted|approved|rejected',
    'points' => 10,
    'obtained_points' => 8,
    'deadline' => Carbon date,
    'supervisor_remarks' => 'Review notes',
    'submission_notes' => 'Student notes',
    'grade' => 85,
    'submitted_at' => Carbon datetime,
    'reviewed_at' => Carbon datetime,
]
```

### Enhanced Model Scopes & Methods

**Task.php now includes:**
```php
// Scopes
->pending()                    // status = pending
->submitted()                  // status = submitted
->approved()                   // status = approved
->rejected()                   // status = rejected
->overdue()                    // deadline < now AND status in [pending, submitted]
->forManager($mgr_id)          // supervisor_id = $mgr_id
->forIntern($int_id)           // intern_id = $int_id
->forProject($proj_id)         // project_id = $proj_id
->expired()                    // deadline < now AND status = pending

// Helper Methods
$task->isPending()            // bool
$task->isSubmitted()          // bool
$task->isApproved()           // bool
$task->isRejected()           // bool
$task->isOverdue()            // bool
$task->isExpired()            // bool

$task->approve($grade, $remarks)    // Update status + grade review
$task->reject($remarks)             // Update status + remarks
$task->submit($notes)               // Update status + submission notes

$task->getDaysUntilDeadline()       // int (0 if overdue)
```

**InternTask.php now includes:**
```php
// Similar scopes + methods as Task.php
->forIntern($eti_id)
->approve($points, $remarks)
->reject($remarks)
->submit($notes, $screenshot, $liveUrl, $gitUrl)
->getQualityScore()           // Calculated from obtained/total points
```

**ProjectTask.php now includes:**
```php
// Similar scopes + methods
->forProject($project_id)
->approve($marks, $review)
->reject($review)
->submit($screenshot, $liveUrl, $gitUrl, $description)
->getProgressPercentage()     // (obtained_marks / total_marks) * 100
```

---

## Leave System Consolidation

### Previous State (3 Systems)
```
Leave                    EmployeeLeave              SupervisorLeave
├── intern_leaves table   ├── employee_leaves table  ├── supervisor_leaves table
├── eti_id                ├── employee_id            ├── supervisor_id
└── ...                   └── ...                    └── ...
```

### New Approach: LeaveManagementService

**Location:** `app/Services/LeaveManagementService.php`

#### Usage Examples:

```php
// In controller
use App\Services\LeaveManagementService;

$service = new LeaveManagementService();

// Get leaves for specific roles
$internLeaves = $service->getInternLeaves($internId, [
    'status' => 'pending',
    'from_date' => '2026-04-01',
    'to_date' => '2026-04-30'
]);

$supervLeaves = $service->getSupervisorLeaves($supervisorId, [
    'status' => 'approved'
]);

$empLeaves = $service->getEmployeeLeaves($employeeId);

// Get all pending leaves (across all types)
$allPending = $service->getPendingLeaves(); // Returns collection

// Get pending leaves of specific type only
$pendingInterns = $service->getPendingLeaves('intern');
$pendingSupervisors = $service->getPendingLeaves('supervisor');

// Get currently active leaves (on leave today)
$activeLeaves = $service->getActiveLeaves();
$activeSupervisors = $service->getActiveLeaves('supervisor');

// Get statistics
$stats = $service->getLeaveStatistics();
// Returns: [
//     'pending' => 15,
//     'approved' => 8,
//     'rejected' => 2,
//     'total' => 25,
//     'intern_pending' => 10,
//     'intern_approved' => 5,
//     'supervisor_pending' => 5,
//     'supervisor_approved' => 3,
//     ...
// ]

// Approve/reject leaves
$service->approveLeave($leaveId, 'intern');
$service->rejectLeave($leaveId, 'supervisor', 'Rejection reason');
```

#### Format Returned:

All leaves are returned in unified format:
```php
[
    'id' => 123,
    'leave_type' => 'intern|supervisor|employee',
    'requestor_name' => 'John Doe',
    'requestor_email' => 'john@example.com',
    'from_date' => Carbon date,
    'to_date' => Carbon date,
    'days' => 5,
    'reason' => 'Medical emergency',
    'status' => 'pending|approved|rejected',
    'created_at' => Carbon datetime,
    'updated_at' => Carbon datetime,
]
```

### Enhanced Model Scopes & Methods

**Leave.php (Intern Leaves) now includes:**
```php
// Scopes
->pending()                    // leave_status = pending
->approved()                   // leave_status = approved
->rejected()                   // leave_status = rejected
->active()                     // approved AND from_date <= now <= to_date
->upcoming()                   // approved AND from_date > now
->intern($int_id)              // eti_id matches int_id

// Helper Methods
$leave->isPending()            // bool
$leave->isApproved()           // bool
$leave->isRejected()           // bool
$leave->isActive()             // bool

$leave->approve()              // Update status
$leave->reject()               // Update status
$leave->getDurationInDays()    // Calculate (to_date - from_date + 1)

// Relationships
$leave->internAccount()        // Get related InternAccount
```

**EmployeeLeave.php & SupervisorLeave.php now include:**
```php
// Same scopes and methods as Leave
->employee($emp_id)            // For EmployeeLeave
->supervisor($sup_id)          // For SupervisorLeave

// Relationships
$leave->employee()             // Get related ManagersAccount
$leave->supervisor()           // Get related ManagersAccount
```

---

## Migration Strategy

### Phase 1: Service Layer (CURRENT ✅)
- Created TaskManagementService
- Created LeaveManagementService
- Enhanced all models with scopes/helper methods
- Controllers can optionally use services

### Phase 2: Controller Updates (RECOMMENDED)
Update existing controllers to use services:

```php
// Before: Direct model queries
$tasks = InternTask::where('eti_id', $etiId)->get();
$leaves = Leave::where('eti_id', $etiId)->get();

// After: Using services
$taskService = new TaskManagementService();
$tasks = $taskService->getInternTasks($intId);

$leaveService = new LeaveManagementService();
$leaves = $leaveService->getInternLeaves($intId);
```

### Phase 3: Database Consolidation (FUTURE)
When ready, consolidate tables:

```php
// Create unified tables
Schema::create('tasks_unified', function (Blueprint $table) {
    $table->id();
    $table->string('task_type'); // 'general' | 'intern' | 'project'
    $table->string('title');
    // ... all fields
    $table->timestamp('created_at');
    $table->timestamp('updated_at');
});

Schema::create('leaves_unified', function (Blueprint $table) {
    $table->id();
    $table->string('requestor_type'); // 'intern' | 'supervisor' | 'employee'
    $table->integer('requestor_id');
    // ... all fields
    $table->timestamp('created_at');
    $table->timestamp('updated_at');
});

// Migrate data
// Drop old tables after verification
```

### Phase 4: Model Updates (FUTURE)
Replace old models with new unified models using polymorphic relationships.

---

## Benefits of This Approach

| Aspect | Before | After |
|--------|--------|-------|
| **Code Duplication** | High (3 task models, 3 leave models) | Low (service abstracts logic) |
| **Query Complexity** | Controllers handle multiple models | Service handles all variations |
| **Consistency** | Different methods per model | Unified interface across types |
| **Discoverability** | Unclear which model to use | Services document all methods |
| **Testing** | Must test 6 models | Can test 2 services |
| **Migration Path** | High risk, big bang | Low risk, gradual transition |

---

## Implementation Checklist

- [x] Create TaskManagementService
- [x] Create LeaveManagementService
- [x] Enhance Task model scopes/methods
- [x] Enhance InternTask model scopes/methods
- [x] Enhance ProjectTask model scopes/methods
- [x] Enhance Leave model scopes/methods
- [x] Enhance EmployeeLeave model scopes/methods
- [x] Enhance SupervisorLeave model scopes/methods
- [ ] Update ManagerLeaveController to use LeaveManagementService
- [ ] Update Leave approval workflows (manager leaves)
- [ ] Update InternTaskController to use TaskManagementService
- [ ] Update ProjectTaskController to use TaskManagementService
- [ ] Add service integrations to dashboards
- [ ] Create unified leave request view
- [ ] Create unified task management view

---

## Questions & Troubleshooting

**Q: Can I still use the models directly?**
A: Yes, all models work exactly as before. Services are optional wrappers for convenience.

**Q: Will this affect existing migrations?**
A: No, all existing tables remain unchanged. This is purely a service layer addition.

**Q: How do I transition to services?**
A: Gradually update controllers one at a time. Keep existing code working while adding service calls.

**Q: What if I need custom filters not in the service?**
A: Extend TaskManagementService or LeaveManagementService, or use models directly.

**Q: When should I consolidate at the database level?**
A: After all controllers use services and you're confident the abstraction is complete.

