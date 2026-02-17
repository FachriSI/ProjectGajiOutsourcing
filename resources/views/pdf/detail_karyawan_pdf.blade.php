<!DOCTYPE html>
<html>
<head>
    <title>Detail Karyawan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; }
        .section-title { background-color: #eee; padding: 5px; font-weight: bold; border-left: 4px solid #0d6efd; margin-top: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { width: 150px; background-color: #f8f9fa; color: #555; }
        .value { font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Detail Karyawan</h1>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    <div class="section-title">Data Personal</div>
    <table>
        <tr>
            <th>Nama Lengkap</th>
            <td class="value">{{ $dataM->nama_tk }}</td>
        </tr>
        <tr>
            <th>OSIS ID</th>
            <td class="value">{{ $dataM->osis_id }}</td>
        </tr>
        <tr>
            <th>Nomor KTP</th>
            <td class="value">{{ $dataM->ktp }}</td>
        </tr>
        <tr>
            <th>Tempat, Tanggal Lahir</th>
            <td class="value">{{ $dataM->asal }}, {{ $dataM->tanggal_lahir }}</td>
        </tr>
        <tr>
            <th>Jenis Kelamin</th>
            <td class="value">{{ $dataM->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <th>Agama</th>
            <td class="value">{{ ucfirst($dataM->agama) }}</td>
        </tr>
        <tr>
            <th>Status Pernikahan</th>
            <td class="value">
                @if($dataM->status == 'S') Single
                @elseif($dataM->status == 'M') Menikah
                @elseif($dataM->status == 'D') Duda
                @elseif($dataM->status == 'J') Janda
                @else -
                @endif
            </td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td class="value">{{ $dataM->alamat }}</td>
        </tr>
    </table>

    <div class="section-title">Data Pekerjaan</div>
    <table>
        <tr>
            <th>Perusahaan / Vendor</th>
            <td class="value">
                @foreach ($dataP as $item)
                    @if($item->perusahaan_id == $dataM->perusahaan_id)
                        {{ $item->perusahaan }}
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th>Tipe Pekerjaan</th>
            <td class="value">{{ $dataM->tipe_pekerjaan }}</td>
        </tr>
        <tr>
            <th>Tanggal Bekerja</th>
            <td class="value">{{ $dataM->tanggal_bekerja }}</td>
        </tr>
        <tr>
            <th>Tanggal Pensiun</th>
            <td class="value">{{ $dataM->tanggal_pensiun }} (Tahun {{ $dataM->tahun_pensiun }})</td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem.
    </div>
</body>
</html>
