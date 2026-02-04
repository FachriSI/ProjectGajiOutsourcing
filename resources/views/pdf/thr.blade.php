<!DOCTYPE html>
<html>
<head>
    <title>Cetak THR</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .header {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .title {
            font-size: 14px;
            text-decoration: underline;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table-data td {
            padding: 5px;
            border: 1px solid black;
        }
        .table-data tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .label-col {
            width: 30%;
            font-weight: bold;
        }
        .sep-col {
            width: 2%;
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            width: 30%;
            float: right;
            text-align: center;
        }
        .notes {
            font-size: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Nilai Tunjangan Hari Raya Keagamaan (THR) Tahun {{ $nilaiKontrak->tahun }}</div>
        <div>Kontrak Pekerjaan Jasa Penunjang Operasional PT Semen Padang</div>
    </div>

    <table class="table-data">
        <tr>
            <td class="label-col">Nama Perusahaan</td>
            <td class="sep-col">:</td>
            <td>{{ $data['nama_perusahaan'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Paket</td>
            <td class="sep-col">:</td>
            <td>{{ $data['paket'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Periode Tagihan</td>
            <td class="sep-col">:</td>
            <td>{{ $data['periode_tagihan'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Jumlah Pekerja Alih Daya</td>
            <td class="sep-col">:</td>
            <td>{{ $data['jumlah_pekerja'] }} Orang</td>
        </tr>
        <tr>
            <td class="label-col">Unit Kerja</td>
            <td class="sep-col">:</td>
            <td>{{ $data['unit_kerja'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Pekerjaan POS</td>
            <td class="sep-col">:</td>
            <td>{{ $data['pekerjaan_pos'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Nilai THR</td>
            <td class="sep-col">:</td>
            <td>Rp {{ number_format($data['nilai_thr'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label-col">Fee THR</td>
            <td class="sep-col">:</td>
            <td>Rp {{ number_format($data['fee_thr'], 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #ddd;">
            <td class="label-col">TOTAL NILAI THR</td>
            <td class="sep-col">:</td>
            <td>Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="notes">
        * Total nilai THR diatas sudah termasuk Fee Pengusaha (sesuai proposal yang disetujui Direksi PT Semen Padang)<br>
        * Permintaan Pembayaran harus ditambahkan PPh dan PPN<br>
        <br>
        Catatan:<br>
        - Pembayaran THR kepada tenaga kerja sesuai dengan peraturan perundang-undangan yang berlaku.<br>
        - Bukti pembayaran THR kepada tenaga kerja wajib dilampirkan pada tagihan bulan ... {{ $nilaiKontrak->tahun }}.
    </div>

    <div class="footer">
        <div class="signature-box">
            <p>Padang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Unit Operasional HC</p>
            <br><br><br>
            <p style="text-decoration: underline; font-weight: bold;">Verdy Radinal Gusman</p>
            <p>Kepala</p>
        </div>
    </div>

</body>
</html>
