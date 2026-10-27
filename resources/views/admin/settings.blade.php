@extends('layouts.app', ['title' => 'Pengaturan Toko & Printer Thermal &bull; POS Indomaret'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6">
    
    <div>
        <h1 class="text-xl font-bold text-slate-800">Pengaturan Profil Toko & Struk Kasir</h1>
        <p class="text-xs text-slate-500 mt-1">
            Konfigurasi informasi cabang gerai Indomaret, persentase pajak PPN, dan format cetak printer thermal ESC/POS.
        </p>
    </div>

    @if(session('success'))
    <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-medium">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Toko / Cabang</label>
                    <input type="text" name="store_name" required value="{{ old('store_name', $setting->store_name) }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kode Gerai Toko</label>
                    <input type="text" name="store_code" required value="{{ old('store_code', $setting->store_code) }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Lengkap Gerai</label>
                    <textarea name="address" rows="2" class="w-full text-xs p-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">{{ old('address', $setting->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Telepon Toko</label>
                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pajak Pertambahan Nilai / PPN (%)</label>
                    <input type="number" step="0.01" name="tax_percentage" value="{{ old('tax_percentage', $setting->tax_percentage) }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Ketik 0 jika harga jual sudah termasuk pajak / bebas pajak.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lebar Kertas Thermal Struk</label>
                    <select name="printer_paper_width" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                        <option value="58mm" {{ $setting->printer_paper_width === '58mm' ? 'selected' : '' }}>58 mm (Standard POS Mini Printer)</option>
                        <option value="80mm" {{ $setting->printer_paper_width === '80mm' ? 'selected' : '' }}>80 mm (Standard POS Full Width)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Teks Header Struk Belanja</label>
                    <input type="text" name="receipt_header" value="{{ old('receipt_header', $setting->receipt_header) }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Teks Footer Struk Belanja</label>
                    <input type="text" name="receipt_footer" value="{{ old('receipt_footer', $setting->receipt_footer) }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                    Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
