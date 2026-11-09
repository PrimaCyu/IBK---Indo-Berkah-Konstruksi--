<?php

namespace App\Policies;

use App\Models\ProjectCashflow;
use App\Models\User;

class ProjectCashflowPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isFinanceStaff($user);
    }

    public function view(User $user, ProjectCashflow $cashflow): bool
    {
        return $this->isFinanceStaff($user);
    }

    public function create(User $user): bool
    {
        return $this->isFinanceStaff($user);
    }

    public function update(User $user, ProjectCashflow $cashflow): bool
    {
        return $this->isFinanceStaff($user);
    }

    public function delete(User $user, ProjectCashflow $cashflow): bool
    {
        return $this->isFinanceStaff($user);
    }

    public function viewAttachment(User $user, ProjectCashflow $cashflow): bool
    {
        return $this->isFinanceStaff($user);
    }

    public function downloadReceipt(User $user, ProjectCashflow $cashflow): bool
    {
        return $this->isFinanceStaff($user);
    }

    public function viewAuditLogs(User $user): bool
    {
        return $this->isFinanceStaff($user);
    }

    private function isFinanceStaff(User $user): bool
    {
        return in_array($user->role, ['admin', 'accounting'], true);
    }
}
