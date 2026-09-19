<x-mail.layout title="New contribution received">
    <h1 style="margin:0 0 16px; font-size:20px;">You received a new contribution!</h1>

    <p style="margin:0 0 16px;">
        Hi {{ $user->name }}. The campaign <strong>{{ $campaign->title }}</strong> has just
        received a contribution.
    </p>

    <table cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f5f7; border-radius:6px;">
        <tr>
            <td style="padding:12px 16px; color:#7b8794;">Amount received</td>
            <td style="padding:12px 16px; text-align:right;"><strong>R$ {{ number_format($contribution->amount, 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td style="padding:12px 16px; color:#7b8794;">Total collected</td>
            <td style="padding:12px 16px; text-align:right;">R$ {{ number_format($campaign->total_collected, 2, ',', '.') }}</td>
        </tr>
        @if ($campaign->limit)
            <tr>
                <td style="padding:12px 16px; color:#7b8794;">Campaign goal</td>
                <td style="padding:12px 16px; text-align:right;">R$ {{ number_format($campaign->limit, 2, ',', '.') }}</td>
            </tr>
        @endif
    </table>
</x-mail.layout>
