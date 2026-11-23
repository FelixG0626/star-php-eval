(() => {
    const picker = document.querySelector('[data-date-picker]');
    if (!picker) return;

    const summary = picker.querySelector('summary');
    const label = picker.querySelector('[data-range-label]');
    const heading = picker.querySelector('[data-month]');
    const days = picker.querySelector('[data-days]');
    const instruction = picker.querySelector('[data-instruction]');
    const fromInput = document.querySelector('#article-from');
    const toInput = document.querySelector('#article-to');
    const dateLabel = new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' });
    const monthLabel = new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' });

    const format = date => [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-');
    const parse = value => new Date(value + 'T00:00:00');

    let from;
    let to;
    let month;
    let awaitingEnd;
    let preset;

    function syncLabel() {
        const start = fromInput.value && dateLabel.format(parse(fromInput.value));
        const end = toInput.value && dateLabel.format(parse(toInput.value));
        label.textContent = start && end
            ? (start === end ? start : start + ' – ' + end)
            : (start ? 'From ' + start : end ? 'Through ' + end : 'Select dates');
    }

    function render() {
        const year = month.getFullYear();
        const index = month.getMonth();
        const offset = (month.getDay() + 6) % 7;
        const count = new Date(year, index + 1, 0).getDate();
        heading.textContent = monthLabel.format(month);
        days.replaceChildren();

        for (let blank = 0; blank < offset; blank++) {
            days.append(document.createElement('span'));
        }

        for (let day = 1; day <= count; day++) {
            const date = new Date(year, index, day);
            const value = format(date);
            const button = document.createElement('button');
            const selected = Boolean(from || to) && (!from || value >= from) && (!to || value <= to);
            button.type = 'button';
            button.textContent = day;
            button.dataset.day = value;
            button.setAttribute('aria-label', dateLabel.format(date));
            button.setAttribute('aria-pressed', String(selected));
            button.toggleAttribute('data-in-range', selected);
            button.toggleAttribute('data-endpoint', value === from || value === to);
            days.append(button);
        }

        for (const button of picker.querySelectorAll('[data-preset]')) {
            button.setAttribute('aria-pressed', String(button.dataset.preset === preset));
        }
        instruction.textContent = awaitingEnd ? 'Choose an end date.' : 'Choose a start and end date.';
    }

    function showMonth(date) {
        month = new Date(date.getFullYear(), date.getMonth(), 1);
    }

    function resetDraft() {
        from = fromInput.value;
        to = toInput.value;
        awaitingEnd = false;
        preset = from || to ? 'custom' : null;
        showMonth(from || to ? parse(from || to) : new Date());
        render();
    }

    function selectPreset(name) {
        preset = name;
        awaitingEnd = false;

        if (name === 'custom') {
            render();
            days.querySelector('button')?.focus();
            return;
        }

        const now = new Date();
        const year = now.getFullYear();
        const index = now.getMonth();
        const monday = now.getDate() - (now.getDay() + 6) % 7;
        const ranges = {
            today: [now, now],
            week: [new Date(year, index, monday - 7), new Date(year, index, monday - 1)],
            month: [new Date(year, index - 1, 1), new Date(year, index, 0)],
        };
        [from, to] = ranges[name].map(format);
        showMonth(parse(to));
        render();
    }

    summary.addEventListener('click', () => {
        if (!picker.open) resetDraft();
    });
    picker.addEventListener('keydown', event => {
        if (event.key === 'Escape' && picker.open) {
            picker.open = false;
            summary.focus();
        }
    });
    picker.closest('form')?.addEventListener('submit', () => { picker.open = false; });
    fromInput.addEventListener('input', syncLabel);
    picker.addEventListener('click', event => {
        const button = event.target.closest('button');
        if (!button) return;

        if (button.dataset.preset) {
            selectPreset(button.dataset.preset);
        } else if (button.dataset.monthStep) {
            month.setMonth(month.getMonth() + Number(button.dataset.monthStep));
            render();
        } else if (button.dataset.day) {
            const value = button.dataset.day;
            preset = 'custom';
            if (awaitingEnd) {
                [from, to] = [from, value].sort();
            } else {
                from = to = value;
            }
            awaitingEnd = !awaitingEnd;
            render();
            days.querySelector('[data-day="' + value + '"]')?.focus();
        } else if (button.hasAttribute('data-apply')) {
            fromInput.value = from;
            toInput.value = to;
            picker.open = false;
            summary.focus();
            fromInput.dispatchEvent(new Event('input', { bubbles: true }));
            picker.closest('form')?.requestSubmit();
        }
    });
    syncLabel();
})();
