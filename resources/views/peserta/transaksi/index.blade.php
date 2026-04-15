@extends('peserta.layout.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-green-900 tracking-tight">Riwayat Iuran Arisan</h1>
        <p class="text-sm text-gray-400 italic">Pantau dan bayar iuran bulanan Anda di sini.</p>
    </div>

    <div class="space-y-4">
        @forelse($tagihans as $tagihan)
        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-center transition-all hover:shadow-md">
            <div class="flex items-center gap-5">
                <div class="w-12 h-12 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center font-black">
                    {{ substr($tagihan->bulan_iuran, 0, 3) }}
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $tagihan->order_id }}</p>
                    <h3 class="font-bold text-slate-800">{{ $tagihan->bulan_iuran }}</h3>
                    <p class="text-sm font-black text-green-700">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div class="mt-4 md:mt-0">
                @if($tagihan->status_pembayaran == 'sukses')
                    <span class="px-6 py-2 bg-green-100 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">Lunas</span>
                @else
                    <button id="pay-button-{{ $tagihan->id_transaksi }}" onclick="payNow({{ $tagihan->id_transaksi }})" 
                        class="bg-green-800 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-900/20 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Bayar Sekarang
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-20 bg-gray-50 rounded-[40px] border-2 border-dashed border-gray-200">
            <p class="text-gray-400 font-medium">Belum ada tagihan iuran untuk Anda.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Midtrans Snap JS --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

<script>
    function payNow(id) {
        const btn = document.getElementById(`pay-button-${id}`);
        const originalText = btn.innerHTML;

        // 1. Tambahkan Efek Loading
        btn.disabled = true;
        btn.innerHTML = "MEMPROSES...";

        // Ambil Snap Token dari server
        fetch(`/peserta/transaksi/get-token/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) { 
                            location.reload(); 
                        },
                        onPending: function(result) { 
                            location.reload(); 
                        },
                        onError: function(result) { 
                            alert("Pembayaran gagal!"); 
                            // 2. Kembalikan tombol jika error
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        },
                        onClose: function() { 
                            // 3. Kembalikan tombol jika pop-up ditutup
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    });
                } else {
                    alert("Gagal mengambil token.");
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    }
</script>
@endsection