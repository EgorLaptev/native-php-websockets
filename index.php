<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="https://kit.fontawesome.com/19ce154370.js" crossorigin="anonymous"></script>
    <title>WebSockets</title>
</head>
<body>

    <aside class="search-users">

        <form name="searchUsers" id="searchUsers" class="search-users__form">
            <input type="text" placeholder="Enter user ID" class="search-users__input">
        </form>

        <ul class="search-users__list"></ul>

    </aside>

    <main class="messages">

        <section class="messages__body" id="messagesList"></section>

        <form id="chat" name="chat" class="chat-form">
            <input type="text" placeholder="Type" id="message" class="chat-form__message">
            <button type="submit" class="chat-form__send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>

    </main>

    <script defer src='./assets/js/app.js'></script>

</body>
</html>