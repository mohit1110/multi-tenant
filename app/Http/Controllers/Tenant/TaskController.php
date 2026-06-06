<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): View
    {
        $tenant = $this->tenantManager->get();
        $user = auth('tenant')->user();

        $query = Task::with(['user', 'assignedUser']);

        // Non-admins only see their own or assigned tasks
        if (! $user->isTenantAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'ilike', '%' . $request->search . '%')
                    ->orWhere('description', 'ilike', '%' . $request->search . '%');
            });
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total'       => $query->count(),
            'pending'     => $query->byStatus('pending')->count(),
            'in_progress' => $query->byStatus('in_progress')->count(),
            'completed'   => $query->byStatus('completed')->count(),
        ];

        return view('tenant.tasks.index', compact('tenant','tasks', 'stats'));
    }

    /**
     * Show form to create a new task.
     */
    public function create(): View
    {
        $tenant = $this->tenantManager->get();
        $users = User::where('is_active', true)->get();
        $statuses = Task::STATUSES;
        $priorities = Task::PRIORITIES;

        return view('tenant.tasks.create', compact('tenant','users', 'statuses', 'priorities'));
    }

    /**
     * Store a new task.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $tenant = $this->tenantManager->get();
        $user = auth('tenant')->user();

        Task::create(array_merge($request->validated(), [
            'user_id'   => $user->id,
        ]));

        return redirect()->route('tenant.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display a specific task.
     */
    public function show($id): View
    {
        $task = Task::findOrFail($id);
        $this->authorizeTenantAccess($task);

        $task->load(['user', 'assignedUser']);

        return view('tenant.tasks.show', compact('task'));
    }

    /**
     * Show form to edit a task.
     */
    public function edit($id): View
    {
        $task = Task::findOrFail($id);

        $this->authorizeTenantAccess($task);
        $this->authorizeEdit($task);

        $tenant = $this->tenantManager->get();
        $users = User::where('is_active', true)->get();
        $statuses = Task::STATUSES;
        $priorities = Task::PRIORITIES;

        return view('tenant.tasks.edit', compact('task', 'users', 'statuses', 'priorities'));
    }

    /**
     * Update a task.
     */
    public function update(UpdateTaskRequest $request, $id): RedirectResponse
    {
         $task = Task::findOrFail($id);
        $this->authorizeTenantAccess($task);
        $this->authorizeEdit($task);

        $task->update($request->validated());

        return redirect()->route('tenant.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Delete a task.
     */
    public function destroy($id): RedirectResponse
    {
        $task = Task::findOrFail($id);
        $this->authorizeTenantAccess($task);
        $this->authorizeEdit($task);

        $task->delete();

        return redirect()->route('tenant.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Ensure the task belongs to the current tenant.
     */
    protected function authorizeTenantAccess(Task $task): void
    {
        $tenant = $this->tenantManager->get();

        // if ($task->tenant_id !== $tenant->id) {
        //     abort(403, 'Access denied.');
        // }
    }

    /**
     * Ensure the user can edit this task.
     */
    protected function authorizeEdit(Task $task): void
    {
        $user = auth('tenant')->user();

        if (! $user->isTenantAdmin() && $task->user_id !== $user->id) {
            abort(403, 'You can only edit tasks you created.');
        }
    }
}
