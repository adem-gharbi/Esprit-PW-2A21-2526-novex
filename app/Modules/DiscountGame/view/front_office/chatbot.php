<?php

declare(strict_types=1);

if (!isset($_SESSION['player'])) {
    return;
}

$chatbotPlayer = $_SESSION['player'];
$chatbotGames = $chatbotGames ?? [];
$chatbotSelectedGame = $chatbotSelectedGame ?? null;
$chatbotCoupon = $chatbotCoupon ?? null;

$chatbotContext = [
    'playerName' => (string) ($chatbotPlayer['full_name'] ?? 'traveler'),
    'destination' => (string) ($chatbotPlayer['destination'] ?? ''),
    'games' => array_map(static function (array $game): array {
        return [
            'name' => (string) ($game['game_name'] ?? ''),
            'type' => (string) ($game['game_type'] ?? ''),
            'description' => (string) ($game['game_description'] ?? ''),
            'prompt' => (string) ($game['prompt_text'] ?? ''),
            'clue' => (string) ($game['clue_text'] ?? ''),
            'discountType' => (string) ($game['discount_type'] ?? ''),
            'discountPercentage' => (int) ($game['discount_percentage'] ?? 0),
            'expiresAt' => (string) ($game['discount_expiration_date'] ?? ''),
        ];
    }, is_array($chatbotGames) ? $chatbotGames : []),
    'selectedGame' => is_array($chatbotSelectedGame) ? [
        'name' => (string) ($chatbotSelectedGame['game_name'] ?? ''),
        'type' => (string) ($chatbotSelectedGame['game_type'] ?? ''),
        'description' => (string) ($chatbotSelectedGame['game_description'] ?? ''),
        'prompt' => (string) ($chatbotSelectedGame['prompt_text'] ?? ''),
        'clue' => (string) ($chatbotSelectedGame['clue_text'] ?? ''),
        'discountType' => (string) ($chatbotSelectedGame['discount_type'] ?? ''),
        'discountPercentage' => (int) ($chatbotSelectedGame['discount_percentage'] ?? 0),
        'expiresAt' => (string) ($chatbotSelectedGame['discount_expiration_date'] ?? ''),
    ] : null,
    'coupon' => is_array($chatbotCoupon) ? [
        'code' => (string) ($chatbotCoupon['code'] ?? ''),
        'discountType' => (string) ($chatbotCoupon['discount_type'] ?? ''),
        'discountPercentage' => (int) ($chatbotCoupon['discount_percentage'] ?? 0),
        'expiresAt' => (string) ($chatbotCoupon['expires_at'] ?? ''),
    ] : null,
];
?>
<div class="ai-help" data-ai-help>
    <button class="ai-help__toggle" type="button" data-ai-help-toggle aria-expanded="false" aria-controls="ai-help-panel">
        AI Help
    </button>

    <section class="ai-help__panel" id="ai-help-panel" aria-label="AI help chat" hidden>
        <header class="ai-help__header">
            <div>
                <strong>Voyagio AI Help</strong>
                <span>Game and coupon support</span>
            </div>
            <button type="button" class="ai-help__close" data-ai-help-close aria-label="Close chat">x</button>
        </header>

        <div class="ai-help__messages" data-ai-help-messages></div>

        <form class="ai-help__form" data-ai-help-form>
            <input
                type="text"
                data-ai-help-input
                autocomplete="off"
                placeholder="Ask about the game or coupon"
                aria-label="Ask the AI help assistant"
            >
            <button type="submit">Send</button>
        </form>
    </section>
</div>

<style>
    .ai-help {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 1000;
        font-family: "Segoe UI", Tahoma, sans-serif;
    }

    .ai-help__toggle,
    .ai-help__form button,
    .ai-help__close {
        width: auto;
        border: 0;
        cursor: pointer;
        font-family: inherit;
    }

    .ai-help__toggle {
        min-width: 112px;
        padding: 13px 18px;
        border-radius: 999px;
        background: #0f3552;
        color: #ffffff;
        font-weight: 800;
        box-shadow: 0 12px 34px rgba(15, 53, 82, 0.28);
    }

    .ai-help__panel {
        position: absolute;
        right: 0;
        bottom: 58px;
        width: min(360px, calc(100vw - 32px));
        height: min(520px, calc(100vh - 110px));
        display: grid;
        grid-template-rows: auto 1fr auto;
        overflow: hidden;
        border-radius: 18px;
        background: #ffffff;
        color: #162434;
        box-shadow: 0 22px 60px rgba(15, 53, 82, 0.22);
        border: 1px solid rgba(15, 53, 82, 0.1);
    }

    .ai-help__panel[hidden] {
        display: none;
    }

    .ai-help__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px;
        background: #0f3552;
        color: #ffffff;
    }

    .ai-help__header strong,
    .ai-help__header span {
        display: block;
    }

    .ai-help__header span {
        margin-top: 2px;
        color: rgba(255, 255, 255, 0.74);
        font-size: 0.82rem;
    }

    .ai-help__close {
        min-width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1;
    }

    .ai-help__messages {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 16px;
        overflow-y: auto;
        background: #f6fafc;
    }

    .ai-help__message {
        max-width: 88%;
        padding: 10px 12px;
        border-radius: 14px;
        font-size: 0.92rem;
        line-height: 1.45;
        white-space: pre-line;
    }

    .ai-help__message--bot {
        align-self: flex-start;
        background: #ffffff;
        border: 1px solid rgba(15, 53, 82, 0.1);
    }

    .ai-help__message--user {
        align-self: flex-end;
        background: #ffbe55;
        color: #422900;
        font-weight: 700;
    }

    .ai-help__form {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 8px;
        padding: 12px;
        background: #ffffff;
        border-top: 1px solid rgba(15, 53, 82, 0.1);
    }

    .ai-help__form input {
        width: 100%;
        min-width: 0;
        border: 1px solid rgba(15, 53, 82, 0.18);
        border-radius: 12px;
        padding: 11px 12px;
        font: inherit;
    }

    .ai-help__form button {
        padding: 11px 13px;
        border-radius: 12px;
        background: #0f3552;
        color: #ffffff;
        font-weight: 800;
    }

    @media (max-width: 520px) {
        .ai-help {
            right: 16px;
            bottom: 16px;
        }

        .ai-help__panel {
            right: -6px;
        }
    }
</style>

<script>
    window.VoyagioChatContext = <?= json_encode($chatbotContext, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
</script>
<script src="chatbot.js"></script>
