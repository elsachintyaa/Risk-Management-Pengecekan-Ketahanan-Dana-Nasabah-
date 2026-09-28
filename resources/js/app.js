let selectedProduct = 'gold';
let selectedPosition = 'BUY';

let currentMarketPrice = 0;
let previousMarketPrice = 0;

const products = {
    gold: {
        name: 'GOLD',
        symbol: 'XAU/USD',
        contractSize: 100
    },

    hangseng: {
        name: 'HANG SENG',
        symbol: 'HSI',
        contractSize: 5
    },

    nikkei: {
        name: 'NIKKEI',
        symbol: 'N225',
        contractSize: 5
    }
};


// ==========================================
// PILIH PRODUK
// ==========================================

document.querySelectorAll('.product-button').forEach(button => {

    button.addEventListener('click', () => {

        selectedProduct = button.dataset.product;

        document.querySelectorAll('.product-button').forEach(item => {

            item.classList.remove(
                'border-amber-400',
                'bg-amber-500/15'
            );

            item.classList.add(
                'border-white/10',
                'bg-slate-800'
            );
        });

        button.classList.remove(
            'border-white/10',
            'bg-slate-800'
        );

        button.classList.add(
            'border-amber-400',
            'bg-amber-500/15'
        );

        currentMarketPrice = 0;
        previousMarketPrice = 0;

        updateMarketPrice();
        updateChart();
        calculateRisk();
    });

});


// ==========================================
// BUY
// ==========================================

document.getElementById('buy-button')
    .addEventListener('click', () => {

        selectedPosition = 'BUY';

        document.getElementById('buy-button').className =
            'rounded-2xl border border-emerald-400 bg-emerald-500/20 px-4 py-4 text-sm font-bold text-emerald-400';

        document.getElementById('sell-button').className =
            'rounded-2xl border border-white/10 bg-slate-800 px-4 py-4 text-sm font-bold text-slate-400';

        calculateRisk();
    });


// ==========================================
// SELL
// ==========================================

document.getElementById('sell-button')
    .addEventListener('click', () => {

        selectedPosition = 'SELL';

        document.getElementById('sell-button').className =
            'rounded-2xl border border-red-400 bg-red-500/20 px-4 py-4 text-sm font-bold text-red-400';

        document.getElementById('buy-button').className =
            'rounded-2xl border border-white/10 bg-slate-800 px-4 py-4 text-sm font-bold text-slate-400';

        calculateRisk();
    });


// ==========================================
// HARGA MARKET
// ==========================================

async function updateMarketPrice() {

    try {

        const response = await fetch(
            `/api/market/price?product=${selectedProduct}`
        );

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message);
        }

        const newPrice = Number(result.price);

        if (currentMarketPrice !== 0) {
            previousMarketPrice = currentMarketPrice;
        }

        currentMarketPrice = newPrice;

        document.getElementById('market-price').textContent =
            formatNumber(currentMarketPrice);

        document.getElementById('market-update').textContent =
            'LIVE • Harga berjalan';

        updatePriceMovement();

        calculateRisk();

    } catch (error) {

        console.error(error);

        document.getElementById('market-update').textContent =
            'Market tidak tersedia';
    }
}


// ==========================================
// PANAH NAIK / TURUN
// ==========================================

function updatePriceMovement() {

    const arrow =
        document.getElementById('price-arrow');

    const change =
        document.getElementById('price-change');

    if (!arrow || !change || previousMarketPrice === 0) {
        return;
    }

    const difference =
        currentMarketPrice - previousMarketPrice;

    const percentage =
        (difference / previousMarketPrice) * 100;


    if (difference > 0) {

        arrow.textContent = '↑';

        arrow.className =
            'text-sm font-bold text-emerald-400';

        change.textContent =
            '+' + formatNumber(difference) +
            ' (' + percentage.toFixed(2) + '%)';

        change.className =
            'text-xs font-medium text-emerald-400';

    }

    else if (difference < 0) {

        arrow.textContent = '↓';

        arrow.className =
            'text-sm font-bold text-red-400';

        change.textContent =
            formatNumber(difference) +
            ' (' + percentage.toFixed(2) + '%)';

        change.className =
            'text-xs font-medium text-red-400';

    }

    else {

        arrow.textContent = '→';

        arrow.className =
            'text-sm font-bold text-slate-400';

        change.textContent = '0.00';

        change.className =
            'text-xs text-slate-400';
    }
}


// ==========================================
// KALKULASI RISIKO
// ==========================================

function calculateRisk()
{
    const equity =
        Number(
            document.getElementById('equity')?.value
        );

    const marginRequired =
        Number(
            document.getElementById('margin-required')?.value
        );

    const openPrice =
        Number(
            document.getElementById('open-price')?.value
        );

    const lot =
        Number(
            document.getElementById('lot')?.value
        );

    if (
        !equity ||
        !marginRequired ||
        !openPrice ||
        !lot ||
        !currentMarketPrice
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | CONTRACT SIZE
    |--------------------------------------------------------------------------
    */

    const contractSize =
        products[selectedProduct].contractSize;


    /*
    |--------------------------------------------------------------------------
    | FLOATING P/L
    |--------------------------------------------------------------------------
    */

    let floatingPL;

    if (selectedPosition === 'BUY') {

        floatingPL =
            (currentMarketPrice - openPrice) *
            lot *
            contractSize;

    } else {

        floatingPL =
            (openPrice - currentMarketPrice) *
            lot *
            contractSize;
    }


    /*
    |--------------------------------------------------------------------------
    | RUNNING EQUITY
    |--------------------------------------------------------------------------
    */

    const runningEquity =
        equity + floatingPL;


    /*
    |--------------------------------------------------------------------------
    | EQUITY RATIO
    |--------------------------------------------------------------------------
    */

    const equityRatio =
        (runningEquity / marginRequired) * 100;


    /*
    |--------------------------------------------------------------------------
    | TARGET 350%
    |--------------------------------------------------------------------------
    */

    const targetEquity =
        marginRequired * 3.5;


    /*
    |--------------------------------------------------------------------------
    | TAMBAHAN DANA
    |--------------------------------------------------------------------------
    */

    const additionalFund =
        Math.max(
            0,
            targetEquity - runningEquity
        );


    /*
    |--------------------------------------------------------------------------
    | STATUS KESEHATAN
    |--------------------------------------------------------------------------
    */

    let healthStatus;
    let healthMessage;

    if (equityRatio >= 350) {

        healthStatus =
            'SEHAT / MAX';

        healthMessage =
            'Equity Ratio sudah mencapai batas sehat 350%. Tidak perlu tambahan dana.';

    } else if (equityRatio > 150) {

        healthStatus =
            'BURUK';

        healthMessage =
            'Disarankan menambah dana agar Equity Ratio kembali ke 350%.';

    } else {

        healthStatus =
            'SANGAT BURUK';

        healthMessage =
            'Dana perlu segera ditambah agar Equity Ratio kembali ke 350%.';
    }


    /*
    |--------------------------------------------------------------------------
    | CALL MARGIN & AUTO LIQUIDATION
    |--------------------------------------------------------------------------
    */

    const callLoss =
        equity * 0.85;

    const liquidationLoss =
        equity;

    const callDistance =
        callLoss /
        (lot * contractSize);

    const liquidationDistance =
        liquidationLoss /
        (lot * contractSize);

    let callPrice;
    let liquidationPrice;

    if (selectedPosition === 'BUY') {

        callPrice =
            openPrice - callDistance;

        liquidationPrice =
            openPrice - liquidationDistance;

    } else {

        callPrice =
            openPrice + callDistance;

        liquidationPrice =
            openPrice + liquidationDistance;
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS CALL / LIQUIDATION
    |--------------------------------------------------------------------------
    */

    let isCallMargin = false;
    let isLiquidation = false;

    if (selectedPosition === 'BUY') {

        isCallMargin =
            currentMarketPrice <= callPrice;

        isLiquidation =
            currentMarketPrice <= liquidationPrice;

    } else {

        isCallMargin =
            currentMarketPrice >= callPrice;

        isLiquidation =
            currentMarketPrice >= liquidationPrice;
    }


    /*
    |--------------------------------------------------------------------------
    | FLOATING P/L
    |--------------------------------------------------------------------------
    */

    const floatingElement =
        document.getElementById('floating-pl');

    if (floatingElement) {

        floatingElement.textContent =
            formatNumber(floatingPL);
    }


    /*
    |--------------------------------------------------------------------------
    | RUNNING EQUITY
    |--------------------------------------------------------------------------
    */

    const runningEquityElement =
        document.getElementById('running-equity');

    if (runningEquityElement) {

        runningEquityElement.textContent =
            formatNumber(runningEquity);
    }


    /*
    |--------------------------------------------------------------------------
    | EQUITY RATIO
    |--------------------------------------------------------------------------
    */

    const equityRatioElement =
        document.getElementById('equity-ratio');

    if (equityRatioElement) {

        equityRatioElement.textContent =
            formatPercent(equityRatio);
    }


    /*
    |--------------------------------------------------------------------------
    | ADDITIONAL FUND
    |--------------------------------------------------------------------------
    */

    const additionalFundElement =
        document.getElementById('additional-fund');

    if (additionalFundElement) {

        additionalFundElement.textContent =
            '$' +
            formatNumber(additionalFund);
    }


    /*
    |--------------------------------------------------------------------------
    | ADDITIONAL FUND MESSAGE
    |--------------------------------------------------------------------------
    */

    const additionalFundMessage =
        document.getElementById(
            'additional-fund-message'
        );

    if (additionalFundMessage) {

        if (equityRatio >= 350) {

            additionalFundMessage.textContent =
                'Dana sudah dalam kondisi sehat. Tidak perlu tambahan dana.';

        } else {

            additionalFundMessage.textContent =
                'Perlu tambahan dana agar Equity Ratio kembali ke 350%.';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CALL PRICE
    |--------------------------------------------------------------------------
    */

    const callPriceElement =
        document.getElementById('call-price');

    if (callPriceElement) {

        callPriceElement.textContent =
            formatNumber(callPrice);
    }


    /*
    |--------------------------------------------------------------------------
    | LIQUIDATION PRICE
    |--------------------------------------------------------------------------
    */

    const liquidationPriceElement =
        document.getElementById('liquidation-price');

    if (liquidationPriceElement) {

        liquidationPriceElement.textContent =
            formatNumber(liquidationPrice);
    }


    /*
    |--------------------------------------------------------------------------
    | CALL / LIQUIDATION STATUS
    |--------------------------------------------------------------------------
    */

    const callStatus =
        document.getElementById('call-status');

    const liquidationStatus =
        document.getElementById('liquidation-status');


    if (isLiquidation) {

        if (callStatus) {
            callStatus.textContent =
                'TERLEWATI';
        }

        if (liquidationStatus) {
            liquidationStatus.textContent =
                'AUTO LIQUIDATION';
        }

    } else if (isCallMargin) {

        if (callStatus) {
            callStatus.textContent =
                'CALL MARGIN';
        }

        if (liquidationStatus) {
            liquidationStatus.textContent =
                'NORMAL';
        }

    } else {

        if (callStatus) {
            callStatus.textContent =
                'NORMAL';
        }

        if (liquidationStatus) {
            liquidationStatus.textContent =
                'NORMAL';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | OVERALL RISK STATUS
    |--------------------------------------------------------------------------
    */

    const riskStatus =
        document.getElementById('risk-status');

    if (riskStatus) {

        if (isLiquidation) {

            riskStatus.textContent =
                'AUTO LIQUIDATION';

        } else if (isCallMargin) {

            riskStatus.textContent =
                'CALL MARGIN';

        } else {

            riskStatus.textContent =
                healthStatus;
        }
    }
}


// ==========================================
// CHART
// ==========================================

async function updateChart() {

    const chart =
        document.getElementById('market-chart');

    if (!chart) return;


    try {

        const response = await fetch(
            `/api/market/chart?product=${selectedProduct}`
        );

        const result = await response.json();


        if (
            !result.success ||
            !result.values ||
            result.values.length < 2
        ) {

            chart.innerHTML = `
                <div class="flex h-full items-center justify-center text-xs text-slate-500">
                    Data chart belum tersedia
                </div>
            `;

            return;
        }


        const candles = result.values
            .map(item => ({
                open: Number(item.open),
                high: Number(item.high),
                low: Number(item.low),
                close: Number(item.close),
                datetime: item.datetime
            }))
            .filter(item =>
                Number.isFinite(item.open) &&
                Number.isFinite(item.high) &&
                Number.isFinite(item.low) &&
                Number.isFinite(item.close)
            );


        if (candles.length < 2) return;


        // =====================================
        // UKURAN CHART
        // =====================================

        const width = 700;
        const height = 240;

        const leftPadding = 8;
        const rightPadding = 8;
        const topPadding = 10;
        const bottomPadding = 10;


        // =====================================
        // RANGE HARGA
        // =====================================

        const highest =
            Math.max(...candles.map(c => c.high));

        const lowest =
            Math.min(...candles.map(c => c.low));

        const range =
            highest - lowest || 1;


        // =====================================
        // POSISI HARGA
        // =====================================

        function priceToY(price) {

            return (
                topPadding +
                (
                    (highest - price) / range
                ) *
                (
                    height -
                    topPadding -
                    bottomPadding
                )
            );
        }


        // =====================================
        // LEBAR CANDLE
        // =====================================

        const chartWidth =
            width -
            leftPadding -
            rightPadding;

        const candleSpace =
            chartWidth / candles.length;

        const candleWidth =
            Math.max(
                4,
                Math.min(
                    14,
                    candleSpace * 0.58
                )
            );


        // =====================================
        // GRID
        // =====================================

        let svg = `
            <svg
                viewBox="0 0 ${width} ${height}"
                preserveAspectRatio="none"
                class="h-full w-full">

                <g
                    stroke="rgba(148,163,184,0.08)"
                    stroke-width="1">
        `;


        // horizontal grid

        for (let i = 1; i < 5; i++) {

            const y =
                topPadding +
                (
                    i / 5
                ) *
                (
                    height -
                    topPadding -
                    bottomPadding
                );

            svg += `
                <line
                    x1="${leftPadding}"
                    y1="${y}"
                    x2="${width - rightPadding}"
                    y2="${y}" />
            `;
        }


        svg += `</g>`;


        // =====================================
        // CANDLESTICKS
        // =====================================

        candles.forEach((candle, index) => {

            const centerX =
                leftPadding +
                (
                    index + 0.5
                ) *
                candleSpace;


            const openY =
                priceToY(candle.open);

            const closeY =
                priceToY(candle.close);

            const highY =
                priceToY(candle.high);

            const lowY =
                priceToY(candle.low);


            const bodyTop =
                Math.min(
                    openY,
                    closeY
                );

            const bodyBottom =
                Math.max(
                    openY,
                    closeY
                );


            const bodyHeight =
                Math.max(
                    2,
                    bodyBottom - bodyTop
                );


            const isBullish =
                candle.close >= candle.open;


            const bodyColor =
                isBullish
                    ? '#34d399'
                    : '#f87171';


            const wickColor =
                isBullish
                    ? '#6ee7b7'
                    : '#fca5a5';


            // WICK

            svg += `
                <line
                    x1="${centerX}"
                    y1="${highY}"
                    x2="${centerX}"
                    y2="${lowY}"
                    stroke="${wickColor}"
                    stroke-width="1.2"
                    stroke-linecap="round" />
            `;


            // BODY

            svg += `
                <rect
                    x="${centerX - candleWidth / 2}"
                    y="${bodyTop}"
                    width="${candleWidth}"
                    height="${bodyHeight}"
                    rx="1"
                    fill="${bodyColor}" />
            `;
        });


        svg += `</svg>`;


        chart.innerHTML = svg;


        // =====================================
        // UPDATE SYMBOL
        // =====================================

        const symbolElement =
            document.getElementById('chart-symbol');

        if (symbolElement) {

            symbolElement.textContent =
                products[selectedProduct].symbol;
        }


    } catch (error) {

        console.error(
            'Chart error:',
            error
        );

        chart.innerHTML = `
            <div class="flex h-full items-center justify-center text-xs text-slate-500">
                Chart tidak tersedia
            </div>
        `;
    }
}

// ==========================================
// FORMAT
// ==========================================

function formatNumber(value) {

    return Number(value).toLocaleString('en-US', {

        minimumFractionDigits: 2,

        maximumFractionDigits: 2
    });
}


function formatPercent(value) {

    return Number(value).toLocaleString('en-US', {

        minimumFractionDigits: 2,

        maximumFractionDigits: 2

    }) + '%';
}


// ==========================================
// INPUT
// ==========================================

[
    'equity',
    'margin-required',
    'open-price',
    'lot'
].forEach(id => {

    document
        .getElementById(id)
        ?.addEventListener('input', calculateRisk);

});


// ==========================================
// START
// ==========================================

updateMarketPrice();

updateChart();


// Harga update setiap 3 detik
setInterval(updateMarketPrice, 60000);


// Chart update setiap 60 detik
setInterval(updateChart, 60000);