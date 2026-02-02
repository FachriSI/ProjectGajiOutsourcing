import mysql.connector
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Connect to database
db = mysql.connector.connect(
    host=os.getenv('DB_HOST', 'localhost'),
    user=os.getenv('DB_USERNAME', 'root'),
    password=os.getenv('DB_PASSWORD', ''),
    database=os.getenv('DB_DATABASE', 'outsourcing_gaji')
)

cursor = db.cursor(dictionary=True)

print("=" * 80)
print("DATA UMP DI DATABASE (kode_lokasi = 12 / Sumbar)")
print("=" * 80)

# Query UMP Sumbar
cursor.execute("""
    SELECT id, tahun, kode_lokasi, ump 
    FROM md_ump 
    WHERE kode_lokasi = '12'
    ORDER BY tahun DESC
""")

umps = cursor.fetchall()

if umps:
    for u in umps:
        print(f"ID: {u['id']} | Tahun: {u['tahun']} | Kode Lokasi: {u['kode_lokasi']} | UMP: Rp {u['ump']:,}")
else:
    print("Tidak ada data UMP Sumbar")

print()
print("=" * 80)
print("DATA NILAI KONTRAK (5 data terakhir)")
print("=" * 80)

# Query nilai kontrak terakhir
cursor.execute("""
    SELECT id, paket_id, periode, ump_sumbar, total_nilai_kontrak
    FROM nilai_kontrak
    ORDER BY created_at DESC
    LIMIT 5
""")

kontraks = cursor.fetchall()

if kontraks:
    for k in kontraks:
        print(f"Paket ID: {k['paket_id']} | Periode: {k['periode']} | UMP: Rp {k['ump_sumbar']:,} | Total Kontrak: Rp {k['total_nilai_kontrak']:,}")
else:
    print("Tidak ada data nilai kontrak")

cursor.close()
db.close()

print()
print("=" * 80)
print("SELESAI")
