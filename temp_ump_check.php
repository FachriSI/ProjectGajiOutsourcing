$rows = DB::table('md_ump')
    ->join('md_lokasi','md_lokasi.kode_lokasi','=','md_ump.kode_lokasi')
    ->where('md_lokasi.lokasi', 'like', '%Sumbar%')
    ->where('tahun', 2026)
    ->get();

foreach ($rows as $row) {
    dump((array)$row);
}
