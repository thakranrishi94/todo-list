<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Todo List</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .todo-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .task-item {
            border-top: 1px solid #dee2e6;
            padding: 10px 0;
        }
        .completed-task {
            text-decoration: line-through;
            color: #6c757d;
        }
        .task-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #6c757d;
        }
        .timestamp {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container todo-container">
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="showAllTasks">
                <label class="form-check-label" for="showAllTasks">
                    Show All Tasks
                </label>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text" id="task-count">0</span>
                    <input type="text" id="taskInput" class="form-control" placeholder="Project # To Do">
                    <button class="btn btn-success" id="addTaskBtn">Add</button>
                </div>
                <div id="errorMessage" class="text-danger mt-1" style="display: none;"></div>
            </div>
        </div>
        
        <div id="taskList">
            @foreach($tasks as $task)
            <div class="row task-item" data-id="{{ $task->id }}">
                <div class="col">
                    <div class="form-check">
                        <input class="form-check-input task-checkbox" type="checkbox" 
                               @if($task->completed) checked @endif>
                        <label class="form-check-label @if($task->completed) completed-task @endif">
                            {{ $task->title }}
                            <span class="timestamp ms-2">a few seconds ago</span>
                        </label>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <div class="task-avatar me-2 d-flex align-items-center justify-content-center text-white">
                        <small>U</small>
                    </div>
                    <button class="btn btn-sm delete-task">
                        <i class="fas fa-trash text-secondary"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Update task counter
            function updateTaskCount() {
                let count = 0;
                if (!$('#showAllTasks').is(':checked')) {
                    count = $('.task-item:visible').length;
                } else {
                    count = $('.task-item').length;
                }
                $('#task-count').text(count);
            }
            
            // Initialize task count
            updateTaskCount();
            
            // Add new task
            $('#addTaskBtn').click(function() {
                const title = $('#taskInput').val().trim();
                
                if (!title) {
                    return;
                }
                
                $.ajax({
                    url: "{{ route('tasks.store') }}",
                    type: "POST",
                    data: {
                        title: title
                    },
                    success: function(response) {
                        // Clear the input and error message
                        $('#taskInput').val('');
                        $('#errorMessage').hide();
                        
                        // Create new task element
                        const newTask = `
                            <div class="row task-item" data-id="${response.task.id}">
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input task-checkbox" type="checkbox">
                                        <label class="form-check-label">
                                            ${response.task.title}
                                            <span class="timestamp ms-2">a few seconds ago</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-auto d-flex align-items-center">
                                    <div class="task-avatar me-2 d-flex align-items-center justify-content-center text-white">
                                        <small>U</small>
                                    </div>
                                    <button class="btn btn-sm delete-task">
                                        <i class="fas fa-trash text-secondary"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        
                        // Add task to the list
                        $('#taskList').prepend(newTask);
                        updateTaskCount();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('#errorMessage').text(xhr.responseJSON.error).show();
                        }
                    }
                });
            });
            
            // Enter key to add task
            $('#taskInput').keypress(function(e) {
                if (e.which === 13) { // Enter key
                    $('#addTaskBtn').click();
                    return false;
                }
            });
            
            // Toggle task completion
            $(document).on('change', '.task-checkbox', function() {
                const taskItem = $(this).closest('.task-item');
                const taskId = taskItem.data('id');
                const taskLabel = $(this).siblings('.form-check-label');
                const isCompleted = $(this).is(':checked');
                
                $.ajax({
                    url: `/tasks/${taskId}/toggle`,
                    type: 'PATCH',
                    success: function(response) {
                        if (response.completed) {
                            taskLabel.addClass('completed-task');
                        } else {
                            taskLabel.removeClass('completed-task');
                        }
                        
                        // If "Show All Tasks" is not checked, hide completed tasks
                        if (!$('#showAllTasks').is(':checked') && response.completed) {
                            taskItem.hide();
                            updateTaskCount();
                        }
                    }
                });
            });
            
            // Delete task (with confirmation)
            let taskToDelete = null;
            
            $(document).on('click', '.delete-task', function() {
                taskToDelete = $(this).closest('.task-item');
                $('#deleteModal').modal('show');
            });
            
            $('#confirmDelete').click(function() {
                if (taskToDelete) {
                    const taskId = taskToDelete.data('id');
                    
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        success: function(response) {
                            taskToDelete.remove();
                            $('#deleteModal').modal('hide');
                            updateTaskCount();
                        }
                    });
                }
            });
            
            // Show/Hide completed tasks
            $('#showAllTasks').change(function() {
                if ($(this).is(':checked')) {
                    // Show all tasks
                    $('.task-item').show();
                } else {
                    // Hide completed tasks
                    $('.task-item').each(function() {
                        if ($(this).find('.task-checkbox').is(':checked')) {
                            $(this).hide();
                        }
                    });
                }
                updateTaskCount();
            });
        });
    </script>
</body>
</html>