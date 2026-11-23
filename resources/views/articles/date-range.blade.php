<input type="hidden" id="article-from" name="from" value="{{ $filters['from'] ?? '' }}">
<input type="hidden" id="article-to" name="to" value="{{ $filters['to'] ?? '' }}">

<span class="form-label d-block" id="article-date-label">Publication date</span>
<details class="article-date-picker" data-date-picker>
    <summary class="form-control" aria-labelledby="article-date-label article-date-value">
        <span id="article-date-value" data-range-label>Select dates</span>
    </summary>

    <div class="article-date-panel">
        <div class="article-date-presets" aria-label="Date presets">
            <button type="button" data-preset="today">Today</button>
            <button type="button" data-preset="week">Last Week</button>
            <button type="button" data-preset="month">Last Month</button>
            <button type="button" data-preset="custom">Custom</button>
            <button class="article-date-apply" type="button" data-apply>Apply</button>
        </div>
        <div class="article-date-calendar">
            <div class="article-date-heading">
                <button type="button" data-month-step="-1" aria-label="Previous month">‹</button>
                <strong data-month aria-live="polite"></strong>
                <button type="button" data-month-step="1" aria-label="Next month">›</button>
            </div>
            <div class="article-date-grid" aria-hidden="true">
                <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
            </div>
            <div class="article-date-grid" data-days aria-label="Calendar days"></div>
            <p class="small text-body-secondary mb-0 mt-2" data-instruction aria-live="polite"></p>
        </div>
    </div>
</details>

@pushOnce('styles')
    <link rel="stylesheet" href="{{ asset('date-picker.css') }}">
@endPushOnce

@pushOnce('scripts')
    <script src="{{ asset('date-picker.js') }}" defer></script>
@endPushOnce
