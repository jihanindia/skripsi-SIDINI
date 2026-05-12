import pandas as pd
import os

# =========================
# 1. LOAD DATA
# =========================
file_path = "C:/laragon/www/screening_preeklampsia/skripsihiu/preklamasia-apps/public/assets/data_coba2.xlsx"

df = pd.read_excel(file_path)

print("===== DATA AWAL =====")
print(df.head())

# =========================
# 2. CEK TIPE DATA
# =========================
print("\n===== TIPE DATA =====")
print(df.dtypes)

# =========================
# 3. CEK NILAI KOSONG (NaN)
# =========================
print("\n===== JUMLAH DATA KOSONG =====")
print(df.isnull().sum())

# =========================
# 4. CEK TOTAL DATA KOSONG
# =========================
total_kosong = df.isnull().sum().sum()
print("\nTotal data kosong:", total_kosong)

# =========================
# 5. TAMPILKAN BARIS YANG ADA KOSONG
# =========================
print("\n===== BARIS YANG MENGANDUNG DATA KOSONG =====")
data_kosong = df[df.isnull().any(axis=1)]
print(data_kosong)

# =========================
# 6. CEK DATA NON NUMERIK (PENYEBAB NaN)
# =========================
print("\n===== CEK DATA NON NUMERIK =====")

for col in df.columns:
    try:
        pd.to_numeric(df[col])
    except:
        print(f"Kolom '{col}' mengandung data non-numerik")

# =========================
# 7. SIMPAN HASIL DATA BERMASALAH (OPSIONAL)
# =========================
if not data_kosong.empty:
    save_path = "data_kosong.xlsx"
    data_kosong.to_excel(save_path, index=False)
    print(f"\nData bermasalah disimpan di: {save_path}")
else:
    print("\nTidak ada data kosong 🎉")