@extends('layouts.app')

@section('title', 'Sign in · Angkor Khmer Cuisine')

@section('styles')
<style>
    :root { --brass: #C9A24B; --brass-soft: #E4CE9A; }

    .auth-wrap {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1.1fr 1fr;
    }

    /* ---- Brand panel ---- */
    .auth-brand {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(120% 90% at 15% 0%, #2f5436 0%, transparent 55%),
            linear-gradient(160deg, var(--leaf-dark) 0%, var(--leaf-darker) 100%);
        color: #fff;
        padding: 56px 64px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    /* Faint concentric "plate" motif, purely decorative (static fallback) */
    .auth-brand::after {
        content: "";
        position: absolute;
        right: -160px; bottom: -160px;
        width: 520px; height: 520px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,.06);
        box-shadow:
            0 0 0 60px rgba(255,255,255,.03),
            0 0 0 130px rgba(255,255,255,.02);
        pointer-events: none;
    }
    .auth-brand > * { position: relative; z-index: 1; }

    /* Animated ring overlay — a real element so anime.js can breathe it in/out */
    .auth-ring {
        position: absolute;
        right: -160px; bottom: -160px;
        width: 520px; height: 520px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,.10);
        pointer-events: none;
        z-index: 0;
        transform-origin: center;
    }

    .auth-monogram {
        width: 54px; height: 54px;
        border-radius: 15px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.16);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 20px; letter-spacing: 0;
    }
    .auth-eyebrow {
        text-transform: uppercase;
        letter-spacing: .22em;
        font-size: 11px;
        font-weight: 700;
        color: var(--brass-soft);
        margin-bottom: 18px;
    }
    .auth-headline {
        font-size: clamp(2rem, 3.4vw, 3rem);
        line-height: 1.08;
        font-weight: 800;
        letter-spacing: -.03em;
        text-wrap: balance;
        max-width: 12ch;
        margin: 0;
        color: white;
    }
    .auth-headline em { color: var(--brass-soft); font-style: normal; }
    .auth-sub {
        margin-top: 20px;
        color: rgba(255,255,255,.72);
        max-width: 42ch;
        line-height: 1.6;
    }
    .auth-brand-foot {
        display: flex; gap: 28px;
        color: rgba(255,255,255,.62);
        font-size: 13px;
    }
    .auth-brand-foot .stat b { color: #fff; font-size: 15px; display: block; letter-spacing: -.01em; }
    .auth-rule { width: 40px; height: 2px; background: var(--brass); border: 0; margin: 0 0 22px; opacity: .9; transform-origin: left center; }

    /* ---- Form panel ---- */
    .auth-form-panel {
        display: flex; align-items: center; justify-content: center;
        padding: 40px 28px;
        background: var(--bg);
    }
    .auth-card {
        width: 100%; max-width: 400px;
    }
    .auth-card h1 { font-size: 1.6rem; margin-bottom: 4px; }
    .auth-card .lede { color: var(--muted); margin-bottom: 28px; }

    .auth-demo {
        margin-top: 26px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: #fff;
        overflow: hidden;
    }
    .auth-demo-head {
        padding: 10px 14px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
        color: var(--leaf-dark); background: var(--leaf-tint);
    }
    .auth-demo button {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        width: 100%; text-align: left;
        padding: 10px 14px;
        border: 0; border-top: 1px solid var(--border);
        background: #fff; cursor: pointer;
        font-size: 13.5px; color: var(--ink);
        transition: background-color .12s ease;
    }
    .auth-demo button:first-of-type { border-top: 0; }
    .auth-demo button:hover { background: var(--leaf-tint); }
    .auth-demo .role { font-weight: 700; }
    .auth-demo .email { color: var(--muted); font-family: ui-monospace, monospace; font-size: 12.5px; }
    .auth-demo .use { color: var(--leaf); font-weight: 600; font-size: 12px; opacity: 0; transition: opacity .12s ease; }
    .auth-demo button:hover .use { opacity: 1; }

    /* Filled-by-demo flash state for the inputs (toggled briefly by JS) */
    .auth-card .form-control.js-filled { border-color: var(--leaf); }

    @media (max-width: 860px) {
        .auth-wrap { grid-template-columns: 1fr; }
        .auth-brand { padding: 40px 32px; gap: 32px; }
        .auth-headline { max-width: none; }
        .auth-brand-foot { display: none; }
    }

    /* Everything below starts hidden and is revealed by the anime.js entrance timeline,
       so there's no flash-of-unanimated-content if JS is slow to run. */
    .js-anim-init { opacity: 0; }
</style>
@endsection

@section('content')
<div class="auth-wrap">
    <aside class="auth-brand">
        <div class="auth-ring" id="authRing"></div>

         <div class="d-flex align-items-center gap-3 js-anim-init" data-anim="brand-logo">
            <div class="auth-monogram" id="authMonogram" style="background:transparent;border:0;">
                <svg viewBox="0 0 100 100" width="44" height="44" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="47" fill="none" stroke="var(--brass)" stroke-width="3"/>
                    <circle cx="50" cy="50" r="40" fill="#5A8567"/>
                    <g fill="var(--brass)">
                        <rect x="46" y="30" width="8" height="22"/>
                        <rect x="30" y="38" width="7" height="14"/>
                        <rect x="63" y="38" width="7" height="14"/>
                        <rect x="20" y="44" width="6" height="8"/>
                        <rect x="74" y="44" width="6" height="8"/>
                        <polygon points="50,16 54,26 46,26"/>
                        <polygon points="33.5,26 37,34 30,34"/>
                        <polygon points="66.5,26 70,34 63,34"/>
                        <polygon points="23,32 26,39 20,39"/>
                        <polygon points="77,32 80,39 74,39"/>
                        <rect x="18" y="52" width="64" height="4"/>
                    </g>
                </svg>
            </div>
            <span class="fw-bold" style="letter-spacing:.04em;">ANGKOR <span style="font-weight:500;opacity:.85;">KHMER CUISINE</span></span>
        </div>

        <div>
            <div class="auth-eyebrow js-anim-init" data-anim="brand-eyebrow">Front of house · Kitchen · Register</div>
            <h1 class="auth-headline js-anim-init" data-anim="brand-headline">One clean pass from <em>table to bill</em>.</h1>
            <p class="auth-sub js-anim-init" data-anim="brand-sub">
                Take orders, fire tickets to the line, and settle the check — every
                seat and every plate in one place. Sign in to pick up your shift.
            </p>
        </div>

        <div class="auth-brand-foot">
            <div class="stat js-anim-init" data-anim="brand-stat"><b>Tables</b> live floor map</div>
            <div class="stat js-anim-init" data-anim="brand-stat"><b>Kitchen</b> real-time queue</div>
            <div class="stat js-anim-init" data-anim="brand-stat"><b>Register</b> split & settle</div>
        </div>
    </aside>

    <main class="auth-form-panel">
        <div class="auth-card">
            <hr class="auth-rule js-anim-init" data-anim="card-rule">
            <h1 class="js-anim-init" data-anim="card-title">Welcome back</h1>
            <p class="lede js-anim-init" data-anim="card-lede">Sign in to your Angkor Khmer Cuisine account.</p>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="mb-3 js-anim-init" data-anim="card-field">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus placeholder="you@pos.test">
                </div>
                <div class="mb-3 js-anim-init" data-anim="card-field">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <div class="form-check mb-4 js-anim-init" data-anim="card-field">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Keep me signed in</label>
                </div>
                <button type="submit" class="btn btn-dark w-100 py-2 js-anim-init" id="submitBtn" data-anim="card-field">
                    <span id="submitBtnLabel">Sign in</span>
                </button>
            </form>

            <div class="auth-demo js-anim-init" data-anim="card-demo">
                <div class="auth-demo-head">Demo accounts · password <code style="color:inherit">password</code></div>
                @foreach ([['Admin','admin@pos.test'], ['Manager','manager@pos.test'], ['Staff','staff@pos.test'], ['Chef','chef@pos.test']] as [$role, $email])
                    <button type="button" class="js-demo" data-email="{{ $email }}">
                        <span class="role">{{ $role }}</span>
                        <span class="email">{{ $email }}</span>
                        <span class="use">Use →</span>
                    </button>
                @endforeach
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js"></script>
<script>
(function () {
    /* ---------------------------------------------------------------
       1. Page-load entrance timeline
       --------------------------------------------------------------- */
    const tl = anime.timeline({
        easing: 'easeOutQuad',
        complete: () => {
            // Clear inline opacity/transform once the timeline is done so the
            // elements behave normally afterward (hover states, etc.)
            document.querySelectorAll('.js-anim-init').forEach((el) => el.classList.remove('js-anim-init'));
        }
    });

    tl.add({
        targets: '[data-anim="brand-logo"]',
        opacity: [0, 1],
        translateY: [-10, 0],
        duration: 500,
    })
    .add({
        targets: '[data-anim="brand-eyebrow"]',
        opacity: [0, 1],
        translateY: [14, 0],
        duration: 450,
    }, '-=250')
    .add({
        targets: '[data-anim="brand-headline"]',
        opacity: [0, 1],
        translateY: [18, 0],
        duration: 600,
    }, '-=300')
    .add({
        targets: '[data-anim="brand-sub"]',
        opacity: [0, 1],
        translateY: [14, 0],
        duration: 500,
    }, '-=350')
    .add({
        targets: '[data-anim="brand-stat"]',
        opacity: [0, 1],
        translateY: [12, 0],
        duration: 450,
        delay: anime.stagger(90),
    }, '-=250')
    .add({
        targets: '[data-anim="card-rule"]',
        opacity: [0, 1],
        scaleX: [0, 1],
        duration: 350,
    }, '-=500')
    .add({
        targets: '[data-anim="card-title"]',
        opacity: [0, 1],
        translateY: [12, 0],
        duration: 400,
    }, '-=200')
    .add({
        targets: '[data-anim="card-lede"]',
        opacity: [0, 1],
        translateY: [10, 0],
        duration: 400,
    }, '-=280')
    .add({
        targets: '[data-anim="card-field"]',
        opacity: [0, 1],
        translateY: [14, 0],
        duration: 400,
        delay: anime.stagger(80),
    }, '-=250')
    .add({
        targets: '[data-anim="card-demo"]',
        opacity: [0, 1],
        translateY: [14, 0],
        duration: 450,
    }, '-=200');

    /* ---------------------------------------------------------------
       2. Idle "breathing" ring behind the brand panel, looping forever
       --------------------------------------------------------------- */
    anime({
        targets: '#authRing',
        scale: [1, 1.06, 1],
        opacity: [0.5, 1, 0.5],
        duration: 5200,
        easing: 'easeInOutSine',
        loop: true,
    });

    /* Slow, continuous rotation on the monogram badge */
    anime({
        targets: '#authMonogram',
        rotate: '1turn',
        duration: 22000,
        easing: 'linear',
        loop: true,
    });

    /* ---------------------------------------------------------------
       3. Field focus feedback
       --------------------------------------------------------------- */
    document.querySelectorAll('#loginForm .form-control').forEach((input) => {
        input.addEventListener('focus', () => {
            anime({
                targets: input,
                scale: [1, 1.015],
                duration: 160,
                easing: 'easeOutQuad',
            });
        });
        input.addEventListener('blur', () => {
            anime({
                targets: input,
                scale: [1.015, 1],
                duration: 160,
                easing: 'easeOutQuad',
            });
        });
    });

    /* ---------------------------------------------------------------
       4. Demo account buttons — click to prefill, with feedback
       --------------------------------------------------------------- */
    const emailInput = document.querySelector('input[name=email]');
    const passwordInput = document.querySelector('input[name=password]');

    document.querySelectorAll('.js-demo').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Pulse the clicked row so it's clear which account was chosen.
            anime({
                targets: btn,
                scale: [1, 0.97, 1],
                duration: 220,
                easing: 'easeOutQuad',
            });

            emailInput.value = btn.dataset.email;
            passwordInput.value = 'password';

            [emailInput, passwordInput].forEach((el) => {
                el.classList.add('js-filled');
                anime({
                    targets: el,
                    translateX: [0, -4, 4, -2, 2, 0],
                    duration: 340,
                    easing: 'easeInOutSine',
                    complete: () => el.classList.remove('js-filled'),
                });
            });

            emailInput.focus();
        });
    });

    /* ---------------------------------------------------------------
       5. Submit button loading feedback (form still submits natively)
       --------------------------------------------------------------- */
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnLabel = document.getElementById('submitBtnLabel');

    loginForm.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtnLabel.textContent = 'Signing in…';
        anime({
            targets: submitBtn,
            scale: [1, 0.98, 1],
            duration: 220,
            easing: 'easeOutQuad',
        });
        anime({
            targets: submitBtn,
            opacity: [1, 0.72, 1],
            duration: 700,
            easing: 'easeInOutSine',
            loop: true,
        });
    });
})();
</script>
@endsection