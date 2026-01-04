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
                @foreach($products as $product)
                    <div class="product-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-lg cursor-pointer transition flex flex-col justify-between"
                         data-name="{{ strtolower($product->name) }}" 
                         data-code="{{ strtolower($product->code) }}">
                        
                        <div>
                            <h4 class="font-bold text-gray-700 text-sm mb-1">{{ $product->name }}</h4>
                            <p class="text-xs text-gray-500 mb-2">Stok: {{ $product->stock + 0 }} {{ $product->base_unit }}</p>
                        </div>

                        <div class="space-y-1">
                            @foreach($product->units as $unit)
                                <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $unit->id }}, '{{ $unit->unit_name }}', {{ $unit->price }})"
                                    class="w-full text-xs bg-emerald-100 text-emerald-800 py-1 px-2 rounded hover:bg-emerald-600 hover:text-white transition flex justify-between">
                                    <span>{{ $unit->unit_name }}</span>
                                    <span class="font-bold">{{ number_format($unit->price, 0, ',', '.') }}</span>
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
            <button onclick="resetCart()" class="text-xs bg-red-500 hover:bg-red-600 px-2 py-1 rounded text-white">Reset</button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cartContainer">
            <p class="text-center text-gray-400 text-sm mt-10" id="emptyCartMsg">Keranjang kosong</p>
        </div>

        <div class="p-4 bg-gray-50 border-t rounded-b-lg">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600">Total:</span>
                <span class="text-2xl font-bold text-emerald-700" id="totalDisplay">Rp 0</span>
            </div>
            
            <form action="{{ route('transactions.store') }}" method="POST" id="checkoutForm" onsubmit="return validateCheckout()">
                @csrf
                <input type="hidden" name="cart" id="cartInput">
                <input type="hidden" name="total_amount" id="totalAmountInput">
                <input type="hidden" name="cash_amount" id="cashReal">

                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Uang Tunai (Cash)</label>
                    <input type="text" id="cashDisplay" 
                        class="w-full border rounded px-3 py-2 text-right font-bold text-lg focus:ring-emerald-500 focus:border-emerald-500" 
                        placeholder="Rp 0" required autocomplete="off">
                </div>
                
                <div class="flex justify-between items-center mb-4 text-sm">
                    <span class="text-gray-600">Kembalian:</span>
                    <span class="font-bold text-blue-600 text-lg" id="changeDisplay">Rp 0</span>
                </div>

                <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-3 rounded-lg hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed" id="payButton" disabled>
                    BAYAR SEKARANG
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let cart = [];

    // --- 1. Fungsi Tambah ke Keranjang ---
    function addToCart(productId, productName, unitId, unitName, price) {
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

    // --- 2. Render Keranjang (FIXED) ---
    function renderCart() {
        const container = document.getElementById('cartContainer');
        
        // Bersihkan tampilan lama
        container.innerHTML = '';

        // Jika Keranjang Kosong, Tampilkan Pesan secara Manual
        if (cart.length === 0) {
            // Kita inject HTML pesan kosong langsung di sini agar tidak error null
            container.innerHTML = `<p class="text-center text-gray-400 text-sm mt-10 animate-fadeIn" id="emptyCartMsg">Keranjang kosong</p>`;
            
            updateTotal(0);
            return;
        }

        let total = 0;

        // Loop item keranjang
        cart.forEach((item, index) => {
            const subtotal = item.price * item.qty;
            total += subtotal;

            const html = `
                <div class="flex justify-between items-center border-b pb-2 animate-fadeIn">
                    <div class="w-1/3">
                        <h5 class="font-bold text-gray-700 text-sm line-clamp-1">${item.product_name}</h5>
                        <div class="text-xs text-gray-500">
                            ${item.unit_name} @ ${formatRupiah(item.price)}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 w-1/3 justify-center">
                        <button type="button" onclick="updateQty(${index}, -1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 font-bold">-</button>
                        <span class="text-sm font-bold w-6 text-center">${item.qty}</span>
                        <button type="button" onclick="updateQty(${index}, 1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 font-bold">+</button>
                    </div>
                    <div class="text-sm font-bold text-gray-700 text-right w-1/4">
                        ${formatRupiah(subtotal)}
                    </div>
                    <div class="w-6 text-right">
                        <button type="button" onclick="removeItem(${index})" class="text-red-500 hover:text-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });

        updateTotal(total);
    }

    // --- 3. Update Qty & Hapus Item ---
    function updateQty(index, change) {
        cart[index].qty += change;
        if (cart[index].qty <= 0) {
            removeItem(index); 
        } else {
            renderCart(); 
        }
    }

    // --- 4. Hapus Item Spesifik ---
    function removeItem(index) {
        cart.splice(index, 1); 
        renderCart();          
    }

    // --- 5. Update Total Harga ---
    function updateTotal(amount) {
        document.getElementById('totalDisplay').innerText = formatRupiah(amount);
        document.getElementById('totalAmountInput').value = amount;
        
        const cleanCart = cart.map(item => ({
            product_id: item.product_id,
            unit_id: item.unit_id,
            qty: item.qty
        }));
        document.getElementById('cartInput').value = JSON.stringify(cleanCart);
        
        calcChange();
    }

    // --- 6. Input Cash ---
    const cashDisplay = document.getElementById('cashDisplay');
    const cashReal = document.getElementById('cashReal');

    if(cashDisplay) {
        cashDisplay.addEventListener('input', function(e) {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            if (rawValue === '') rawValue = '0';
            let intValue = parseInt(rawValue, 10);

            cashReal.value = intValue;
            this.value = intValue.toLocaleString('id-ID');
            calcChange();
        });
    }

    // --- 7. Hitung Kembalian ---
    function calcChange() {
        const total = parseInt(document.getElementById('totalAmountInput').value) || 0;
        const c<script>
    let cart = [];

    // --- 1. Fungsi Tambah ke Keranjang ---
    function addToCart(productId, productName, unitId, unitName, price) {
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

    // --- 2. Render Keranjang (Update Tampilan & Total) ---
    function renderCart() {
        const container = document.getElementById('cartContainer');
        const emptyMsg = document.getElementById('emptyCartMsg');
        
        container.innerHTML = ''; // Bersihkan list lama

        if (cart.length === 0) {
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
                <div class="flex justify-between items-center border-b pb-2 animate-fadeIn">
                    <div class="w-1/3">
                        <h5 class="font-bold text-gray-700 text-sm line-clamp-1">${item.product_name}</h5>
                        <div class="text-xs text-gray-500">
                            ${item.unit_name} @ ${formatRupiah(item.price)}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 w-1/3 justify-center">
                        <button type="button" onclick="updateQty(${index}, -1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 font-bold">-</button>
                        <span class="text-sm font-bold w-6 text-center">${item.qty}</span>
                        <button type="button" onclick="updateQty(${index}, 1)" class="w-6 h-6 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 font-bold">+</button>
                    </div>
                    <div class="text-sm font-bold text-gray-700 text-right w-1/4">
                        ${formatRupiah(subtotal)}
                    </div>
                    <div class="w-6 text-right">
                        <button type="button" onclick="removeItem(${index})" class="text-red-500 hover:text-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });

        updateTotal(total);
    }

    // --- 3. Update Qty & Hapus Item ---
    function updateQty(index, change) {
        cart[index].qty += change;
        if (cart[index].qty <= 0) {
            removeItem(index); // Panggil fungsi hapus jika 0
        } else {
            renderCart(); // Render ulang jika cuma ganti angka
        }
    }

    // --- 4. Hapus Item Spesifik (Fixed Realtime Total) ---
    function removeItem(index) {
        cart.splice(index, 1); // Hapus dari array
        renderCart();          // Render ulang & Hitung Total Otomatis
    }

    // --- 5. Update Total Harga ---
    function updateTotal(amount) {
        // Tampilkan Total
        document.getElementById('totalDisplay').innerText = formatRupiah(amount);
        
        // Simpan ke Input Hidden untuk dikirim ke Server
        document.getElementById('totalAmountInput').value = amount;
        
        // Update input cart JSON
        const cleanCart = cart.map(item => ({
            product_id: item.product_id,
            unit_id: item.unit_id,
            qty: item.qty
        }));
        document.getElementById('cartInput').value = JSON.stringify(cleanCart);
        
        // Hitung ulang kembalian karena total berubah
        calcChange();
    }

    // --- 6. Input Cash dengan Format Ribuan (Logic Baru) ---
    const cashDisplay = document.getElementById('cashDisplay');
    const cashReal = document.getElementById('cashReal');

    cashDisplay.addEventListener('input', function(e) {
        // 1. Ambil value mentah, buang semua karakter selain angka
        let rawValue = this.value.replace(/[^0-9]/g, '');
        
        // 2. Jika kosong, set 0
        if (rawValue === '') rawValue = '0';

        // 3. Konversi ke integer untuk logika matematika
        let intValue = parseInt(rawValue, 10);

        // 4. Simpan nilai ASLI (angka murni) ke input hidden
        cashReal.value = intValue;

        // 5. Format tampilan dengan titik ribuan (misal: 10.000)
        // Kita pakai toLocaleString 'id-ID' untuk format Indonesia
        this.value = intValue.toLocaleString('id-ID');

        // 6. Hitung kembalian
        calcChange();
    });

    // --- 7. Hitung Kembalian ---
    function calcChange() {
        const total = parseInt(document.getElementById('totalAmountInput').value) || 0;
        const cash = parseInt(document.getElementById('cashReal').value) || 0; // Ambil dari input hidden
        const btn = document.getElementById('payButton');

        const change = cash - total;

        if (total > 0 && cash >= total) {
            document.getElementById('changeDisplay').innerText = formatRupiah(change);
            document.getElementById('changeDisplay').classList.remove('text-red-500');
            document.getElementById('changeDisplay').classList.add('text-blue-600');
            btn.disabled = false;
        } else {
            if(total > 0) {
                document.getElementById('changeDisplay').innerText = 'Kurang: ' + formatRupiah(Math.abs(change));
                document.getElementById('changeDisplay').classList.add('text-red-500');
                document.getElementById('changeDisplay').classList.remove('text-blue-600');
            } else {
                document.getElementById('changeDisplay').innerText = 'Rp 0';
            }
            btn.disabled = true;
        }
    }

    // --- Helper: Format Rupiah ---
    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    // --- Reset Keranjang ---
    function resetCart() {
        if (cart.length > 0 && confirm('Kosongkan keranjang?')) {
            cart = [];
            renderCart();
            cashDisplay.value = '';
            cashReal.value = 0;
            calcChange();
        }
    }

    // --- Validasi Submit ---
    function validateCheckout() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return false;
        }
        return true;
    }

    // --- Fitur Search ---
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
</script>ash = parseInt(document.getElementById('cashReal').value) || 0;
        const btn = document.getElementById('payButton');

        const change = cash - total;

        if (total > 0 && cash >= total) {
            document.getElementById('changeDisplay').innerText = formatRupiah(change);
            document.getElementById('changeDisplay').classList.remove('text-red-500');
            document.getElementById('changeDisplay').classList.add('text-blue-600');
            btn.disabled = false;
        } else {
            if(total > 0) {
                document.getElementById('changeDisplay').innerText = 'Kurang: ' + formatRupiah(Math.abs(change));
                document.getElementById('changeDisplay').classList.add('text-red-500');
                document.getElementById('changeDisplay').classList.remove('text-blue-600');
            } else {
                document.getElementById('changeDisplay').innerText = 'Rp 0';
            }
            btn.disabled = true;
        }
    }

    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function resetCart() {
        if (cart.length > 0 && confirm('Kosongkan keranjang?')) {
            cart = [];
            renderCart();
            document.getElementById('cashDisplay').value = '';
            document.getElementById('cashReal').value = 0;
            calcChange();
        }
    }

    function validateCheckout() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return false;
        }
        return true;
    }

    // Search Barang
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

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }
</style>
@endsection