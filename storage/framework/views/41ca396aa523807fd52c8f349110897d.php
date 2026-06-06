<?php $__env->startSection('title', $task->title); ?>
<?php $__env->startSection('page-title', 'Task Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="<?php echo e(route('tenant.tasks.index')); ?>" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Tasks
        </a>
        <div class="flex gap-2">
            <a href="<?php echo e(route('tenant.tasks.edit', $task)); ?>"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <form method="POST" action="<?php echo e(route('tenant.tasks.destroy', $task)); ?>"
                  onsubmit="return confirm('Are you sure you want to delete this task?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="px-8 py-6 border-b border-gray-100">
            <div class="flex flex-wrap items-start gap-3 mb-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                    <?php if($task->status === 'completed'): ?> bg-green-100 text-green-800
                    <?php elseif($task->status === 'in_progress'): ?> bg-blue-100 text-blue-800
                    <?php elseif($task->status === 'cancelled'): ?> bg-red-100 text-red-800
                    <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                    <?php echo e(\App\Models\Task::STATUSES[$task->status]); ?>

                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                    <?php if($task->priority === 'urgent'): ?> bg-red-100 text-red-800
                    <?php elseif($task->priority === 'high'): ?> bg-orange-100 text-orange-800
                    <?php elseif($task->priority === 'medium'): ?> bg-yellow-100 text-yellow-800
                    <?php else: ?> bg-green-100 text-green-800 <?php endif; ?>">
                    <?php echo e(\App\Models\Task::PRIORITIES[$task->priority]); ?> Priority
                </span>
            </div>
            <h1 class="text-xl font-bold text-gray-900"><?php echo e($task->title); ?></h1>
        </div>

        
        <div class="px-8 py-6 space-y-6">
            
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Description</h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->description): ?>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line"><?php echo e($task->description); ?></p>
                <?php else: ?>
                    <p class="text-sm text-gray-400 italic">No description provided.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Created By</h3>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 text-xs font-bold">
                            <?php echo e(strtoupper(substr($task->user->name, 0, 1))); ?>

                        </div>
                        <span class="text-sm text-gray-900"><?php echo e($task->user->name); ?></span>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Assigned To</h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->assignedUser): ?>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-purple-100 rounded-full flex items-center justify-center text-purple-700 text-xs font-bold">
                                <?php echo e(strtoupper(substr($task->assignedUser->name, 0, 1))); ?>

                            </div>
                            <span class="text-sm text-gray-900"><?php echo e($task->assignedUser->name); ?></span>
                        </div>
                    <?php else: ?>
                        <span class="text-sm text-gray-400">Unassigned</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Due Date</h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->due_date): ?>
                        <span class="text-sm <?php echo e($task->due_date->isPast() && !$task->isCompleted() ? 'text-red-600 font-semibold' : 'text-gray-900'); ?>">
                            <?php echo e($task->due_date->format('F j, Y')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($task->due_date->isPast() && !$task->isCompleted()): ?>
                                <span class="text-xs">(Overdue)</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    <?php else: ?>
                        <span class="text-sm text-gray-400">No due date</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Created At</h3>
                    <span class="text-sm text-gray-900"><?php echo e($task->created_at->format('F j, Y g:i A')); ?></span>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Last Updated</h3>
                    <span class="text-sm text-gray-900"><?php echo e($task->updated_at->format('F j, Y g:i A')); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.tenant', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\cms-final\resources\views/tenant/tasks/show.blade.php ENDPATH**/ ?>