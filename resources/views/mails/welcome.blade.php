<x-mail.layout title="Welcome">
    <h1 style="margin:0 0 16px; font-size:20px;">Welcome, {{ $user->name }}!</h1>

    <p style="margin:0 0 16px;">
        Your {{ config('app.name') }} account has been created. You can now create groups,
        organize campaigns and follow every contribution in one place.
    </p>

    <p style="margin:0;">
        To start receiving contributions, the next step is to activate your receiving
        account by sending your documents.
    </p>
</x-mail.layout>
