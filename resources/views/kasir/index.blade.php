<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Kios Afgan - Kasir</title>
</head>

<body class="bg-gray-100 h-screen flex flex-col overflow-hidden">
    <!-- Header -->
    <header class="bg-blue-600 text-white p-4 flex justify-between items-center shadow-lg print:hidden">
        <h1 class="text-2xl font-bold">Kios Afgan</h1>
        <div class="text-xl font-mono" id="clock">--:--:--</div>
    </header>

    <style>
        @media print {

            /* Hide everything by default */
            body * {
                visibility: hidden;
                height: 0;
                /* Try to collapse space */
                overflow: hidden;
            }

            /* Unhide the receipt and its children */
            #receipt,
            #receipt * {
                visibility: visible;
                height: auto;
                overflow: visible;
            }

            /* Position the receipt at the top left */
            #receipt {
                display: block !important;
                /* Override Tailwind 'hidden' */
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm !important;
                /* Force width */
                margin: 0;
                padding: 10px;
                /* Minimal padding */
                background-color: white !important;
                color: black !important;
                font-family: 'Courier New', Courier, monospace !important;
                border: none;
                z-index: 9999;
            }

            /* Ensure specific elements like headers/buttons don't mess it up if they leak */
            .print\:hidden {
                display: none !important;
            }

            /* Hide modal backgrounds or overlays if they interfere */
            #successModal,
            .modal-backdrop {
                display: none !important;
            }
        }
    </style>

    <!-- Receipt Template -->
    <div id="receipt" class="hidden bg-white p-4 max-w-[80mm] mx-auto text-sm font-mono leading-tight">
        <div class="text-center mb-4">
            <h2 class="text-xl font-bold uppercase">Kios Afgan</h2>
            <p>Jl. Merdeka No. 123</p>
            <p>Telp: 0812-3456-7890</p>
            <p class="mt-2" id="receiptDate">--/--/---- --:--</p>
        </div>

        <div class="mb-2 border-b-2 border-dashed border-gray-400 pb-2">
            <div class="flex justify-between">
                <span>Invoice:</span>
                <span id="receiptInvoice">#--</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span id="receiptCashier">{{ auth()->guard('kasir')->user()->username ?? 'Kasir' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Metode:</span>
                <span id="receiptMethod">-</span>
            </div>
        </div>

        <table class="w-full mb-2">
            <thead class="border-b border-dashed border-gray-400">
                <tr>
                    <th class="text-left py-1">Item</th>
                    <th class="text-right py-1">Qty</th>
                    <th class="text-right py-1">Hrg</th>
                    <th class="text-right py-1">Sub</th>
                </tr>
            </thead>
            <tbody id="receiptItems">
                <!-- Items injected here -->
            </tbody>
        </table>

        <div class="border-t-2 border-dashed border-gray-400 pt-2 mb-4">
            <div class="flex justify-between font-bold text-lg">
                <span>Total:</span>
                <span id="receiptTotal">Rp 0</span>
            </div>
            <div class="flex justify-between mt-1">
                <span>Bayar:</span>
                <span id="receiptPaid">Rp 0</span>
            </div>
            <div class="flex justify-between mt-1">
                <span>Kembali:</span>
                <span id="receiptChange">Rp 0</span>
            </div>
        </div>

        <div class="text-center text-xs mt-4 border-t border-gray-400 pt-2">
            <p>Terima Kasih</p>
            <p>Powered by Kios Afgan</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col p-4 gap-4">

        <!-- Input Section -->
        <div class="bg-white p-4 rounded shadow">
            <input type="text" id="barcodeInput"
                class="w-full text-2xl p-4 border-2 border-blue-400 rounded focus:outline-none focus:border-blue-600"
                placeholder="Scan Barcode / Cari Produk..." autofocus autocomplete="off">
        </div>

        <!-- Transaction List -->
        <div class="flex-1 bg-white rounded shadow overflow-y-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-200 sticky top-0">
                    <tr>
                        <th class="p-4 text-lg font-semibold border-b">Produk</th>
                        <th class="p-4 text-lg font-semibold border-b w-32">Harga</th>
                        <th class="p-4 text-lg font-semibold border-b w-32 text-center">Qty</th>
                        <th class="p-4 text-lg font-semibold border-b w-40 text-right">Subtotal</th>
                        <th class="p-4 text-lg font-semibold border-b w-16">Item</th>
                    </tr>
                </thead>
                <tbody id="cartTableBody" class="text-lg">
                    <!-- Items will be injected here -->
                </tbody>
            </table>
        </div>

        <!-- Footer / Checkout -->
        <div class="bg-white p-4 rounded shadow border-t-4 border-blue-600">
            <div class="flex justify-between items-center mb-4">
                <span class="text-2xl font-bold text-gray-700">Total:</span>
                <span class="text-5xl font-bold text-blue-700" id="totalDisplay">Rp 0</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <button id="resetBtn"
                    class="bg-red-500 hover:bg-red-600 text-white text-2xl font-bold py-4 rounded shadow transition transform active:scale-95">
                    RESET (F9)
                </button>
                <button id="payBtn"
                    class="bg-green-500 hover:bg-green-600 text-white text-2xl font-bold py-4 rounded shadow transition transform active:scale-95">
                    BAYAR (Enter)
                </button>
            </div>
            <!-- Payment Modal -->
            <div id="paymentModal"
                class="fixed inset-0 bg-black opacity-90 hidden z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl w-3/4 max-w-lg flex flex-col">
                    <div class="p-4 border-b flex justify-between items-center bg-gray-100 rounded-t-lg">
                        <h2 class="text-xl font-bold">Pembayaran</h2>
                        <button onclick="closePaymentModal()"
                            class="text-gray-600 hover:text-red-500 font-bold text-xl">&times;</button>
                    </div>

                    <div class="p-6">
                        <!-- Total Display -->
                        <div class="text-center mb-6">
                            <div class="text-gray-500">Total Tagihan</div>
                            <div class="text-4xl font-bold text-blue-700" id="paymentTotalDisplay">Rp 0</div>
                        </div>

                        <!-- Payment Method Toggle -->
                        <div class="flex gap-4 mb-6">
                            <button onclick="setPaymentMethod('cash')" id="btnCash"
                                class="flex-1 py-3 text-lg font-bold border-2 rounded transition-colors border-blue-600 bg-blue-600 text-white">
                                TUNAI
                            </button>
                            <button onclick="setPaymentMethod('qris')" id="btnQris"
                                class="flex-1 py-3 text-lg font-bold border-2 border-gray-300 text-gray-600 rounded transition-colors hover:border-blue-400">
                                QRIS
                            </button>
                            <button onclick="setPaymentMethod('bon')" id="btnBon"
                                class="flex-1 py-3 text-lg font-bold border-2 border-gray-300 text-gray-600 rounded transition-colors hover:border-blue-400">
                                BON
                            </button>
                        </div>

                        <!-- Cash Input Section -->
                        <div id="cashInputSection">
                            <label class="block text-gray-700 font-bold mb-2">Uang Diterima</label>
                            <input type="number" id="cashReceived"
                                class="w-full text-3xl p-3 border rounded mb-4 text-right focus:border-blue-600 focus:outline-none"
                                placeholder="0">

                            <div class="flex justify-between items-center bg-gray-100 p-4 rounded">
                                <span class="text-xl font-bold text-gray-600">Kembalian:</span>
                                <span class="text-3xl font-bold text-green-600" id="changeDisplay">Rp 0</span>
                            </div>
                        </div>

                        <!-- QRIS Instructions -->
                        <div id="qrisSection" class="hidden text-center py-4 bg-gray-50 rounded mb-4">
                            <p class="text-lg font-bold text-gray-800">Silahkan Scan QRIS</p>
                            <p class="text-sm text-gray-500">Pastikan pembayaran berhasil sebelum menyelesaikan
                                transaksi.</p>
                        </div>

                        <!-- Bon Input Section -->
                        <div id="bonInputSection" class="hidden">
                            <label class="block text-gray-700 font-bold mb-2">Nama Pelanggan / Catatan</label>
                            <input type="text" id="customerName"
                                class="w-full text-xl p-3 border rounded mb-4 focus:border-blue-600 focus:outline-none"
                                placeholder="Masukkan nama pelanggan...">
                            <p class="text-sm text-red-500 italic">* Wajib diisi untuk transaksi Bon</p>
                        </div>
                    </div>

                    <div class="p-4 border-t bg-gray-50 text-right">
                        <button onclick="submitPayment()" id="btnConfirmPayment"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 text-xl rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            SELESAIKAN (Enter)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Selection Modal -->
            <div id="productModal"
                class="fixed inset-0 bg-black opacity-90 hidden flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl w-3/4 max-w-2xl max-h-[80vh] flex flex-col">
                    <div class="p-4 border-b flex justify-between items-center bg-gray-100 rounded-t-lg">
                        <h2 class="text-xl font-bold">Pilih Produk</h2>
                        <button onclick="closeModal()"
                            class="text-gray-600 hover:text-red-500 font-bold text-xl">&times;</button>
                    </div>
                    <div id="productList" class="p-4 overflow-y-auto grid grid-cols-1 gap-2">
                        <!-- Dynamically populated -->
                    </div>
                    <div class="p-4 border-t bg-gray-50 text-right">
                        <button onclick="closeModal()"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal
                            (Esc)</button>
                    </div>
                </div>
            </div>

            <!-- Success Modal -->
            <div id="successModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-[60]">
                <div
                    class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md text-center transform transition-all scale-100 border-t-8 border-green-500">
                    <div class="mb-6">
                        <div
                            class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-4 animate-bounce">
                            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-extrabold text-gray-900 mb-2">Transaksi Berhasil!</h3>
                        <p class="text-gray-500 text-lg">Pesanan telah berhasil direkam.</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-8">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Total Transaksi</span>
                            <span class="text-xl font-bold text-gray-900" id="successTotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Metode Pembayaran</span>
                            <span class="font-bold text-gray-900 uppercase" id="successMethod">-</span>
                        </div>
                        <div id="successChangeInfo" class="flex justify-between items-center hidden border-t pt-2 mt-2">
                            <span class="text-gray-600">Kembalian</span>
                            <span class="text-xl font-bold text-green-600" id="successChange">Rp 0</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button onclick="window.print()"
                            class="w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 px-4 rounded-lg border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Cetak Struk
                            </span>
                        </button>
                        <button onclick="closeSuccessModal()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Transaksi Baru
                        </button>
                    </div>
                </div>
            </div>
            <script>
                // State
                let cart = [];
                const barcodeInput = document.getElementById('barcodeInput');
                const cartTableBody = document.getElementById('cartTableBody');
                const totalDisplay = document.getElementById('totalDisplay');
                const productModal = document.getElementById('productModal');
                const productList = document.getElementById('productList');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Clock
                function updateClock() {
                    const now = new Date();
                    document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID');
                }
                setInterval(updateClock, 1000);
                updateClock();

                // Focus management
                document.body.addEventListener('click', (e) => {
                    if (!productModal.classList.contains('hidden')) return; // Don't steal focus if modal is open
                    barcodeInput.focus();
                });
                barcodeInput.focus();

                // Barcode Listener
                barcodeInput.addEventListener('keydown', async (e) => {
                    if (e.key === 'Enter') {
                        const code = barcodeInput.value.trim();
                        if (code) {
                            await searchItem(code);
                        } else if (cart.length > 0) {
                            processPayment();
                        }
                    }
                });

                // Search Item Logic
                async function searchItem(query) {
                    query = query.trim();
                    if (!query) return;

                    try {
                        const res = await fetch('/kasir/search-product', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ query: query })
                        });

                        if (!res.ok) throw new Error('Produk tidak ditemukan');
                        const result = await res.json();

                        if (result.success) {
                            if (result.action === 'add') {
                                addToCart(result.data);
                                barcodeInput.value = '';
                            } else if (result.action === 'choose') {
                                showProductModal(result.data);
                            }
                        }
                    } catch (err) {
                        alert('Produk tidak ditemukan!');
                        barcodeInput.value = '';
                        barcodeInput.focus();
                    }
                }

                function addToCart(product) {
                    // Check if exist in cart
                    const existing = cart.find(i => i.id === product.id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        cart.unshift({
                            id: product.id,
                            barcode: product.barcode,
                            name: product.name,
                            price: parseFloat(product.price),
                            qty: 1
                        });
                    }
                    renderCart();
                }

                // Modal Logic
                function showProductModal(products) {
                    productList.innerHTML = '';
                    products.forEach((p, index) => {
                        const btn = document.createElement('button');
                        btn.className = "w-full text-left p-4 border rounded hover:bg-blue-100 flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500";
                        btn.innerHTML = `
                    <span class="font-bold text-lg">${p.name}</span>
                    <div class="text-right">
                        <div class="text-blue-600 font-bold">Rp ${parseInt(p.price).toLocaleString('id-ID')}</div>
                        <div class="text-xs text-gray-500">Stok: ${p.stock}</div>
                    </div>
                `;
                        btn.onclick = () => {
                            addToCart(p);
                            closeModal();
                            barcodeInput.value = '';
                            barcodeInput.focus();
                        };

                        // Keyboard navigation
                        btn.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                btn.click();
                            }
                        });

                        productList.appendChild(btn);
                    });

                    productModal.classList.remove('hidden');

                    // Focus first item
                    const firstBtn = productList.querySelector('button');
                    if (firstBtn) firstBtn.focus();
                }

                function closeModal() {
                    productModal.classList.add('hidden');
                    barcodeInput.focus();
                }

                // Render Cart
                function renderCart() {
                    cartTableBody.innerHTML = '';
                    let total = 0;

                    cart.forEach((item, index) => {
                        const subtotal = item.price * item.qty;
                        total += subtotal;

                        const tr = document.createElement('tr');
                        tr.className = 'border-b hover:bg-blue-50';
                        tr.innerHTML = `
                    <td class="p-4">${item.name}</td>
                    <td class="p-4 text-gray-600">Rp ${item.price.toLocaleString('id-ID')}</td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button class="bg-gray-200 px-3 py-1 rounded font-bold" onclick="updateQty(${index}, -1)">-</button>
                            <span>${item.qty}</span>
                            <button class="bg-gray-200 px-3 py-1 rounded font-bold" onclick="updateQty(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td class="p-4 text-right font-bold">Rp ${subtotal.toLocaleString('id-ID')}</td>
                    <td class="p-4 text-center">
                        <button onclick="removeItem(${index})" class="text-red-500 font-bold hover:text-red-700">X</button>
                    </td>
                `;
                        cartTableBody.appendChild(tr);
                    });

                    totalDisplay.innerText = `Rp ${total.toLocaleString('id-ID')}`;
                }

                // Qty Update
                window.updateQty = (index, change) => {
                    if (cart[index].qty + change > 0) {
                        cart[index].qty += change;
                        renderCart();
                    }
                };

                // Remove Item
                window.removeItem = (index) => {
                    cart.splice(index, 1);
                    renderCart();
                };

                // Payment State
                let currentPaymentMethod = 'cash';
                const paymentModal = document.getElementById('paymentModal');
                const paymentTotalDisplay = document.getElementById('paymentTotalDisplay');
                const btnCash = document.getElementById('btnCash');
                const btnQris = document.getElementById('btnQris');
                const cashInputSection = document.getElementById('cashInputSection');
                const qrisSection = document.getElementById('qrisSection');
                const cashReceivedInput = document.getElementById('cashReceived');
                const changeDisplay = document.getElementById('changeDisplay');
                const btnConfirmPayment = document.getElementById('btnConfirmPayment');

                // Open Payment Modal
                document.getElementById('payBtn').addEventListener('click', openPaymentModal);

                function openPaymentModal() {
                    if (cart.length === 0) return;

                    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                    paymentTotalDisplay.innerText = `Rp ${total.toLocaleString('id-ID')}`;

                    // Reset State
                    setPaymentMethod('cash');
                    cashReceivedInput.value = '';
                    changeDisplay.innerText = 'Rp 0';
                    validatePayment();

                    paymentModal.classList.remove('hidden');
                    paymentModal.classList.add('flex'); // Add flex to center

                    setTimeout(() => cashReceivedInput.focus(), 100);
                }

                function closePaymentModal() {
                    paymentModal.classList.add('hidden');
                    paymentModal.classList.remove('flex');
                    barcodeInput.focus();
                }

                const btnBon = document.getElementById('btnBon');
                const bonInputSection = document.getElementById('bonInputSection');
                const customerNameInput = document.getElementById('customerName');

                function setPaymentMethod(method) {
                    currentPaymentMethod = method;

                    // Reset all styles
                    [btnCash, btnQris, btnBon].forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                        btn.classList.add('border-gray-300', 'text-gray-600');
                    });

                    // Hide all sections
                    cashInputSection.classList.add('hidden');
                    qrisSection.classList.add('hidden');
                    bonInputSection.classList.add('hidden');

                    if (method === 'cash') {
                        btnCash.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        btnCash.classList.remove('border-gray-300', 'text-gray-600');
                        cashInputSection.classList.remove('hidden');
                        setTimeout(() => cashReceivedInput.focus(), 100);
                    } else if (method === 'qris') {
                        btnQris.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        btnQris.classList.remove('border-gray-300', 'text-gray-600');
                        qrisSection.classList.remove('hidden');
                    } else if (method === 'bon') {
                        btnBon.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        btnBon.classList.remove('border-gray-300', 'text-gray-600');
                        bonInputSection.classList.remove('hidden');
                        setTimeout(() => customerNameInput.focus(), 100);
                    }
                    validatePayment();
                }

                // Customer Name Validation
                customerNameInput.addEventListener('input', validatePayment);

                function validatePayment() {
                    if (currentPaymentMethod === 'qris') {
                        btnConfirmPayment.disabled = false;
                        return;
                    }

                    if (currentPaymentMethod === 'bon') {
                        btnConfirmPayment.disabled = customerNameInput.value.trim() === '';
                        return;
                    }

                    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                    const received = parseFloat(cashReceivedInput.value) || 0;
                    const change = received - total;

                    changeDisplay.innerText = `Rp ${change.toLocaleString('id-ID')}`;

                    if (change >= 0) {
                        changeDisplay.classList.remove('text-red-500');
                        changeDisplay.classList.add('text-green-600');
                        btnConfirmPayment.disabled = false;
                    } else {
                        changeDisplay.classList.add('text-red-500');
                        changeDisplay.classList.remove('text-green-600');
                        btnConfirmPayment.disabled = true;
                    }
                }

                async function submitPayment() {
                    if (btnConfirmPayment.disabled) return;
                    if (cart.length === 0) return;

                    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                    const received = parseFloat(cashReceivedInput.value) || 0;
                    const change = currentPaymentMethod === 'cash' ? (received - total) : 0;
                    const customerName = currentPaymentMethod === 'bon' ? customerNameInput.value.trim() : null;

                    try {
                        const res = await fetch('{{ route('kasir.transaction') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                items: cart.map(item => ({
                                    id: item.id,
                                    qty: item.qty
                                })),
                                total: total,
                                payment_method: currentPaymentMethod,
                                cash_received: currentPaymentMethod === 'cash' ? received : null,
                                change_amount: change,
                                customer_name: customerName
                            })
                        });

                        const result = await res.json();
                        if (res.ok && result.success) {
                            closePaymentModal();
                            const transactionData = {
                                id: result.transaction_id || '-----',
                                total: total,
                                method: currentPaymentMethod,
                                change: change,
                                received: received,
                                items: [...cart],
                                customer_name: customerName
                            };
                            showSuccessModal(transactionData);
                            cart = [];
                            renderCart();
                        } else {
                            throw new Error(result.message || 'Gagal menyimpan transaksi');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan: ' + err.message);
                    }
                }

                const successModal = document.getElementById('successModal');

                function showSuccessModal(data) {
                    // Update Modal
                    document.getElementById('successTotal').innerText = `Rp ${data.total.toLocaleString('id-ID')}`;

                    let methodLabel = 'TUNAI';
                    if (data.method === 'qris') methodLabel = 'QRIS';
                    if (data.method === 'bon') methodLabel = `BON (${data.customer_name || '-'})`;

                    document.getElementById('successMethod').innerText = methodLabel;

                    const changeInfo = document.getElementById('successChangeInfo');
                    if (data.method === 'cash') {
                        document.getElementById('successChange').innerText = `Rp ${data.change.toLocaleString('id-ID')}`;
                        changeInfo.classList.remove('hidden');
                    } else {
                        changeInfo.classList.add('hidden');
                    }

                    // Populate Receipt
                    document.getElementById('receiptInvoice').innerText = '#' + data.id;
                    document.getElementById('receiptDate').innerText = new Date().toLocaleString('id-ID');
                    document.getElementById('receiptMethod').innerText = data.method.toUpperCase();
                    document.getElementById('receiptTotal').innerText = `Rp ${data.total.toLocaleString('id-ID')}`;

                    if (data.method === 'bon') {
                        document.getElementById('receiptPaid').innerText = 'BELUM LUNAS';
                        document.getElementById('receiptChange').innerText = `Pelanggan: ${data.customer_name}`;
                    } else {
                        document.getElementById('receiptPaid').innerText = data.method === 'cash' ? `Rp ${data.received.toLocaleString('id-ID')}` : `Rp ${data.total.toLocaleString('id-ID')}`;
                        document.getElementById('receiptChange').innerText = `Rp ${data.change.toLocaleString('id-ID')}`;
                    }

                    const receiptItemsBody = document.getElementById('receiptItems');
                    receiptItemsBody.innerHTML = '';
                    data.items.forEach(item => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="py-1 align-top">${item.name}</td>
                            <td class="py-1 align-top text-right">${item.qty}</td>
                            <td class="py-1 align-top text-right">${parseInt(item.price).toLocaleString('id-ID')}</td>
                            <td class="py-1 align-top text-right">${(item.price * item.qty).toLocaleString('id-ID')}</td>
                        `;
                        receiptItemsBody.appendChild(tr);
                    });

                    successModal.classList.remove('hidden');
                    successModal.classList.add('flex');
                }

                function closeSuccessModal() {
                    successModal.classList.add('hidden');
                    successModal.classList.remove('flex');
                    document.getElementById('barcodeInput').focus();
                }

                // Global key listener for modals
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        // ... existing escape logic
                        if (!successModal.classList.contains('hidden')) {
                            closeSuccessModal();
                        }
                    }
                    if (e.key === 'Enter') {
                        if (!successModal.classList.contains('hidden')) {
                            e.preventDefault();
                            closeSuccessModal();
                        }
                    }
                });
                document.getElementById('resetBtn').addEventListener('click', () => {
                    if (confirm('Reset transaksi saat ini?')) {
                        cart = [];
                        renderCart();
                        barcodeInput.focus();
                    }
                });

                // Hotkeys
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'F9') { // Reset
                        e.preventDefault();
                        document.getElementById('resetBtn').click();
                    }
                    if (e.key === 'Escape') {
                        closeModal();
                    }
                });

            </script>
</body>

</html>