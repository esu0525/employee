document.addEventListener('DOMContentLoaded', function() {
    const taskInput = document.getElementById('taskInput');
    const addTaskBtn = document.getElementById('addTaskBtn');
    const taskList = document.getElementById('taskList');
    const allCaughtUp = document.getElementById('allCaughtUp');
    const taskCountBadge = document.getElementById('taskCount');
    
    // Get userId from a hidden input or data attribute to keep script clean
    const taskSection = document.getElementById('taskSection');
    const userId = taskSection ? taskSection.getAttribute('data-user-id') : 'default';
    
    let tasks = JSON.parse(localStorage.getItem('user_tasks_' + userId)) || [];

    function saveTasks() {
        localStorage.setItem('user_tasks_' + userId, JSON.stringify(tasks));
        renderTasks();
    }

    function renderTasks() {
        if (!taskList) return;
        taskList.innerHTML = '';
        
        if (tasks.length === 0) {
            if(allCaughtUp) allCaughtUp.style.display = 'flex';
            if(taskCountBadge) taskCountBadge.style.display = 'none';
        } else {
            if(allCaughtUp) allCaughtUp.style.display = 'none';
            if(taskCountBadge) {
                taskCountBadge.style.display = 'inline-block';
                taskCountBadge.innerText = tasks.length + ' Tasks';
            }
            
            tasks.forEach((task, index) => {
                const div = document.createElement('div');
                div.className = 'recent-item'; // Re-use recent-item styles for consistency
                div.style.cssText = 'padding: 0.75rem 1rem; background: var(--bg-main); border: 1px solid var(--border); transition: 0.3s;';
                
                div.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 1rem; width: 100%;">
                        <input type="checkbox" class="task-checkbox" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);" data-index="${index}">
                        <span style="flex: 1; font-size: 0.95rem; font-weight: 500; color: var(--text-main);">${task}</span>
                        <button class="delete-task" style="background: transparent; border: none; color: #ef4444; opacity: 0.5; padding: 4px; border-radius: 4px; cursor: pointer; transition: 0.2s;" data-index="${index}">
                            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                `;
                taskList.appendChild(div);
            });
            
            // Re-init lucide icons for dynamic items
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }

    function addTask() {
        if (!taskInput) return;
        const val = taskInput.value.trim();
        if (val) {
            tasks.push(val);
            taskInput.value = '';
            saveTasks();
        }
    }

    if (addTaskBtn) {
        addTaskBtn.addEventListener('click', addTask);
    }
    
    if (taskInput) {
        taskInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') addTask();
        });
    }

    if (taskList) {
        taskList.addEventListener('click', (e) => {
            const delBtn = e.target.closest('.delete-task');
            const checkbox = e.target.closest('.task-checkbox');
            
            if (delBtn) {
                const index = delBtn.getAttribute('data-index');
                tasks.splice(index, 1);
                saveTasks();
            } else if (checkbox) {
                // When checked, adding a slight delay for visual confirmation then complete
                const index = checkbox.getAttribute('data-index');
                const row = checkbox.closest('.recent-item');
                row.style.opacity = '0.4';
                row.style.transform = 'scale(0.95)';
                
                setTimeout(() => {
                    tasks.splice(index, 1);
                    saveTasks();
                }, 400);
            }
        });
    }

    renderTasks();

    // Re-init lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
