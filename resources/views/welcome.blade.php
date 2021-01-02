<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="loggedUser" content="{{ $loggedUser }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>ToDoApp</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet"></link>

    </head>

    

    <body>
        <div id="app">
            
            <navbar></navbar>

            <router-view></router-view>

        </div>
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
