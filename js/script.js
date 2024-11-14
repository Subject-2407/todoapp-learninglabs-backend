
function switchTheme(theme) {
    if (theme === 'dark') {
      $('html').addClass('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      $('html').removeClass('dark');
      localStorage.setItem('theme', 'light');
    }
  }
  
  
  function initializeTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
      switchTheme('dark');
    } else {
      switchTheme('light');
    }
  }
  
  $('#toggle-theme').on('click', function () {
    const currentTheme = $('html').hasClass('dark') ? 'dark' : 'light';
    switchTheme(currentTheme === 'dark' ? 'light' : 'dark');
  });
  
  $(document).ready(function () {
    let currentDetailId = -1;
    initializeTheme();

    function checkIfListIsEmpty() {
        if ($('#todo-list li').length === 0) {
            $('#todo-list').html(`
                <p class="empty-message text-center text-slate-500 dark:text-slate-400 mt-10">
                    Your to-do list is empty. Create a new one!
                </p>
            `);
        } else {
            $('.empty-message').remove();
        }
    }

    function fetchTodoById(id) {
        return $.ajax({
            url: `/todoapp/api/todo.php?id=${id}`,
            method: 'GET', 
            dataType: 'json', 
            success: function(response) {
                if (response.code === 200) {
                    return response.data[0];
                } else {
                    throw new Error(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching todo:', error);
                return null;
            }
        });
    }
    
    
    $.ajax({
        url: "/todoapp/api/todo.php", 
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.code === 200 && response.data) {
                response.data.forEach(function (item) {
                    let stateOptions = `
                        <option value="Pending" class="bg-blue-500" ${item.state === "Pending" ? "selected" : ""}>Pending</option>
                        <option value="In Progress" class="bg-orange-500" ${item.state === "In Progress" ? "selected" : ""}>In Progress</option>
                        <option value="Completed" class="bg-green-500" ${item.state === "Completed" ? "selected" : ""}>Completed</option>
                    `;
                    let stateColor = (state) => {switch (state) {
                        case "Pending":
                            return "bg-blue-500";
                            break;
                        case "In Progress":
                            return "bg-orange-500";
                            break;
                        case "Completed":
                            return "bg-green-500";
                            break;
                        }
                    }

                    let listItem = `
                        <li id="todo-${item.id}" data-updated-at="${item.updatedAt}">
                            <div class="todo-item flex w-full h-10 gap-5 font-sans mb-3 rounded-sm">
                                <h3 class="todo-title text-slate-900 dark:text-slate-200 mt-1 text-xl basis-3/4 cursor-pointer">
                                    <span class="mr-5">✦</span>${item.title}
                                </h3>
                                <select
                                    tabindex="-1"
                                    name="state"
                                    class="todo-state basis-1/6 rounded-3xl w-1/4 h-8 pb-1 mt-1 text-slate-200 ${stateColor(item.state)} font-semibold text-center appearance-none focus:outline-none focus:ring-0 hover:cursor-pointer"
                                >
                                    ${stateOptions}
                                </select>
                                <div class="todo-delete basis-auto hover:cursor-pointer hover:bg-red-500 text-center rounded-full">
                                    <img src="assets/images/trash-bin.png" 
                                    class="hover:invert dark:invert w-full h-full p-2 object-contain">
                                </div>
                            </div>
                        </li>
                    `;
                    
                    $('#todo-list').append(listItem);
                    checkIfListIsEmpty();
                });
            } else {
                console.error("Error: " + response.message);
                checkIfListIsEmpty();
            }
        },
        error: function (error) {
            console.error("Error fetching data", error);
        }
    });

    $('#title').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#todo-create-button').click();
        }
    });

    $('#input-details').on('click', function (event) {
        event.preventDefault();
        $(".input-details").removeClass("hidden");
        $(".main").addClass("blur-sm");
        $(".details").addClass("blur-sm");
    });

    $('#close-input-details').on('click', function (event) {
        event.preventDefault();
        $(".input-details").addClass("hidden");
        $(".main").removeClass("blur-sm");
        $(".details").removeClass("blur-sm");
    });

    $('#todo-create-button').click(function (event) {
        event.preventDefault();

        const title = $('#title').val();
        const description = $('#input-description').val();
        const state = 'Pending';

        if (!title) {
            alert("Title is required");
            return;
        }

        const imageFile = $('#input-image')[0].files[0];

        const formData = new FormData();
        formData.append('title', title);
        formData.append('description', description);
        formData.append('state', 'Pending');
        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: "/todoapp/api/todo.php", 
            type: "POST",
            contentType: "application/json",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.code === 201) {
                    const newItem = response.data;

                    let newListItem = `
                        <li id="todo-${newItem.id}" data-updated-at="${newItem.updatedAt}">
                            <div class="todo-item flex w-full h-10 gap-5 font-sans mb-3">
                                <h3 class="todo-title text-slate-900 dark:text-slate-200 mt-1 text-xl basis-3/4 cursor-pointer">
                                    <span class="mr-5">✦</span>${newItem.title}
                                </h3>
                                <select
                                    tabindex="-1"
                                    name="state"
                                    class="todo-state basis-1/6 rounded-3xl w-1/4 h-8 pb-1 mt-1 text-slate-200 bg-blue-500 font-semibold text-center appearance-none focus:outline-none focus:ring-0 hover:cursor-pointer"
                                >
                                    <option value="Pending" class="bg-blue-500" selected>Pending</option>
                                    <option value="In Progress" class="bg-orange-500">In Progress</option>
                                    <option value="Completed" class="bg-green-500">Completed</option>
                                </select>
                                <div class="todo-delete basis-auto hover:cursor-pointer hover:bg-red-400 text-center rounded-full">
                                    <img src="assets/images/trash-bin.png" 
                                    class="hover:invert dark:invert w-full h-full p-2 object-contain">
                                </div>
                            </div>
                        </li>
                    `;

                    const lastInProgress = $('#todo-list li').filter(function() {
                        return $(this).find('.todo-state').val() === 'In Progress';
                    }).last();

                    if (lastInProgress.length) {
                        $(newListItem).insertAfter(lastInProgress);
                    } else {
                        $('#todo-list').prepend(newListItem);
                        checkIfListIsEmpty();
                    }

                    $('#title').val('');
                    $('#input-description').val('');
                    $('#input-image').val('');
                } else {
                    console.error("Error: " + response.message);
                }
            },
            error: function (error) {
                console.error("Error adding new todo", error);
            }
        });
    });

    $('#todo-list').on('click', '.todo-delete', function () {
        const listItem = $(this).closest('li');
        const todoId = listItem.attr('id').split('-')[1];
        if (todoId == currentDetailId) {
            $('.details').removeClass('active');
            $('.main').removeClass('active');
        }

        $.ajax({
            url: `/todoapp/api/todo.php?id=${todoId}`,
            type: "DELETE",
            success: function (response) {
                if (response.code === 200) {
                    listItem.remove();
                    checkIfListIsEmpty();
                } else {
                    console.error("Error deleting todo item:", response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", xhr.responseText);
            }
        });
    });

    $('#todo-list').on('change', '.todo-state', function () {
        const listItem = $(this).closest('li');
        const todoId = listItem.attr('id').split('-')[1];
        const newState = $(this).val();

        $.ajax({
            url: `/todoapp/api/todo.php?id=${todoId}`,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ state: newState }),
            success: function (response) {
                if (response.code === 200) {
                    updateSelectBackgroundColor($(listItem).find('.todo-state'), newState);
                    listItem.attr('data-updated-at', response.data.updatedAt);
                    reorderList();
                } else {
                    console.error("Error updating todo item:", response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", xhr.responseText);
            }
        });
    });

    function updateSelectBackgroundColor(selectElement, state) {
        selectElement.removeClass('bg-blue-500 bg-orange-500 bg-green-500');
        
        if (state === 'Pending') {
            selectElement.addClass('bg-blue-500');
        } else if (state === 'In Progress') {
            selectElement.addClass('bg-orange-500');
        } else if (state === 'Completed') {
            selectElement.addClass('bg-green-500');
        }
    }

    function reorderList() {
        const listItems = $('#todo-list li').get();

        listItems.sort(function (a, b) {
            const stateOrder = { 'In Progress': 1, 'Pending': 2, 'Completed': 3 };
            const stateA = $(a).find('.todo-state').val();
            const stateB = $(b).find('.todo-state').val();
            const dateA = new Date($(a).attr('data-updated-at'));
            const dateB = new Date($(b).attr('data-updated-at'));

            if (stateOrder[stateA] !== stateOrder[stateB]) {
                return stateOrder[stateA] - stateOrder[stateB];
            }

            return dateB - dateA;
        });

        $.each(listItems, function (index, item) {
            $('#todo-list').append(item);
        });
    }

    $('#todo-list').on('click', '.todo-title', function () {
        const todoId = $(this).closest('li').attr('id').split('-')[1];
        const todoTitle = $(this).text().split('✦')[1];
        const imagePath = "http://localhost/todoapp/public/images/todo/";

        currentDetailId = todoId;
        fetchTodoById(todoId).then(todo => {
            if (todo) {
                $('.details').html(`
                    <h2 class="text-3xl font-semibold text-slate-900 dark:text-slate-200">${todoTitle}</h2>
                    <br>
                    ${todo.data[0].image ? `<img src="${imagePath + todo.data[0].image}" class="h-1/2"><br>` : ""}
                    <p id="todo-description" class="text-slate-900 dark:text-slate-200">${todo.data[0].description ? todo.data[0].description : ""}</p>
                    <br>
                    <button id="close-details" class="mt-5 pl-4 pt-1 pr-4 pb-1 bg-red-500 text-white rounded">Close</button>
                `);
        
                $('.details').addClass('active');
                $('.main').addClass('active');
            }
        });
        
    });

    $(document).on('click', '#close-details', function () {
        $('.details').removeClass('active');
        $('.main').removeClass('active');

    });
});

  