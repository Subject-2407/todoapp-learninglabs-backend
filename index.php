<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
         tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>To Do App</title>
</head>
<body class="bg-slate-200 dark:bg-slate-900 flex justify-center items-center">
    <div class="main rounded-lg w-1/2 h-full p-7">
        <div class="container flex flex-col h-full gap-5">
            <div class="title basis-1/6">
                <div class="flex items-center">
                    <h1 class="text-4xl text-slate-900 dark:text-slate-200 font-semibold font-mono grow">To Do List</h1>
                    <div id="toggle-theme" class="rounded-full hover:bg-slate-300 dark:hover:bg-slate-700 hover:cursor-pointer p-2">
                        <img src="assets/images/theme.png" class="dark:invert" width="30" height="30">
                    </div>
                </div>
                <br>
                <hr class="border-slate-500">
            </div>
            <div class="todo-list basis-4/5 overflow-y-auto pr-2">
                <ul id="todo-list">
                </ul>
            </div>
            <div class="todo-create basis-auto">
                <div class="todo-create-input flex gap-7 p-2 items-center">
                    <input placeholder="Create your todos here" text="text" id="title" name="title" class="text-lg grow rounded-3xl bg-slate-100 dark:bg-slate-900 pl-4 pr-4 pt-3 pb-3 text-slate-900 dark:text-slate-200 border-2 border-slate-400 dark:border-slate-700">
                    <p id="input-details" class="text-3xl font-bold cursor-pointer text-slate-900 dark:text-slate-200">⋮</p>
                    <button tabindex="-1" id="todo-create-button" class="w-11 h-11 rounded-full bg-slate-900 dark:bg-slate-100 hover:bg-slate-700 dark:hover:bg-slate-300 flex justify-center items-center">
                        <p class="text-4xl pb-2 text-slate-200 dark:text-slate-900">+</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="details bg-slate-300 dark:bg-slate-800 w-1/2 h-full">

    </div>
    <div class="input-details absolute w-1/2 h-1/2 bg-slate-200 dark:bg-slate-800 rounded-xl border border-slate-300 dark:border-slate-600 z-50 p-7 text-slate-900 dark:text-slate-200 shadow hidden">
        
        <label for="input-description" class="block mb-2 text-sm font-medium text-slate-900 dark:text-slate-200">To Do Description (optional)</label>
        <textarea id="input-description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 resize-none" placeholder="Write your description here..."></textarea>
        <br>
        <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-slate-200" for="input-image">To Do image attachment (optional)</label>
        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="input-image" type="file" accept="image/*">
        <br><br><br>    
        <div class="w-full text-center">
            <button id="close-input-details" class="pl-4 pt-1 pr-4 pb-1 bg-slate-400 dark:bg-slate-500 text-slate-200 rounded-lg">Close</button>
        </div>
        
    </div>
    <script src="js/script.js"></script>
</body>
</html>