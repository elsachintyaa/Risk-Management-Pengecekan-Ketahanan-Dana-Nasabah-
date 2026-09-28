<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gold Risk Monitor</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #020617;
        }

        .card {
            animation: fadeUp .5s ease both;
        }

        .card:nth-child(2) {
            animation-delay: .05s;
        }

        .card:nth-child(3) {
            animation-delay: .1s;
        }

        .card:nth-child(4) {
            animation-delay: .15s;
        }

        .card:nth-child(5) {
            animation-delay: .2s;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-button,
        #buy-button,
        #sell-button {
            transition:
                transform .2s ease,
                border-color .2s ease,
                background-color .2s ease,
                box-shadow .2s ease;
        }

        .product-button:hover,
        #buy-button:hover,
        #sell-button:hover {
            transform: translateY(-2px);
        }

        .product-button:active,
        #buy-button:active,
        #sell-button:active {
            transform: scale(.98);
        }

        input {
            transition:
                border-color .2s ease,
                box-shadow .2s ease;
        }

        input:focus {
            box-shadow: 0 0 0 3px rgba(251, 191, 36, .08);
        }

        /* Hilangkan spinner number input */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        #market-price {
            transition: all .25s ease;
        }

        #market-chart {
            min-height: 176px;
        }
    </style>
</head>


<body class="min-h-screen bg-slate-950 text-white antialiased">

    <main class="mx-auto w-full max-w-2xl px-4 py-6 sm:px-6">

        <!-- HEADER -->
        <header class="card mb-5 flex items-start justify-between">

            <div>
                <p class="text-xs font-medium tracking-widest text-slate-500">
                    CLIENT RISK MONITOR
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight">
                    Gold Risk Check
                </h1>
            </div>

            <div
                class="mt-1 flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-2">

                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>

                <span class="text-xs font-bold text-emerald-400">
                    LIVE
                </span>

            </div>

        </header>


        <!-- MARKET PRICE -->
        <section
            class="card mb-4 rounded-3xl border border-white/10 bg-slate-900 p-5 shadow-xl shadow-black/10">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-xs font-medium tracking-widest text-slate-500">
                        HARGA BERJALAN
                    </p>

                    <div class="mt-2 flex items-baseline gap-2">

                        <span
                            id="market-price"
                            class="text-4xl font-bold tracking-tight sm:text-5xl">
                            0.00
                        </span>

                        <span class="text-sm text-slate-500">
                            USD
                        </span>

                    </div>


                    <!-- PRICE MOVEMENT -->
                    <div class="mt-2 flex items-center gap-2">

                        <span
                            id="price-arrow"
                            class="text-sm font-bold text-slate-400">
                            →
                        </span>

                        <span
                            id="price-change"
                            class="text-xs text-slate-400">
                            0.00
                        </span>

                    </div>

                </div>


                <!-- INSTRUMENT -->
                <div
                    class="rounded-2xl bg-slate-800 px-4 py-3 text-right">

                    <p class="text-[9px] font-medium tracking-widest text-slate-500">
                        INSTRUMENT
                    </p>

                    <p
                        id="instrument-name"
                        class="mt-1 text-sm font-bold text-amber-400">
                        XAU/USD
                    </p>

                </div>

            </div>


            <!-- MARKET STATUS -->
            <p
                id="market-update"
                class="mt-3 text-xs text-slate-500">
                Menghubungkan ke market...
            </p>


            <!-- CANDLESTICK CHART -->
            <div
                class="mt-4 overflow-hidden rounded-2xl border border-white/5 bg-slate-950/80">

                <!-- CHART HEADER -->
                <div
                    class="flex items-center justify-between border-b border-white/5 px-4 py-3">

                    <div>

                        <p class="text-[10px] font-medium tracking-widest text-slate-500">
                            MARKET CHART
                        </p>

                        <p
                            id="chart-symbol"
                            class="mt-1 text-xs font-bold text-slate-300">
                            XAU/USD
                        </p>

                    </div>


                    <!-- LEGEND -->
                    <div
                        class="flex items-center gap-3 text-[9px] text-slate-500">

                        <div class="flex items-center gap-1">
                            <span class="h-2 w-2 rounded-sm bg-emerald-400"></span>
                            NAIK
                        </div>

                        <div class="flex items-center gap-1">
                            <span class="h-2 w-2 rounded-sm bg-red-400"></span>
                            TURUN
                        </div>

                    </div>

                </div>


                <!-- CHART CONTAINER -->
                <div class="w-full px-3 pb-3 pt-2">

                    <div
                        id="market-chart"
                        class="h-44 w-full overflow-hidden">
                    </div>

                </div>

            </div>

        </section>


        <!-- PRODUK -->
        <section
            class="card mb-4 rounded-3xl border border-white/10 bg-slate-900 p-5">

            <div class="mb-5">

                <p class="text-xs font-medium tracking-widest text-slate-500">
                    PRODUK
                </p>

                <h2 class="mt-1 text-lg font-bold">
                    Pilih Produk
                </h2>

            </div>


            <div class="grid grid-cols-3 gap-2">

                <!-- GOLD -->
                <button
                    id="product-gold"
                    type="button"
                    data-product="gold"
                    class="product-button rounded-2xl border border-amber-400 bg-amber-500/15 px-2 py-4 text-center">

                    <div class="text-xl">
                        🥇
                    </div>

                    <div class="mt-2 text-xs font-bold text-amber-400">
                        GOLD
                    </div>

                    <div class="mt-1 text-[9px] text-slate-500">
                        XAU/USD
                    </div>

                </button>


                <!-- HANG SENG -->
                <button
                    id="product-hangseng"
                    type="button"
                    data-product="hangseng"
                    class="product-button rounded-2xl border border-white/10 bg-slate-800 px-2 py-4 text-center">

                    <div class="text-xl">
                        🇭🇰
                    </div>

                    <div class="mt-2 text-xs font-bold">
                        HANG SENG
                    </div>

                    <div class="mt-1 text-[9px] text-slate-500">
                        INDEX
                    </div>

                </button>


                <!-- NIKKEI -->
                <button
                    id="product-nikkei"
                    type="button"
                    data-product="nikkei"
                    class="product-button rounded-2xl border border-white/10 bg-slate-800 px-2 py-4 text-center">

                    <div class="text-xl">
                        🇯🇵
                    </div>

                    <div class="mt-2 text-xs font-bold">
                        NIKKEI
                    </div>

                    <div class="mt-1 text-[9px] text-slate-500">
                        225
                    </div>

                </button>

            </div>

        </section>


        <!-- DATA NASABAH -->
        <section
            class="card mb-4 rounded-3xl border border-white/10 bg-slate-900 p-5">

            <div class="mb-5">

                <p class="text-xs font-medium tracking-widest text-slate-500">
                    DATA NASABAH
                </p>

                <h2 class="mt-1 text-lg font-bold">
                    Input Data
                </h2>

            </div>


            <div class="space-y-4">

                <!-- EQUITY -->
                <div>

                    <label
                        for="equity"
                        class="mb-2 block text-sm font-medium text-slate-300">
                        Equity (USD)
                    </label>

                    <input
                        id="equity"
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        placeholder="Contoh: 10000"
                        class="no-spinner w-full rounded-2xl border border-white/10 bg-slate-900 px-4 py-4 text-white outline-none focus:border-amber-400">

                </div>


                <!-- JUMLAH LOT -->
                <div>

                    <label
                        for="lot"
                        class="mb-2 block text-sm font-medium text-slate-300">
                        Jumlah Lot
                    </label>

                    <input
                        id="lot"
                        type="number"
                        min="1"
                        step="1"
                        inputmode="numeric"
                        placeholder="Contoh: 5"
                        class="no-spinner w-full rounded-2xl border border-white/10 bg-slate-900 px-4 py-4 text-white outline-none focus:border-amber-400">

                </div>


                <!-- MR DAILY -->
                <div>

                    <label
                        for="margin-required"
                        class="mb-2 block text-sm font-medium text-slate-300">
                        MR Daily (USD)
                    </label>

                    <input
                        id="margin-required"
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        placeholder="Contoh: 5000"
                        class="no-spinner w-full rounded-2xl border border-white/10 bg-slate-900 px-4 py-4 text-white outline-none focus:border-amber-400">

                </div>


                <!-- OPEN PRICE -->
                <div>

                    <label
                        for="open-price"
                        class="mb-2 block text-sm font-medium text-slate-300">
                        Open Price
                    </label>

                    <input
                        id="open-price"
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        placeholder="Contoh: 4300"
                        class="no-spinner w-full rounded-2xl border border-white/10 bg-slate-900 px-4 py-4 text-white outline-none focus:border-amber-400">

                </div>


                <!-- BUY / SELL -->
                <div>

                    <label
                        class="mb-2 block text-xs font-medium text-slate-400">
                        Open Position
                    </label>

                    <div class="grid grid-cols-2 gap-2">

                        <!-- BUY -->
                        <button
                            id="buy-button"
                            type="button"
                            class="rounded-2xl border border-emerald-400 bg-emerald-500/20 px-4 py-4 text-sm font-bold text-emerald-400">

                            BUY

                        </button>


                        <!-- SELL -->
                        <button
                            id="sell-button"
                            type="button"
                            class="rounded-2xl border border-white/10 bg-slate-800 px-4 py-4 text-sm font-bold text-slate-400">

                            SELL

                        </button>

                    </div>

                </div>

            </div>

        </section>


        <!-- HASIL PERHITUNGAN -->
        <section
            class="card mb-4 rounded-3xl border border-white/10 bg-slate-900 p-5">

            <div class="mb-5">

                <p class="text-xs font-medium tracking-widest text-slate-500">
                    HASIL PERHITUNGAN
                </p>

                <h2 class="mt-1 text-lg font-bold">
                    Kondisi Dana
                </h2>

            </div>


            <div class="grid grid-cols-2 gap-3">

                <!-- FLOATING P/L -->
                <div class="rounded-2xl border border-white/5 bg-slate-800 p-4">

                    <p class="text-[10px] font-medium tracking-widest text-slate-500">
                        FLOATING P/L
                    </p>

                    <p
                        id="floating-pl"
                        class="mt-2 text-lg font-bold">
                        0.00
                    </p>

                </div>


                <!-- RUNNING EQUITY -->
                <div class="rounded-2xl border border-white/5 bg-slate-800 p-4">

                    <p class="text-[10px] font-medium tracking-widest text-slate-500">
                        RUNNING EQUITY
                    </p>

                    <p
                        id="running-equity"
                        class="mt-2 text-lg font-bold">
                        0.00
                    </p>

                </div>


                <!-- EFFECTIVE MARGIN -->
                <div class="rounded-2xl border border-white/5 bg-slate-800 p-4">

                    <p class="text-[10px] font-medium tracking-widest text-slate-500">
                        EFFECTIVE MARGIN
                    </p>

                    <p
                        id="effective-margin"
                        class="mt-2 text-lg font-bold">
                        0.00
                    </p>

                </div>


                <!-- EQUITY RATIO -->
                <div class="rounded-2xl border border-white/5 bg-slate-800 p-4">

                    <p class="text-[10px] font-medium tracking-widest text-slate-500">
                        EQUITY RATIO
                    </p>

                    <p
                        id="equity-ratio"
                        class="mt-2 text-lg font-bold text-amber-400">
                        0.00%
                    </p>

                </div>

            </div>

        </section>


        <!-- CALL MARGIN -->
        <section
            class="card mb-4 rounded-3xl border border-amber-400/10 bg-slate-900 p-5">

            <div class="mb-5 flex items-start justify-between">

                <div>

                    <p class="text-xs font-medium tracking-widest text-slate-500">
                        CALL MARGIN
                    </p>

                    <h2 class="mt-1 text-lg font-bold">
                        Batas Tidak Aman Pertama
                    </h2>

                </div>

                <div class="rounded-xl bg-amber-500/10 px-3 py-2">

                    <span class="text-xs font-bold text-amber-400">
                        85%
                    </span>

                </div>

            </div>


            <div class="rounded-2xl border border-white/5 bg-slate-800 p-4">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-[10px] text-slate-500">
                            STATUS
                        </p>

                        <p
                            id="call-status"
                            class="mt-1 text-sm font-bold">
                            NORMAL
                        </p>

                    </div>


                    <div class="text-right">

                        <p class="text-[10px] text-slate-500">
                            HARGA CALL MARGIN
                        </p>

                        <p
                            id="call-price"
                            class="mt-1 text-lg font-bold text-amber-400">
                            0.00
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- AUTO LIQUIDATION -->
        <section
            class="card mb-4 rounded-3xl border border-red-400/10 bg-slate-900 p-5">

            <div class="mb-5 flex items-start justify-between">

                <div>

                    <p class="text-xs font-medium tracking-widest text-slate-500">
                        AUTO LIQUIDATION
                    </p>

                    <h2 class="mt-1 text-lg font-bold">
                        Batas Tidak Aman Kedua
                    </h2>

                </div>

                <div class="rounded-xl bg-red-500/10 px-3 py-2">

                    <span class="text-xs font-bold text-red-400">
                        100%
                    </span>

                </div>

            </div>


            <div class="rounded-2xl border border-white/5 bg-slate-800 p-4">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-[10px] text-slate-500">
                            STATUS
                        </p>

                        <p
                            id="liquidation-status"
                            class="mt-1 text-sm font-bold">
                            NORMAL
                        </p>

                    </div>


                    <div class="text-right">

                        <p class="text-[10px] text-slate-500">
                            HARGA AUTO LIQUIDATION
                        </p>

                        <p
                            id="liquidation-price"
                            class="mt-1 text-lg font-bold text-red-400">
                            0.00
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- TAMBAHAN DANA -->
        <section
            class="card mb-4 rounded-3xl border border-white/10 bg-slate-900/80 p-5">

            <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                Tambahan Dana
            </div>

            <div
                id="additional-fund"
                class="text-2xl font-black text-white">
                0.00
            </div>

            <div
                id="additional-fund-message"
                class="mt-2 text-sm text-slate-400">
                Masukkan data nasabah
            </div>

        </section>


        <!-- RISK STATUS -->
        <section
            class="card mb-5 rounded-3xl border border-white/10 bg-slate-900 p-5">

            <div class="flex items-center gap-4">

                <div
                    id="risk-icon"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/10 text-xl">

                    ✓

                </div>


                <div class="min-w-0">

                    <p class="text-[10px] font-medium tracking-widest text-slate-500">
                        FUND RESILIENCE
                    </p>

                    <p
                        id="risk-status"
                        class="mt-1 text-base font-bold">
                        SEHAT / NORMAL
                    </p>

                </div>

            </div>

        </section>


        <!-- FOOTER -->
        <footer
            class="pb-4 text-center text-[10px] text-slate-600">

            Client Risk Monitor • Market data berjalan otomatis

        </footer>

    </main>

</body>

</html>