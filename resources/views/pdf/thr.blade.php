<!DOCTYPE html>
<html>
<head>
    <title>Cetak THR</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .title {
            font-size: 14px;
            /* text-decoration: underline; */
        }
        .subtitle {
            font-size: 12px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-left: auto;
            margin-right: auto;
        }
        .table-data td {
            padding: 4px;
            border: 1px solid black;
        }
        .label-col {
            width: 35%;
            font-weight: bold;
        }
        .sep-col {
            width: 2%;
            text-align: center;
        }
        .val-col {
            width: 63%;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
        }
        .notes {
            font-size: 10px;
            margin-top: 15px;
        }
        .currency {
            float: left;
        }
        .amount {
            float: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Perhitungan Tagihan Tunjangan Hari Raya Keagamaan (THR) Tahun {{ $nilaiKontrak->tahun }}</div>
        <div class="subtitle">Kontrak Pekerjaan Jasa Penunjang Operasional PT Semen Padang</div>
    </div>

    <table class="table-data" style="width: 80%;">
        <tr>
            <td class="label-col">Nama Perusahaan</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $data['nama_perusahaan'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Paket</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $data['paket'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Unit Kerja</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $data['unit_kerja'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Tagihan</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $data['periode_tagihan'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Jumlah Pekerja Alih Daya</td>
            <td class="sep-col">:</td>
            <td class="val-col">{{ $data['jumlah_pekerja'] }} orang</td>
        </tr>
        <tr>
            <td class="label-col">Nilai THR</td>
            <td class="sep-col">:</td>
            <td class="val-col">
                <span class="currency">Rp</span>
                <span class="amount">{{ number_format($data['nilai_thr'], 0, ',', '.') }}</span>
            </td>
        </tr>
        <tr>
            <td class="label-col">Fee Nilai THR</td>
            <td class="sep-col">:</td>
            <td class="val-col">
                <span class="currency">Rp</span>
                <span class="amount">{{ number_format($data['fee_thr'], 0, ',', '.') }}</span>
            </td>
        </tr>
        <tr style="font-weight: bold;">
            <td class="label-col">TOTAL NILAI THR</td>
            <td class="sep-col">:</td>
            <td class="val-col">
                <span class="currency">Rp</span>
                <span class="amount">{{ number_format($data['total'], 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>


    <div class="notes" style="width: 80%; margin: 0 auto;">
        * Total nilai THR diatas sudah termasuk Fee Pengusaha (sesuai proposal yang disetujui Direksi PT Semen Padang)<br>
        * Permintaan Pembayaran harus ditambahkan PPh dan PPN<br>
        * Karyawan yang berhak menerima THR adalah yang masih aktif bekerja sampai dengan tanggal {{ $data['cutoff_date'] }}<br>
        <br>
        Catatan :<br>
        - Pembayaran THR kepada tenaga kerja sesuai dengan peraturan perundang-undangan yang berlaku.<br>
        - Bukti pembayaran THR kepada tenaga kerja wajib dilampirkan pada tagihan bulan {{ \Carbon\Carbon::parse($data['tanggal_lebaran'])->subMonth()->translatedFormat('F Y') }} yang ditagih pada bulan {{ \Carbon\Carbon::parse($data['tanggal_lebaran'])->translatedFormat('F Y') }}.
    </div>

    <table class="signature-table">
        <tr>
            <td width="33%">
                Disetujui oleh,<br><br><br><br><br>
                (Pimpinan Unit)<br>
                Ka. Unit Kerja
            </td>
            <td width="33%"></td>
            <td width="33%">
                Padang, {{ \Carbon\Carbon::parse($data['tanggal_dokumen'])->translatedFormat('d F Y') }}<br>
                {{ $data['nama_perusahaan'] }}<br><br><br><br>
                ({{ $data['pimpinan_vendor'] }})<br>
                {{ $data['jabatan_vendor'] }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; padding-top: 30px;">
                Mengetahui,<br><br><br><br><br>
                (Verdy Radinal Gusman)<br>
                Ka. Unit Operasional HC
            </td>
        </tr>
    </table>

    @if(isset($data['qr_code']))
        <div style="position: absolute; bottom: 0; right: 0; text-align: center;">
            <img src="data:image/svg+xml;base64,{{ $data['qr_code'] }}" alt="QR Code" width="60"><br>
            <div style="font-size: 8px; margin-top: 5px;">Scan untuk validasi</div>
        </div>
    @endif

</body>
</html>
