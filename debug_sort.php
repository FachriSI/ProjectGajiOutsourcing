$data = [
    (object)['paket' => 'Paket 1'],
    (object)['paket' => 'Paket 10'],
    (object)['paket' => 'Paket 2'],
    (object)['paket' => 'Paket 100'],
];

$collection = collect($data);

echo "Standard Sort:\n";
$sorted = $collection->sortBy('paket')->pluck('paket')->toArray();
echo implode(", ", $sorted) . "\n\n";

echo "Natural Sort:\n";
$sortedNat = $collection->sortBy('paket', SORT_NATURAL)->pluck('paket')->toArray();
echo implode(", ", $sortedNat) . "\n\n";

echo "Regex Sort:\n";
$sortedReg = $collection->sortBy(function($item) {
    preg_match('/(\d+)/', $item->paket, $matches);
    return (int) ($matches[1] ?? 0);
})->pluck('paket')->toArray();
echo implode(", ", $sortedReg) . "\n\n";
