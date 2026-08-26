@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('styles')
<style>
    .menu-scroll { max-height: 66vh; overflow-y: auto; padding-right: 4px; }
    .cat-label {
        display: flex; align-items: center; gap: 8px;
        font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
        color: var(--muted); margin: 18px 0 10px;
    }
    .cat-label::after { content: ""; flex: 1; height: 1px; background: var(--border); }

    .dish {
        border: 1px solid var(--border); border-radius: var(--radius);
        background: #fff; overflow: hidden; height: 100%;
        display: flex; flex-direction: column;
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }
    .dish:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--leaf-tint-strong); }
    .dish-media { position: relative; height: 132px; background: var(--leaf-tint); }
    .dish-media img { width: 100%; height: 100%; object-fit: cover; }
    .dish-media .noimg {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        color: var(--leaf-dark); opacity: .4;
    }
    .dish-price {
        position: absolute; top: 8px; right: 8px;
        background: rgba(31,57,36,.92); color: #fff;
        font-weight: 700; font-size: 12.5px; letter-spacing: -.01em;
        padding: 3px 9px; border-radius: 999px;
        font-variant-numeric: tabular-nums;
    }
    .dish-body { padding: 10px 12px 12px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
    .dish-name { font-weight: 700; font-size: 14px; line-height: 1.25; }
    .dish-desc { color: var(--muted); font-size: 12px; line-height: 1.4; margin-top: 2px; }

    /* Quantity stepper (menu grid) */
    .stepper { display: flex; align-items: center; margin-top: auto; gap: 8px; }
    .qty { display: inline-flex; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; }
    .qty button {
        width: 30px; border: 0; background: #fff; color: var(--ink);
        font-size: 16px; line-height: 1; cursor: pointer;
    }
    .qty button:hover { background: var(--leaf-tint); }
    .qty input {
        width: 38px; border: 0; text-align: center; font-weight: 600;
        -moz-appearance: textfield; font-variant-numeric: tabular-nums;
    }
    .qty input::-webkit-outer-spin-button, .qty input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    /* Order ticket */
    .ticket-line {
        display: flex; align-items: flex-start; gap: 10px; padding: 12px 8px; margin: 0 -8px;
        border-top: 1px solid var(--border); border-radius: 10px;
        overflow: hidden; /* required so the height-collapse removal animation doesn't clip abruptly */
    }
    .ticket-line:first-child { border-top: 0; }

    .qty-stepper {
        flex-shrink: 0; width: 34px; display: flex; flex-direction: column; align-items: stretch;
        border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; background: #fff;
    }
    .qty-btn {
        border: 0; background: #fff; color: var(--leaf-dark); height: 22px;
        display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 13px;
    }
    .qty-btn:hover { background: var(--leaf-tint); }
    .qty-btn:disabled { opacity: .5; cursor: default; }

    .qty-num {
        text-align: center; font-weight: 700; font-size: 13px;
        height: 22px; line-height: 22px; padding: 0;
        border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
        font-variant-numeric: tabular-nums;
    }
    .ticket-qty {
        flex-shrink: 0; min-width: 30px; height: 30px; padding: 0 6px;
        border-radius: 8px; background: var(--leaf-tint); color: var(--leaf-dark);
        font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center;
        font-variant-numeric: tabular-nums;
    }
    .btn-icon {
        border: 0; background: transparent; color: var(--danger);
        width: 26px; height: 26px; padding: 0;
        display: flex; align-items: center; justify-content: center;
        border-radius: 8px; line-height: 1; font-size: 15px;
    }
    .btn-icon:hover { background: rgba(179, 69, 46, .1); }

    /* --- Two-panel layout: CSS grid, so overflowing content inside a panel
       (e.g. the horizontally-scrolling dish grid) can never push the column
       widths around the way it can with flex-basis percentages. --- */
    .order-layout {
        display: grid;
        grid-template-columns: minmax(0, 58fr) minmax(0, 42fr);
        align-items: start;
        gap: 1.5rem;
        width: 100%;
    }
    .order-layout .panel-menu,
    .order-layout .panel-ticket {
        min-width: 0; /* required so grid children don't grow to fit their content */
        width: 100%;
    }
    /* Stack on narrow screens so the menu grid doesn't get crushed */
    @media (max-width: 767.98px) {
        .order-layout { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="mb-1">{{ $order->diningTable->name }} <span class="text-muted fw-normal">· Order #{{ $order->id }}</span></h3>
        <span class="badge {{ match($order->status) {
    'open' => 'bg-secondary',
    'sent_to_kitchen', 'accepted', 'preparing' => 'bg-warning',
    'finished' => 'bg-info',
    'served' => 'bg-success',
    'paid' => 'bg-dark',
    default => 'bg-secondary',
} }} text-uppercase" style="letter-spacing:.04em;">{{ str_replace('_',' ', $order->status) }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.tables.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Tables</a>
        @if(in_array($order->status, ['served','finished']))
            <a href="{{ route('staff.orders.pay', $order) }}" class="btn btn-success btn-sm"><i class="bi bi-cash-coin"></i> Bill / Pay</a>
        @endif
    </div>
</div>

<div class="order-layout">
    <div class="panel-menu">
        @php($menuEditable = in_array($order->status, ['open', 'sent_to_kitchen']))
        @if($menuEditable)
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Menu</span>
                    <span class="text-muted small fw-normal">
                        @if($order->status === 'open')
                            Adding the same dish bumps its quantity
                        @else
                            <i class="bi bi-lightning-charge-fill text-warning"></i> Extra items fire straight to the kitchen
                        @endif
                    </span>
                </div>
                <div class="card-body menu-scroll" id="menu-grid">
                    @forelse($categories as $category)
                        @if($category->menuItems->isNotEmpty())
                            <div class="cat-label">{{ $category->name }} <span class="badge bg-light text-dark border">{{ $category->station }}</span></div>
                            <div class="row g-3 mb-2">
                                @foreach($category->menuItems as $menuItem)
                                    <div class="col-sm-6 col-xl-4">
                                        <form method="POST" action="{{ route('staff.orders.items.store', $order) }}" class="dish">
                                            @csrf
                                            <input type="hidden" name="menu_item_id" value="{{ $menuItem->id }}">
                                            <div class="dish-media">
                                                @if($menuItem->image)
                                                    <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}">
                                                @else
                                                    <span class="noimg"><i class="bi bi-egg-fried" style="font-size:2rem;"></i></span>
                                                @endif
                                                <span class="dish-price">${{ number_format($menuItem->price, 2) }}</span>
                                            </div>
                                            <div class="dish-body">
                                                <div>
                                                    <div class="dish-name">{{ $menuItem->name }}</div>
                                                    @if($menuItem->description)
                                                        <div class="dish-desc">{!! \Illuminate\Support\Str::limit(strip_tags($menuItem->description, '<b><strong><i><em><u><ul><ol><li><br><p>'), 60) !!}</div>
                                                    @endif
                                                    @if($menuItem->allergy_info)
                                                        <div class="small text-danger fw-semibold">
                                                            <i class="bi bi-exclamation-triangle-fill"></i> {{ $menuItem->allergy_info }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="stepper">
                                                    <div class="qty">
                                                        <button type="button" onclick="this.nextElementSibling.stepDown()">&minus;</button>
                                                        <input type="number" name="quantity" value="1" min="1" max="99">
                                                        <button type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                                                    </div>
                                                    <button class="btn btn-dark btn-sm flex-fill"><i class="bi bi-plus-lg"></i> Add</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @empty
                        <p class="text-muted mb-0">No categories available.</p>
                    @endforelse
                </div>
            </div>
        @elseif(! in_array($order->status, ['paid', 'cancelled']))
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                <i class="bi bi-lock-fill fs-5"></i>
                <div>
                    <div class="fw-semibold">Menu locked</div>
                    <div class="small">The kitchen is already working on this order, so items can no longer be added or removed. Start a new order for the table if the guest needs something else.</div>
                </div>
            </div>
        @else
            <div class="alert alert-secondary">This order is {{ $order->status }} and can no longer be changed.</div>
        @endif
    </div>

    <div class="panel-ticket">
        <div class="card shadow-sm" style="position: sticky; top: 20px;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Current Order</span>
                <span class="text-muted small fw-normal" id="order-item-count">{{ $order->items->sum('quantity') }} item{{ $order->items->sum('quantity') === 1 ? '' : 's' }}</span>
            </div>
            <div class="card-body" id="order-ticket-body">
                @forelse($order->items as $item)
                    <div class="ticket-line"
                         data-item-id="{{ $item->id }}"
                         data-menu-item-id="{{ $item->menu_item_id }}"
                         data-notes="{{ $item->notes }}"
                         data-price="{{ $item->price }}"
                         data-qty="{{ $item->quantity }}">
                        @if($menuEditable)
                            <div class="qty-stepper">
                                <button type="button" class="qty-btn" data-dir="up" aria-label="Increase quantity"><i class="bi bi-plus"></i></button>
                                <span class="qty-num" data-role="qty">{{ $item->quantity }}</span>
                                <button type="button" class="qty-btn" data-dir="down" aria-label="Decrease quantity"><i class="bi bi-dash"></i></button>
                            </div>
                        @else
                            <span class="ticket-qty">{{ $item->quantity }}&times;</span>
                        @endif
                        <div class="flex-grow-1">
                            <div class="ticket-name">{{ $item->menuItem->name }}</div>
                            @if($item->notes)<div class="ticket-note"><i class="bi bi-sticky-fill"></i> {{ $item->notes }}</div>@endif
                            <div class="text-muted small">${{ number_format($item->price, 2) }} each</div>
                        </div>
                        <div class="text-end">
                            <div class="ticket-sub" data-role="subtotal">${{ number_format($item->subtotal(), 2) }}</div>
                            @if($menuEditable)
                                <form method="POST" action="{{ route('staff.orders.items.destroy', [$order, $item]) }}" data-confirm="Remove {{ $item->menuItem->name }}?" class="mt-1">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon" title="Remove"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-basket" style="font-size:2rem; opacity:.35;"></i>
                        <p class="mb-0 mt-2 small">No items yet — pick from the menu.</p>
                    </div>
                @endforelse

                @if($order->items->isNotEmpty())
                    <div class="ticket-total">
                        <span class="text-uppercase small fw-bold text-muted" style="letter-spacing:.06em;">Total</span>
                        <span class="amt" id="order-total-amt">${{ number_format($order->total, 2) }}</span>
                    </div>
                @endif
            </div>

            <div class="card-body border-top d-grid gap-2">
                @if($order->status === 'open')
                    <form method="POST" action="{{ route('staff.orders.send', $order) }}">
                        @csrf
                        <button class="btn btn-dark w-100 py-2" id="send-to-kitchen-btn" {{ $order->items->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-send"></i> Send Order to Kitchen
                        </button>
                    </form>
                @elseif($order->status === 'finished')
                    <form method="POST" action="{{ route('staff.orders.serve', $order) }}">
                        @csrf
                        <button class="btn btn-success w-100 py-2"><i class="bi bi-check2-circle"></i> Mark as Served</button>
                    </form>
                @elseif(in_array($order->status, ['sent_to_kitchen','accepted','preparing']))
                    <div class="alert alert-warning mb-0 text-center">Waiting on the kitchen: <strong>{{ str_replace('_',' ', $order->status) }}</strong></div>
                @elseif($order->status === 'served')
                    <div class="alert alert-success mb-0 text-center">Served — ready to bill.</div>
                @elseif($order->status === 'paid')
                    <div class="alert alert-secondary mb-0 text-center">Paid in full.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js"></script>
<script>
(function () {
    const ticketBody = document.getElementById('order-ticket-body');
    const menuGrid = document.getElementById('menu-grid');
    const totalEl = document.getElementById('order-total-amt');
    const countEl = document.getElementById('order-item-count');
    const sendBtn = document.getElementById('send-to-kitchen-btn');
    if (!ticketBody) return;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const ADD_URL = "{{ route('staff.orders.items.store', $order) }}";
    const REMOVE_URL = (id) => "{{ route('staff.orders.items.destroy', [$order, '__ID__']) }}".replace('__ID__', id);
    const ORDER_URL = "{{ route('staff.orders.show', $order) }}";
    let syncSeq = 0;

    const money = (n) => '$' + n.toFixed(2);

    function recalcLocal() {
        let total = 0, count = 0;
        ticketBody.querySelectorAll('.ticket-line').forEach((line) => {
            const qty = parseInt(line.dataset.qty, 10) || 0;
            const price = parseFloat(line.dataset.price) || 0;
            total += qty * price;
            count += qty;
            const qtyEl = line.querySelector('[data-role="qty"]');
            const subEl = line.querySelector('[data-role="subtotal"]');
            if (qtyEl) {
                qtyEl.textContent = qty;
                anime({ targets: qtyEl, scale: [1.3, 1], duration: 200, easing: 'easeOutQuad' });
            }
            if (subEl) {
                subEl.textContent = money(qty * price);
                anime({ targets: subEl, scale: [1.15, 1], duration: 200, easing: 'easeOutQuad' });
            }
        });

        if (totalEl) {
            const from = parseFloat(totalEl.dataset.raw || totalEl.textContent.replace('$', '')) || 0;
            totalEl.dataset.raw = total;
            anime({
                targets: { val: from },
                val: total,
                duration: 350,
                easing: 'easeOutQuad',
                round: 100,
                update: (a) => { totalEl.textContent = money(a.animations[0].currentValue); }
            });
        }
        if (countEl) countEl.textContent = count + ' item' + (count === 1 ? '' : 's');
        if (sendBtn) sendBtn.disabled = ticketBody.querySelectorAll('.ticket-line').length === 0;
    }

    function request(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: body || new URLSearchParams(),
        });
    }

    async function syncTicket() {
        const mySeq = ++syncSeq;
        try {
            const res = await fetch(ORDER_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            if (mySeq !== syncSeq) return;
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const freshBody = doc.getElementById('order-ticket-body');
            const freshCount = doc.getElementById('order-item-count');
            if (freshCount && countEl) countEl.textContent = freshCount.textContent;
            if (!freshBody) return;

            const key = (el) => (el.dataset.menuItemId || '') + '::' + (el.dataset.notes || '');
            const freshLines = Array.from(freshBody.querySelectorAll('.ticket-line'));
            const consumed = new Set();

            // Update existing lines in place — never move them — matched by dish + notes,
            // not by server id (which changes when a line is recreated on qty change).
            ticketBody.querySelectorAll('.ticket-line').forEach((line) => {
                const match = freshLines.find((fl) => key(fl) === key(line));
                if (!match) { line.remove(); return; }
                consumed.add(match);
                // Copy every data-* attribute (item id, qty, price, notes) from the server's
                // current version of this line, then swap the inner markup wholesale so the
                // delete form's action="..." always points at the live item id.
                Array.from(match.attributes).forEach((attr) => {
                    if (attr.name.startsWith('data-')) line.setAttribute(attr.name, attr.value);
                });
                line.innerHTML = match.innerHTML;
            });

            // Brand-new lines (added from the menu grid) get appended at the end, in order,
            // sliding + fading in so they don't just pop into place.
            freshLines.forEach((fl) => {
                if (!consumed.has(fl)) {
                    const clone = fl.cloneNode(true);
                    clone.style.opacity = 0;
                    ticketBody.appendChild(clone);
                    anime({
                        targets: clone,
                        opacity: [0, 1],
                        translateX: [16, 0],
                        duration: 320,
                        easing: 'easeOutQuad'
                    });
                }
            });

            // Refresh the "no items" placeholder / TOTAL footer (non-line children), kept last.
            Array.from(ticketBody.children)
                .filter((el) => !el.classList.contains('ticket-line'))
                .forEach((el) => el.remove());
            Array.from(freshBody.children)
                .filter((el) => !el.classList.contains('ticket-line'))
                .forEach((el) => ticketBody.appendChild(el.cloneNode(true)));

            if (sendBtn) sendBtn.disabled = ticketBody.querySelectorAll('.ticket-line').length === 0;
        } catch (e) { /* keep optimistic UI if the background sync fails */ }
    }

    ticketBody.addEventListener('click', async function (e) {
        const btn = e.target.closest('.qty-btn');
        if (!btn) return;
        const line = btn.closest('.ticket-line');
        const itemId = line.dataset.itemId;
        const notes = line.dataset.notes || '';
        let qty = (parseInt(line.dataset.qty, 10) || 0) + (btn.dataset.dir === 'up' ? 1 : -1);

        line.querySelectorAll('.qty-btn').forEach((b) => (b.disabled = true));

        if (qty <= 0) {
            line.dataset.qty = 0;
            anime({
                targets: line,
                opacity: [1, 0],
                translateX: [0, -16],
                height: [line.offsetHeight, 0],
                paddingTop: 0,
                paddingBottom: 0,
                duration: 260,
                easing: 'easeInQuad',
                complete: () => line.remove()
            });
            recalcLocal();
            await request(REMOVE_URL(itemId), new URLSearchParams({ _method: 'DELETE' }));
            await syncTicket();
            return;
        }

        line.dataset.qty = qty;
        recalcLocal();

        await request(REMOVE_URL(itemId), new URLSearchParams({ _method: 'DELETE' }));
        await request(ADD_URL, new URLSearchParams({ menu_item_id: line.dataset.menuItemId, quantity: qty, notes }));
        await syncTicket();
    });

    if (menuGrid) {
        menuGrid.addEventListener('submit', async function (e) {
            const form = e.target.closest('form.dish');
            if (!form) return;
            e.preventDefault();
            anime({
                targets: form,
                scale: [1, 1.04, 1],
                duration: 260,
                easing: 'easeOutQuad'
            });
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            await request(form.action, new FormData(form));
            await syncTicket();
            if (btn) btn.disabled = false;
        });
    }
})();
</script>
@endsection