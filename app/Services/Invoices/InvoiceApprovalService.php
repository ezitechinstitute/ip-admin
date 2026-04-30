<?php

namespace App\Services\Invoices;

class InvoiceApprovalService
{
    /**
     * Determine approval status based on role
     */
    public static function determine(string $role): string
    {
        return match ($role) {
            'admin'   => 'approved',
            'manager' => 'pending',
            default   => 'pending',
        };
    }

    /**
     * Check if invoice should go to approval queue
     */
    public static function shouldGoToQueue(string $role): bool
    {
        return $role === 'manager';
    }
}