<?php

return [
    "almostExpiring" => [

        // Transação parcelada
        "withInstallment" =>
            "Olá, {name}! 👋\n" .
            "\n" .
            "A parcela *{installment}/{total}* de *{title}* está chegando.\n" .
            "\n" .
            "💰 Valor: *R$ {amount}*\n" .
            "📅 Vencimento: *{dueDate}*\n" .
            "\n" .
            "Se já estiver pago, é só ignorar esta mensagem.",

        // Transação única (sem parcelas)
        "single" =>
            "Olá, {name}! 👋\n" .
            "\n" .
            "A conta *{title}* está chegando.\n" .
            "\n" .
            "💰 Valor: *R$ {amount}*\n" .
            "📅 Vencimento: *{dueDate}*\n" .
            "\n" .
            "Se já estiver pago, é só ignorar esta mensagem.",
    ]
];
