<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EscalationTracking extends Model
{
    use SoftDeletes;

    protected $table = 'escalation_tracking';

    protected $fillable = [
        'intern_id',
        'manager_id',
        'escalation_type',
        'escalation_level',
        'escalated_at',
        'resolved_at',
        'notes',
        'notified_admin',
        'resolution_notes',
    ];

    protected $casts = [
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'notified_admin' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the intern related to this escalation
     */
    public function intern()
    {
        // Try InternAccount first
        return $this->belongsTo(InternAccount::class, 'intern_id', 'int_id');
    }

    /**
     * Get the manager related to this escalation
     */
    public function manager()
    {
        return $this->belongsTo(ManagersAccount::class, 'manager_id', 'manager_id');
    }

    // ==================== SCOPES ====================

    /**
     * Get pending escalations (not resolved)
     */
    public function scopePending($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Get resolved escalations
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Get escalations at manager reminder level
     */
    public function scopeManagerReminder($query)
    {
        return $query->where('escalation_level', 'manager_reminder');
    }

    /**
     * Get escalations at admin alert level
     */
    public function scopeAdminAlert($query)
    {
        return $query->where('escalation_level', 'admin_alert');
    }

    /**
     * Get interview escalations
     */
    public function scopeInterview($query)
    {
        return $query->where('escalation_type', 'interview');
    }

    /**
     * Get test escalations
     */
    public function scopeTest($query)
    {
        return $query->where('escalation_type', 'test');
    }

    /**
     * Get escalations for a specific manager
     */
    public function scopeForManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    /**
     * Get unnotified admin escalations
     */
    public function scopeUnnotifiedAdmin($query)
    {
        return $query->where('notified_admin', false)->where('escalation_level', 'admin_alert');
    }

    /**
     * Get recent escalations (last N hours)
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('escalated_at', '>=', now()->subHours($hours));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if escalation is pending
     */
    public function isPending(): bool
    {
        return is_null($this->resolved_at);
    }

    /**
     * Check if escalation is resolved
     */
    public function isResolved(): bool
    {
        return !is_null($this->resolved_at);
    }

    /**
     * Check if at manager reminder level
     */
    public function isManagerReminder(): bool
    {
        return $this->escalation_level === 'manager_reminder';
    }

    /**
     * Check if at admin alert level
     */
    public function isAdminAlert(): bool
    {
        return $this->escalation_level === 'admin_alert';
    }

    /**
     * Get escalation status label
     */
    public function getStatusLabel(): string
    {
        if ($this->isResolved()) {
            return 'Resolved ✓';
        }

        return match ($this->escalation_level) {
            'manager_reminder' => '⚠️ Manager Reminder',
            'admin_alert' => '🔴 Admin Alert',
            default => 'Unknown',
        };
    }

    /**
     * Get time since escalation
     */
    public function getHoursSinceEscalation(): int
    {
        return (int) $this->escalated_at->diffInHours(now());
    }

    /**
     * Upgrade escalation from manager_reminder to admin_alert
     */
    public function upgradeToAdminAlert(): bool
    {
        return $this->update([
            'escalation_level' => 'admin_alert',
            'notified_admin' => false, // Will send notification
        ]);
    }

    /**
     * Mark escalation as notified to admin
     */
    public function markAdminNotified(): bool
    {
        return $this->update(['notified_admin' => true]);
    }

    /**
     * Resolve escalation
     */
    public function resolve($resolutionNotes = null): bool
    {
        return $this->update([
            'resolved_at' => now(),
            'resolution_notes' => $resolutionNotes,
        ]);
    }

    /**
     * Get escalation description
     */
    public function getDescription(): string
    {
        $type = ucfirst($this->escalation_type);
        $level = str_replace('_', ' ', $this->escalation_level);

        $desc = "{$type} Escalation - ";

        if ($this->intern) {
            $desc .= "Intern: {$this->intern->name}";
        }

        if ($this->manager) {
            $desc .= " | Manager: {$this->manager->name}";
        }

        return $desc;
    }

    /**
     * Format escalation for display
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'intern_name' => $this->intern?->name ?? 'Unknown',
            'manager_name' => $this->manager?->name ?? 'Unknown',
            'type' => ucfirst($this->escalation_type),
            'level' => $this->getStatusLabel(),
            'escalated_at' => $this->escalated_at->format('d M Y, H:i'),
            'resolved_at' => $this->resolved_at?->format('d M Y, H:i') ?? 'Not Resolved',
            'hours_since' => $this->getHoursSinceEscalation(),
            'notes' => $this->notes,
        ];
    }
}
