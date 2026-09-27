<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | Room Scheduling</title>
</head>
<body>
    <main>
        <h1>Room Scheduling</h1>
        <p>Sign in to manage the schedule.</p>

        @if ($errors->any())
            <div role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <label>
                <input name="remember" type="checkbox" value="1">
                Remember me
            </label>

            <button type="submit">Sign in</button>
        </form>

        @if (app()->environment('local') && config('auth.auto_login.enabled'))
            <form method="POST" action="{{ route('login.auto') }}">
                @csrf
                <button type="submit">Auto-login</button>
            </form>
        @endif
    </main>
</body>
</html>