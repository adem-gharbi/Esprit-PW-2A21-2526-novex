<?php

declare(strict_types=1);

$reviewGame = $reviewGame ?? null;
$reviewSession = $reviewSession ?? null;
$reviewReturnTo = $reviewReturnTo ?? 'play.php';

if (!is_array($reviewGame) || !is_array($reviewSession)) {
    return;
}
?>
<div class="review-modal" data-review-modal>
    <div class="review-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="review-title">
        <button class="review-modal__close" type="button" data-review-close aria-label="Close review">x</button>
        <p class="review-modal__eyebrow">Game review</p>
        <h2 id="review-title">How was <?= htmlspecialchars($reviewGame['game_name'], ENT_QUOTES, 'UTF-8') ?>?</h2>

        <form method="post" action="review.php" class="review-modal__form">
            <input type="hidden" name="admin_game_id" value="<?= (int) $reviewGame['id'] ?>">
            <input type="hidden" name="game_session_id" value="<?= (int) $reviewSession['id'] ?>">
            <input type="hidden" name="return_to" value="<?= htmlspecialchars($reviewReturnTo, ENT_QUOTES, 'UTF-8') ?>">

            <fieldset class="review-modal__stars" aria-label="Star rating">
                <?php for ($star = 5; $star >= 1; $star--): ?>
                    <input id="review-star-<?= $star ?>" type="radio" name="rating" value="<?= $star ?>" required>
                    <label for="review-star-<?= $star ?>" title="<?= $star ?> stars">★</label>
                <?php endfor; ?>
            </fieldset>

            <label class="review-modal__label" for="review-description">Description</label>
            <textarea
                id="review-description"
                name="description"
                rows="4"
                placeholder="Write what you thought about this game"
                required
            ></textarea>

            <div class="review-modal__actions">
                <button type="button" class="review-modal__secondary" data-review-close>Later</button>
                <button type="submit" class="review-modal__primary">Submit review</button>
            </div>
        </form>
    </div>
</div>

<style>
    .review-modal {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(15, 53, 82, 0.46);
    }

    .review-modal[hidden] {
        display: none;
    }

    .review-modal__dialog {
        position: relative;
        width: min(460px, 100%);
        padding: 26px;
        border-radius: 22px;
        background: #ffffff;
        color: #0f3552;
        box-shadow: 0 24px 70px rgba(15, 53, 82, 0.28);
    }

    .review-modal__close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 50%;
        background: rgba(15, 53, 82, 0.1);
        color: #0f3552;
        cursor: pointer;
        font-weight: 800;
    }

    .review-modal__eyebrow {
        margin: 0 0 6px;
        color: #2e7c74;
        font: 800 0.78rem "Segoe UI", Tahoma, sans-serif;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .review-modal h2 {
        margin: 0 34px 18px 0;
        color: #0f3552;
        font-size: 1.6rem;
        line-height: 1.15;
    }

    .review-modal__form {
        display: grid;
        gap: 14px;
    }

    .review-modal__stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 6px;
        border: 0;
        padding: 0;
        margin: 0;
    }

    .review-modal__stars input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .review-modal__stars label {
        color: #c7d0d8;
        cursor: pointer;
        font-size: 2.2rem;
        line-height: 1;
        margin: 0;
    }

    .review-modal__stars input:checked ~ label,
    .review-modal__stars label:hover,
    .review-modal__stars label:hover ~ label {
        color: #ffbe55;
    }

    .review-modal__label {
        font-weight: 800;
        margin: 0;
    }

    .review-modal textarea {
        width: 100%;
        resize: vertical;
        min-height: 110px;
        border: 1px solid rgba(15, 53, 82, 0.18);
        border-radius: 14px;
        padding: 12px;
        font: 1rem "Segoe UI", Tahoma, sans-serif;
    }

    .review-modal__actions {
        display: grid;
        grid-template-columns: 0.75fr 1fr;
        gap: 10px;
    }

    .review-modal__actions button {
        width: 100%;
        border: 0;
        border-radius: 14px;
        padding: 13px 14px;
        cursor: pointer;
        font-weight: 800;
    }

    .review-modal__primary {
        background: #0f3552;
        color: #ffffff;
    }

    .review-modal__secondary {
        background: rgba(15, 53, 82, 0.08);
        color: #0f3552;
    }
</style>

<script src="review_modal.js"></script>
