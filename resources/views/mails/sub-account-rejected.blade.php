<x-mail.layout title="Documents rejected">
    <h1 style="margin:0 0 16px; font-size:20px;">We need to review your documents</h1>

    <p style="margin:0 0 16px;">
        Hi {{ $user->name }}. The documents you sent were not approved, so your receiving
        account is still inactive.
    </p>

    @if ($reason)
        <p style="margin:0 0 16px; padding:12px 16px; background-color:#f4f5f7; border-radius:6px;">
            <strong>Reason:</strong> {{ $reason }}
        </p>
    @endif

    <x-mail.button :url="$onboardingUrl">
        Resend documents
    </x-mail.button>
</x-mail.layout>
