@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row gap-6 h-[calc(100vh-150px)]">

        <div class="w-full md:w-2/3 bg-white rounded-lg shadow-md flex flex-col">
            <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                <input type="text" id="searchProduct" placeholder="Cari Nama Barang / Kode..."
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-emerald-500 focus:border-emerald-500 text-lg">
            </div>

            <div class="flex-1 overflow-y-auto p-4" id="productListContainer">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($products as $product)
                        <div class="product-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-lg cursor-pointer transition flex flex-col justify-between"
                            data-name="{{ strtolower($product->name) }}" data-code="{{ strtolower($product->code) }}">

                            <div>
                                <h4 class="font-bold text-gray-700 text-sm mb-1">{{ $product->name }}</h4>
                                <p class="text-xs text-gray-500 mb-2">Stok: {{ $product->stock + 0 }}
                                    {{ $product->base_unit }}</p>
                            </div>

                            <div class="space-y-1">
                                @foreach ($product->units as $unit)
                                    <button
                                        onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $unit->id }}, '{{ $unit->unit_name }}', {{ $unit->price }})"
                                        class="w-full text-xs bg-emerald-100 text-emerald-800 py-1 px-2 rounded hover:bg-emerald-600 hover:text-white transition flex justify-between">
                                        <span>{{ $unit->unit_name }}</span>
                                        <span class="font-bold">Rp {{ number_format($unit->price / 1000, 0) }}k</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <p id="noResult" class="hidden text-center text-gray-400 mt-10">Produk tidak ditemukan.</p>
            </div>
        </div>

        <div class="w-full md:w-1/3 bg-white rounded-lg shadow-md flex flex-col">
            <div class="p-4 border-b bg-emerald-600 text-white rounded-t-lg flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-cart-shopping"></i> Keranjang</h3>
                <button onclick="resetCart()"
                    class="text-xs bg-red-500 hover:bg-red-600 px-2 py-1 rounded text-white">Reset</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cartContainer">
                <p class="text-center text-gray-400 text-sm mt-10" id="emptyCartMsg">Keranjang kosong</p>
            </div>

            <div class="p-4 bg-gray-50 border-t rounded-b-lg">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Total:</span>
                    <span class="text-2xl font-bold text-emerald-700" id="totalDisplay">Rp 0</span>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST" id="checkoutForm"
                    onsubmit="return validateCheckout()">
                    @csrf
                    <input type="hidden" name="cart" id="cartInput">
                    <input type="hidden" name="total_amount" id="totalAmountInput">

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Uang Tunai (Cash)</label>
                        <input type="number" name="cash_amount" id="cashInput"
                            class="w-full border rounded px-3 py-2 text-right font-bold" placeholder="Rp 0" required
                            oninput="calcChange()">
                    </div>

                    <div class="flex justify-between items-center mb-4 text-sm">
                        <span class="text-gray-600">Kembalian:</span>
                        <span class="font-bold text-blue-600" id="changeDisplay">Rp 0</span>
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 text-white font-bold py-3 rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
                        id="payButton" disabled>
                        BAYAR SEKARANG
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- LOGIC KASIR SEDERHANA (Vanilla JS) ---
        let cart = [];

        function addToCart(productId, productName, unitId, unitName, price) {
            // Cek apakah item sudah ada di keranjang
            const existingItem = cart.find(item => item.product_id === productId && item.unit_id === unitId);

            if (existingItem) {
                existingItem.qty += 1;
            } else {
                cart.push({
                    product_id: productId,
                    product_name: productName,
                    unit_id: unitId,
                    unit_name: unitName,
                    price: price,
                    qty: 1
                });
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cartContainer');
            const emptyMsg = document.getElementById('emptyCartMsg');

            container.innerHTML = '';

            if (cart.length === 0) {
                container.appendChild(emptyMsg);
                emptyMsg.style.display = 'block';
                updateTotal(0);
                return;
            } else {
                emptyMsg.style.display = 'none';
            }

            let total = 0;

            cart.forEach((item, index) => {
                const subtotal = item.price * item.qty;
                total += subtotal;

                const html = `
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <h5 class="font-bold text-gray-700 text-sm line-clamp-1">${item.product_name}</h5>
                        <div class="text-xs text-gray-500">
                            ${item.unit_name} x Rp ${new Intl.NumberFormat('id-ID').format(item.price)}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQty(${index}, -1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300">-</button>
                        <span class="text-sm font-bold w-4 text-center">${item.qty}</span>
                        <button onclick="updateQty(${index}, 1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300">+</button>
                    </div>
                    <div class="text-sm font-bold text-gray-700 text-right w-20">
                        ${new Intl.NumberFormat('id-ID').format(subtotal)}
                    </div>
                </div>
            `;
                container.innerHTML += html;
            });

            updateTotal(total);
        }

        function updateQty(index, change) {
            if (cart[index].qty + change <= 0) {
                // Hapus item jika qty jadi 0
                cart.splice(index, 1);
            } else {
                cart[index].qty += change;
            }
            renderCart();
        }

        function updateTotal(amount) {
            document.getElementById('totalDisplay').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            document.getElementById('totalAmountInput').value = amount;

            // Update input hidden cart untuk dikirim ke backend
            // Format ulang array cart agar hanya mengirim data yg dibutuhkan
            const cleanCart = cart.map(item => ({
                product_id: item.product_id,
                unit_id: item.unit_id,
                qty: item.qty
            }));
            document.getElementById('cartInput').value = JSON.stringify(cleanCart);

            calcChange();
        }

        function calcChange() {
            const total = parseFloat(document.getElementById('totalAmountInput').value) || 0;
            const cash = parseFloat(document.getElementById('cashInput').value) || 0;
            const change = cash - total;
            const btn = document.getElementById('payButton');

            if (cash >= total && total > 0) {
                document.getElementById('changeDisplay').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
                btn.disabled = false;
            } else {
                document.getElementById('changeDisplay').innerText = 'Rp 0';
                btn.disabled = true;
            }
        }

        function resetCart() {
            if (confirm('Kosongkan keranjang?')) {
                cart = [];
                renderCart();
                document.getElementById('cashInput').value = '';
            }
        }

        function validateCheckout() {
            if (cart.length === 0) {
                alert('Keranjang masih kosong!');
                return false;
            }
            return true;
        }

        // Fitur Search Barang
        document.getElementById('searchProduct').addEventListener('input', function(e) {
            const keyword = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.product-card');
            let found = false;

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const code = card.getAttribute('data-code');
                if (name.includes(keyword) || code.includes(keyword)) {
                    card.style.display = 'flex';
                    found = true;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('noResult').style.display = found ? 'none' : 'block';
        });
    </script>
@endsection
