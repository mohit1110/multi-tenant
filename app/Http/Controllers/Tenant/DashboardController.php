<?php

namespace App\Http\Controllers\Tenant;


use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Services\TenantManager;
use App\Services\TenantService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Show the tenant dashboard.
     */
    public function index(): View
    {
       
        $tenant = $this->tenantManager->get();
        $user = auth('tenant')->user();

        $taskQuery = Task::query();

        // Non-admins only see their tasks
        if (! $user->isTenantAdmin()) {
            $taskQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        }


        $stats = [
            'total_tasks'     => (clone $taskQuery)->count(),
            'pending_tasks'   => (clone $taskQuery)->where('status', 'pending')->count(),
            'in_progress'     => (clone $taskQuery)->where('status', 'in_progress')->count(),
            'completed_tasks' => (clone $taskQuery)->where('status', 'completed')->count(),
            'total_users'     => User::count(),
        ];

        $recentTasks = (clone $taskQuery)
            ->with(['user', 'assignedUser'])
            ->latest()
            ->take(5)
            ->get();

        $overdueTasks = (clone $taskQuery)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->count();

        return view('tenant.dashboard', compact('tenant', 'stats', 'recentTasks', 'overdueTasks'));
    }
}
