<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Booking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans p-6">
    <div class="max-w-4xl mx-auto">
        {{-- Thông tin người dùng và đăng xuất --}}
        <div class="bg-white p-4 rounded-lg shadow mb-6 flex justify-between items-center text-sm md:text-base">
            @if(auth()->check())
                <div>
                    <span class="text-gray-700">Xin chào:</span>
                    <strong class="text-gray-900">{{ $currentUser->name }}</strong>
                    <span class="ml-1 px-2 py-0.5 text-xs font-semibold uppercase rounded bg-blue-100 text-blue-800">
                            {{ $currentUser->role }}
                        </span> |
                    <span class="text-gray-700">Số dư:</span>
                    <strong class="text-red-600">{{ number_format($currentUser->balance) }} đ</strong>
                </div>
                <div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-bold py-1.5 px-3 rounded transition">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            @else
                <div class="text-gray-600">Chào mừng khách! Vui lòng đăng nhập để thực hiện đặt vé.</div>
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">Đăng ký</a>
                </div>
            @endif
        </div>

        {{-- Thông báo thành công hoặc lỗi --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Nút thêm suất mới chỉ hiển thị cho admin --}}
        @if(auth()->check() && auth()->user()->role === 'admin')
            <div class="mb-6 px-3 flex items-end justify-end">
                <button id="openModalBtn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Thêm Suất Mới
                </button>
            </div>

            <div id="adminModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
                    <span id="closeModalBtn" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold cursor-pointer">&times;</span>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">[Admin] Thêm suất đặt chỗ</h3>

                    <form action="{{ route('slots.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên suất đặt chỗ:</label>
                            <input type="text" name="title" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ví dụ: Phòng Gym Ca Sáng" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số chỗ tối đa:</label>
                            <input type="number" name="max_seats" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ví dụ: 20" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá tiền (đ):</label>
                            <input type="number" name="price" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ví dụ: 150000" required>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded transition shadow">
                            Tạo ngay
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <h2 class="text-xl font-bold text-gray-800 mb-4">Danh sách suất đặt chỗ đang mở</h2>

        {{-- Danh sách các suất đặt chỗ --}}
        <div class="space-y-4">
            @foreach($slots as $slot)
                {{-- Tính giá hiển thị dựa trên role của người dùng --}}
                @php
                    $discountPercent = (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'vip')) ? 0.1 : 0;
                    $discountAmount = $slot['price'] * $discountPercent;
                    $finalPrice = max(0, $slot['price'] - $discountAmount);
                @endphp

                <div class="bg-white p-5 rounded-lg shadow border border-gray-200"
                     x-data="{ openConfirm: false }">

                    {{-- Tiêu đề và thông tin suất --}}
                    <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $slot['title'] }}</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Hạng của bạn: <span class="text-blue-600 font-semibold uppercase">{{ auth()->check() ? $currentUser->role : 'Khách' }}</span> |
                        Chỗ ngồi: <span class="font-semibold text-gray-800">{{ $slot['booked_seats'] }} / {{ $slot['max_seats'] }}</span>

                        @if($discountPercent > 0)
                            <br>
                            Giá gốc: <span class="text-gray-500 line-through">{{ number_format($slot['price']) }} đ</span>
                            <span class="text-xs bg-green-100 text-green-800 px-1.5 py-0.5 rounded font-bold ml-1">-{{ $discountPercent * 100 }}%</span>
                            <br>
                            Giá phải trả: <span class="text-red-600 font-bold text-lg">{{ number_format($finalPrice) }} đ</span>
                        @else
                            <br>
                            Giá: <span class="text-red-600 font-bold text-lg">{{ number_format($finalPrice) }} đ</span>
                        @endif
                    </p>

                    <div class="flex space-x-3 items-center">
                        {{-- Nút hiển thị Pop-up xác nhận giá --}}
                        <button type="button"
                                @click="openConfirm = true"
                                class="font-bold py-2 px-4 rounded text-sm shadow transition {{ $slot['booked_seats'] >= $slot['max_seats'] ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white' }}"
                            {{ $slot['booked_seats'] >= $slot['max_seats'] ? 'disabled' : '' }}>
                            {{ $slot['booked_seats'] >= $slot['max_seats'] ? 'Hết chỗ' : 'Đặt chỗ ngay' }}
                        </button>

                        {{-- Nút sửa dành cho Admin --}}
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <a href="?edit_id={{ $slot['id'] }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded text-sm shadow transition">
                                Sửa
                            </a>
                        @endif
                    </div>

                    {{-- MODAL XÁC NHẬN GIÁ PHẢI TRẢ (Chỉ hiển thị khi openConfirm == true) --}}
                    <div id="confirmModal-{{ $slot['id'] }}"
                         x-show="openConfirm"
                         class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4"
                         x-transition
                         style="display: none;">

                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative" @click.outside="openConfirm = false">
                            <span @click="openConfirm = false" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold cursor-pointer">&times;</span>

                            <h3 class="text-lg font-bold text-gray-900 mb-2">⚠️ Xác nhận thanh toán</h3>
                            <p class="text-sm text-gray-600 mb-4">Bạn đang thực hiện đặt chỗ cho suất: <strong class="text-gray-900">{{ $slot['title'] }}</strong></p>

                            <div class="bg-gray-50 p-4 rounded-lg space-y-2 text-sm border border-gray-100 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Giá gốc:</span>
                                    <span class="font-medium text-gray-700">{{ number_format($slot['price']) }} đ</span>
                                </div>
                                <div class="flex justify-between text-green-700">
                                    <span>Chiết khấu hạng thành viên ({{ auth()->check() ? $currentUser->role : 'Khách' }}):</span>
                                    <span class="font-medium">-{{ number_format($discountAmount) }} đ</span>
                                </div>
                                <hr class="border-gray-200 my-1">
                                <div class="flex justify-between text-base font-bold">
                                    <span class="text-gray-900">Tổng số tiền phải trả:</span>
                                    <span class="text-red-600">{{ number_format($finalPrice) }} đ</span>
                                </div>
                            </div>

                            @if(auth()->check())
                                <div class="text-xs text-gray-500 mb-4 flex justify-between">
                                    <span>Số dư hiện tại của bạn:</span>
                                    <span class="font-bold {{ $currentUser->balance < $finalPrice ? 'text-red-600' : 'text-gray-700' }}">
                            {{ number_format($currentUser->balance) }} đ
                        </span>
                                </div>
                            @endif

                            <div class="flex space-x-3">
                                <button @click="openConfirm = false" type="button" class="w-1/2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 rounded transition text-sm">
                                    Hủy bỏ
                                </button>

                                {{-- Form submit thật lên server --}}
                                <form action="{{ route('slots.book', $slot['id']) }}" method="POST" class="w-1/2">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded transition shadow text-sm {{ (auth()->check() && $currentUser->balance < $finalPrice) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ (auth()->check() && $currentUser->balance < $finalPrice) ? 'disabled' : '' }}>
                                        {{ (auth()->check() && $currentUser->balance < $finalPrice) ? 'Số dư không đủ' : 'Xác nhận đặt' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Form sửa nhanh chỉ hiển thị khi admin --}}
                    @if(isset($editingSlotId) && $editingSlotId == $slot['id'])
                        <div class="mt-4 p-4 bg-amber-50 border border-amber-300 rounded-lg">
                            <strong class="text-amber-800 text-sm block mb-2">[Chế độ sửa nhanh]</strong>
                            <form action="{{ route('slots.update', $slot['id']) }}" method="POST" class="flex flex-wrap gap-2 items-center">
                                @csrf
                                @method('PUT')
                                <input type="text" name="title" value="{{ $slot['title'] }}" class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full sm:w-auto flex-1" required>
                                <input type="number" name="max_seats" value="{{ $slot['max_seats'] }}" class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-20" required>
                                <input type="number" name="price" value="{{ $slot['price'] }}" class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-28" required>

                                <div class="space-x-1 flex w-full sm:w-auto justify-end mt-2 sm:mt-0">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded text-xs shadow transition">Cập nhật</button>
                                    <a href="{{ route('slots.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-1.5 px-3 rounded text-xs shadow transition">Hủy</a>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Script để xử lý mở/đóng popup admin --}}
    <script>
        const modal = document.getElementById("adminModal");
        const openBtn = document.getElementById("openModalBtn");
        const closeBtn = document.getElementById("closeModalBtn");

        if (openBtn && modal && closeBtn) {
            // Mở popup (Xóa class hidden, thêm class flex)
            openBtn.onclick = function() {
                modal.classList.remove("hidden");
            }

            // Đóng popup (Thêm lại class hidden)
            closeBtn.onclick = function() {
                modal.classList.add("hidden");
            }

            // Click ra ngoài đóng popup
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.classList.add("hidden");
                }
            }
        }
    </script>
</body>
</html>
