<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
    @yield('js')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="/" class="header__logo"><img src="images/rogo.png" alt="coachtech"></a>
            @if (request()->is('login'))
                <a href="/register" class="header__button">register</a>
            @elseif (request()->is('register'))
                <a href="/login" class="header__button">login</a>
            @elseif (request()->is('admin*'))
                <!-- <a href="/logout" class="header__button">logout</a> -->
                <form class="header__logout" action="{{ route('logout') }}" method="post">
                    @csrf
                    <button>ログアウト</button>
                </form>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>