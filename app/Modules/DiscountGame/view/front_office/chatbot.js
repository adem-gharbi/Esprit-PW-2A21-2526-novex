(function () {
    const root = document.querySelector('[data-ai-help]');

    if (!root) {
        return;
    }

    const context = window.VoyagioChatContext || {};
    const toggle = root.querySelector('[data-ai-help-toggle]');
    const close = root.querySelector('[data-ai-help-close]');
    const panel = root.querySelector('#ai-help-panel');
    const messages = root.querySelector('[data-ai-help-messages]');
    const form = root.querySelector('[data-ai-help-form]');
    const input = root.querySelector('[data-ai-help-input]');

    function addMessage(text, sender) {
        const bubble = document.createElement('div');
        bubble.className = 'ai-help__message ai-help__message--' + sender;
        bubble.textContent = text;
        messages.appendChild(bubble);
        messages.scrollTop = messages.scrollHeight;
    }

    function describeGame(game) {
        if (!game) {
            return '';
        }

        const type = game.type ? game.type.replace(/_/g, ' ') : 'game';
        const reward = game.discountPercentage
            ? game.discountPercentage + '% ' + (game.discountType || 'discount')
            : 'a linked discount coupon';

        return game.name + ' is a ' + type + '. ' +
            (game.description || 'Read the prompt and clue, then submit your answer.') +
            ' Reward: ' + reward + '.';
    }

    function gameList() {
        const games = Array.isArray(context.games) ? context.games : [];

        if (!games.length) {
            return 'There are no active games available right now. Please check again after the admin publishes an active game with an active discount.';
        }

        return 'Available games:\n' + games.map(function (game, index) {
            return (index + 1) + '. ' + describeGame(game);
        }).join('\n');
    }

    function couponHelp() {
        const coupon = context.coupon;

        if (coupon && coupon.code) {
            return 'Your coupon code is ' + coupon.code + '. It gives you ' +
                coupon.discountPercentage + '% ' + coupon.discountType +
                ' and is valid until ' + coupon.expiresAt + '.';
        }

        const selectedGame = context.selectedGame;

        if (selectedGame) {
            return 'To unlock the coupon, answer the selected game correctly. This game rewards ' +
                selectedGame.discountPercentage + '% ' + selectedGame.discountType +
                (selectedGame.expiresAt ? ' until ' + selectedGame.expiresAt + '.' : '.');
        }

        return 'Choose an active game and submit the correct answer. When you win, Voyagio creates your discount coupon automatically.';
    }

    function clueHelp() {
        const selectedGame = context.selectedGame;

        if (!selectedGame) {
            return 'Select a game first, then I can explain its clue and reward.';
        }

        return 'For ' + selectedGame.name + ', read this clue carefully: ' +
            (selectedGame.clue || 'No clue is shown for this game yet.') +
            ' I can explain the rules, but I cannot reveal the exact answer because that would skip the challenge.';
    }

    function rulesHelp() {
        const selectedGame = context.selectedGame;

        if (selectedGame) {
            return 'How this game works: read the prompt, use the clue, type your answer in the answer box, and submit it. If it matches the admin answer, your coupon is created immediately.';
        }

        return 'How to play: choose one active game, read the description and clue, then submit your answer. A correct answer unlocks the discount connected to that game.';
    }

    function answerFor(question) {
        const text = question.toLowerCase();

        if (text.includes('coupon') || text.includes('code') || text.includes('discount') || text.includes('reward')) {
            return couponHelp();
        }

        if (text.includes('clue') || text.includes('hint') || text.includes('help answer')) {
            return clueHelp();
        }

        if (text.includes('game') || text.includes('play') || text.includes('rule') || text.includes('how')) {
            if (context.selectedGame) {
                return describeGame(context.selectedGame) + '\n\n' + rulesHelp();
            }

            return gameList();
        }

        if (text.includes('destination') || text.includes('travel')) {
            return 'Your dream destination is ' + (context.destination || 'saved in your profile') +
                '. Play a Voyagio game to unlock a travel discount for your booking.';
        }

        if (text.includes('hello') || text.includes('hi') || text.includes('hey')) {
            return 'Hi ' + (context.playerName || 'traveler') + '. Ask me about the game, clue, discount, or coupon code.';
        }

        return 'I can help with the Voyagio game, clues, rewards, and coupon details. Try asking "How do I play?", "What is my coupon?", or "Explain the clue."';
    }

    function openPanel() {
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');

        if (!messages.children.length) {
            addMessage('Hi ' + (context.playerName || 'traveler') + '. I can help with the game and discount coupon. What do you need?', 'bot');
        }

        input.focus();
    }

    function closePanel() {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
    }

    toggle.addEventListener('click', function () {
        if (panel.hidden) {
            openPanel();
        } else {
            closePanel();
        }
    });

    close.addEventListener('click', closePanel);

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const question = input.value.trim();

        if (!question) {
            return;
        }

        addMessage(question, 'user');
        input.value = '';
        window.setTimeout(function () {
            addMessage(answerFor(question), 'bot');
        }, 220);
    });
}());
