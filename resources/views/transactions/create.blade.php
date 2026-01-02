@extends('layouts.app')

@section('content')
    {{-- CSS Tambahan untuk Scrollbar --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <div class="flex flex-col md:flex-row gap-6 h-[calc(100vh-150px)]">

        <div class="w-full md:w-2/3 flex flex-col">
            <div class="mb-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-search"></i>
                    </span>
                    <input type="text" id="searchProduct" placeholder="Cari nama barang atau scan barcode..."
                        class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-emerald-500 shadow-sm text-lg transition-colors">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4" id="productGrid">
                    @foreach ($products as $product)
                        <div class="product-item bg-white p-4 rounded-lg shadow border hover:shadow-md transition flex flex-col justify-between h-full"
                            data-name="{{ strtolower($product->name) }}" 
                            data-code="{{ strtolower($product->code) }}">

                            <div>
                                <div class="text-xs text-emerald-600 font-bold mb-1">{{ $product->code }}</div>
                                <h3 class="font-bold text-gray-800 leading-tight mb-2">{{ $product->name }}</h3>
                                <div class="text-sm text-gray-500 mb-3">
                                    Stok: <span class="font-semibold {{ $product->stock <= 0 ? 'text-red-500' : 'text-gray-700' }}">
                                        {{ $product->stock + 0 }} {{ $product->base_unit }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2 mt-auto">
                                @foreach ($product->units as $unit)
                                    <button type="button"
                                        onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $unit->id }}, '{{ $unit->unit_name }}', {{ $unit->price }}, {{ $unit->conversion_factor }})"
                                        class="w-full flex justify-between items-center bg-gray-50 hover:bg-emerald-50 border border-gray-200 rounded px-3 py-2 text-sm group transition active:scale-95">
                                        <span class="font-medium text-gray-700 group-hover:text-emerald-700">
                                            Per {{ $unit->unit_name }}
                                        </span>
                                        <span class="font-bold text-emerald-600">
                                            Rp {{ number_format($unit->price, 0, ',', '.') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="emptyState" class="hidden text-center py-10 text-gray-400">
                    <i class="fa-solid fa-box-open text-4xl mb-2"></i>
                    <p>Barang tidak ditemukan</p>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/3 bg-white rounded-lg shadow-lg flex flex-col border border-gray-200 h-full">
            <div class="p-4 bg-emerald-700 text-white rounded-t-lg flex justify-between items-center">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-cart-shopping mr-2"></i> Keranjang</h3>
                <span id="cartCount" class="bg-red-500 text-xs font-bold px-2 py-1 rounded-full">0 Item</span>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar" id="cartItems">
                {{-- Item akan muncul di sini via JS --}}
                <div class="text-center text-gray-400 mt-10" id="emptyCartMessage">
                    <i class="fa-solid fa-basket-shopping text-4xl mb-2 opacity-50"></i>
                    <p>Belum ada barang dipilih</p>
                </div>
            </div>

            <div class="p-4 border-t bg-gray-50 rounded-b-lg">
                <div class="flex justify-between items-center mb-2 text-gray-600">
                    <span>Total Item</span>
                    <span id="totalQty" class="font-bold">0</span>
                </div>
                <div class="flex justify-between items-center mb-4 text-xl font-bold text-gray-800">
                    <span>Total Bayar</span>
                    <span id="totalPrice" class="text-emerald-600">Rp 0</span>
                </div>

                <form id="checkoutForm" action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cart_data" id="cartDataInput">

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <input type="number" id="cashInput" name="cash_amount" class="border p-2 rounded w-full focus:ring-2 focus:ring-emerald-500 outline-none"
                            placeholder="Uang Tunai (Rp)">
                        </div>
                        <div class="flex items-center justify-end text-sm font-bold text-gray-500 bg-white border rounded px-2" id="changeAmount">
                            Kembali: Rp 0
                        </div>
                    </div>

                    <button type="button" onclick="processCheckout()" id="btnPay" disabled
                        class="w-full bg-emerald-600 text-white font-bold py-3 rounded-lg hover:bg-emerald-700 shadow-lg transform transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        BAYAR SEKARANG
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- 0. HELPER FUNCTIONS ---
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // --- 1. LOGIC PENCARIAN (REALTIME) ---
        document.getElementById('searchProduct').addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.product-item');
            let visibleCount = 0;

            items.forEach(function(item) {
                let name = item.getAttribute('data-name');
                let code = item.getAttribute('data-code');

                if (name.includes(filter) || code.includes(filter)) {
                    item.style.display = "flex";
                    visibleCount++;
                } else {
                    item.style.display = "none";
                }
            });

            const emptyState = document.getElementById('emptyState');
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        });

        // --- 2. LOGIC KERANJANG (CART) ---
        let cart = [];
        let currentGrandTotal = 0;

        function addToCart(productId, productName, unitId, unitName, price, factor) {
            // Cek barang sama
            const existingItem = cart.find(item => item.product_id === productId && item.unit_id === unitId);

            if (existingItem) {
                existingItem.qty += 1;
            } else {
                cart.push({
                    product_id: productId,
                    product_name: productName,
                    unit_id: unitId,
                    unit_name: unitName,
                    price: parseFloat(price),
                    qty: 1,
                    conversion_factor: factor
                });
            }
            
            renderCart();
            
            // Auto scroll ke bawah keranjang
            const cartContainer = document.getElementById('cartItems');
            setTimeout(() => {
                cartContainer.scrollTop = cartContainer.scrollHeight;
            }, 50);
        }

        function renderCart() {
            const cartContainer = document.getElementById('cartItems');
            const cartCount = document.getElementById('cartCount');
            const totalQtyEl = document.getElementById('totalQty');
            const totalPriceEl = document.getElementById('totalPrice');

            // Reset UI dulu
            cartContainer.innerHTML = '';
            let totalQty = 0;
            let grandTotal = 0;

            if (cart.length === 0) {
                // Tampilkan pesan kosong
                cartContainer.innerHTML = `
                    <div class="text-center text-gray-400 mt-10">
                        <i class="fa-solid fa-basket-shopping text-4xl mb-2 opacity-50"></i>
                        <p>Belum ada barang dipilih</p>
                    </div>`;
            } else {
                // Loop cart items
                cart.forEach((item, index) => {
                    const subtotal = item.price * item.qty;
                    totalQty += item.qty;
                    grandTotal += subtotal;

                    // Saya hapus class 'animate-fade-in' agar tidak ada risiko item hidden
                    const itemHtml = `
                    <div class="flex justify-between items-start bg-gray-50 p-2 rounded border border-gray-100 mb-2">
                        <div class="flex-1">
                            <div class="font-bold text-sm text-gray-700 line-clamp-2">${item.product_name}</div>
                            <div class="text-xs text-emerald-600 font-medium">Satuan: ${item.unit_name}</div>
                            <div class="text-xs text-gray-500">@Rp ${formatRupiah(item.price)}</div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-1">
                            <button type="button" onclick="removeFromCart(${index})" class="text-red-400 hover:text-red-600 text-xs font-semibold">
                                <i class="fa-solid fa-trash-can"></i> Hapus
                            </button>
                            
                            <div class="flex items-center bg-white border rounded shadow-sm">
                                <button type="button" onclick="updateQty(${index}, -1)" class="px-2 py-1 text-gray-500 hover:bg-gray-100 hover:text-red-500 transition">-</button>
                                <input type="number" value="${item.qty}" onchange="manualQty(${index}, this.value)" 
                                    class="w-10 text-center text-sm font-bold border-none focus:ring-0 p-0 h-6 text-gray-700">
                                <button type="button" onclick="updateQty(${index}, 1)" class="px-2 py-1 text-emerald-600 hover:bg-emerald-50 transition">+</button>
                            </div>

                            <div class="font-bold text-sm text-gray-800">Rp ${formatRupiah(subtotal)}</div>
                        </div>
                    </div>`;
                    
                    cartContainer.insertAdjacentHTML('beforeend', itemHtml);
                });
            }

            // Update Text Ringkasan
            cartCount.innerText = `${totalQty} Item`;
            totalQtyEl.innerText = totalQty;
            totalPriceEl.innerText = `Rp ${formatRupiah(grandTotal)}`;

            // Update variabel global Total
            currentGrandTotal = grandTotal;

            // Hitung ulang kembalian (penting agar kalau dihapus, tombol bayar disable lagi)
            calculateChange();
        }

        function updateQty(index, change) {
            if (cart[index].qty + change <= 0) {
                if (confirm('Hapus barang ini dari keranjang?')) {
                    removeFromCart(index);
                }
            } else {
                cart[index].qty += change;
                renderCart();
            }
        }

        function manualQty(index, value) {
            let newVal = parseFloat(value);
            if (newVal <= 0 || isNaN(newVal)) {
                cart[index].qty = 1;
            } else {
                cart[index].qty = newVal;
            }
            renderCart();
        }

        function removeFromCart(index) {
            cart.splice(index, 1); // Hapus dari array
            renderCart(); // Render ulang agar tampilan bersih
        }

        // --- 3. LOGIC PEMBAYARAN & KEMBALIAN ---
        document.getElementById('cashInput').addEventListener('input', calculateChange);

        function calculateChange() {
            let cashInput = parseFloat(document.getElementById('cashInput').value) || 0;
            let changeEl = document.getElementById('changeAmount');
            let btn = document.getElementById('btnPay');

            // Jika keranjang kosong (Total 0)
            if (currentGrandTotal === 0) {
                changeEl.innerText = 'Keranjang Kosong';
                changeEl.className = "flex items-center justify-end text-sm font-bold text-gray-400 bg-gray-100 border rounded px-2 w-full h-full";
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            // Jika Uang Cukup
            if (cashInput >= currentGrandTotal) {
                let change = cashInput - currentGrandTotal;
                changeEl.innerText = `Kembali: Rp ${formatRupiah(change)}`;
                changeEl.className = "flex items-center justify-end text-sm font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded px-2 w-full h-full";
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                // Jika Uang Kurang
                let kurang = currentGrandTotal - cashInput;
                changeEl.innerText = `Kurang: Rp ${formatRupiah(kurang)}`;
                changeEl.className = "flex items-center justify-end text-sm font-bold text-red-500 bg-red-50 border border-red-200 rounded px-2 w-full h-full";
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        function processCheckout() {
            if (cart.length === 0) {
                alert('Keranjang belanja masih kosong!');
                return;
            }

            let cash = parseFloat(document.getElementById('cashInput').value) || 0;
            if (cash < currentGrandTotal) {
                alert('Uang pembayaran kurang!');
                return;
            }

            document.getElementById('cartDataInput').value = JSON.stringify(cart);
            document.getElementById('checkoutForm').submit();
        }
    </script>
@endsection