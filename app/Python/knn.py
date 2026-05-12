import pandas as pd
import sys
import json
from sklearn.model_selection import train_test_split, KFold, cross_val_score
from sklearn.neighbors import KNeighborsClassifier
from sklearn.preprocessing import MinMaxScaler
from sklearn.metrics import accuracy_score
from sklearn.pipeline import Pipeline

# =========================
# 1. LOAD DATA (CSV)
# =========================
if len(sys.argv) < 2:
    print(json.dumps({"error": "Path file CSV wajib diberikan sebagai argumen."}))
    sys.exit(1)

file_path = sys.argv[1]

try:
    df = pd.read_csv(file_path)
except Exception as e:
    print(json.dumps({"error": f"Gagal membaca file CSV: {str(e)}"}))
    sys.exit(1)

# =========================
# 2. HAPUS KOLOM TIDAK PERLU
# =========================
# Jangan drop 'no' dan 'nama' dari df agar datanya tetap terkirim ke Laravel.
# Akan didrop nanti saat membuat X (matriks fitur).

# =========================
# 3. CLEANING
# =========================
df.columns = df.columns.str.strip().str.lower()

for col in df.columns:
    if df[col].dtype == 'object':
        df[col] = df[col].astype(str).str.strip()

# Validasi kolom target
if "status" not in df.columns:
    print(json.dumps({"error": "Kolom 'status' tidak ditemukan dalam dataset"}))
    sys.exit(1)



# =========================
# 4.5 FREQUENCY ENCODING
# =========================
if "riw_ht_keluarga" in df.columns:
    freq_riw = df['riw_ht_keluarga'].value_counts(normalize=True)
    df['riw_ht_keluarga_freq'] = df['riw_ht_keluarga'].map(freq_riw)

if "protein_urin" in df.columns:
    freq_protein = df['protein_urin'].value_counts(normalize=True)
    df['protein_urin_freq'] = df['protein_urin'].map(freq_protein)
elif "protein_urine" in df.columns:
    freq_protein = df['protein_urine'].value_counts(normalize=True)
    df['protein_urine_freq'] = df['protein_urine'].map(freq_protein)

# =========================
# 5. DROP NULL
# =========================
df = df.dropna().reset_index(drop=True)

# =========================
# 6. SPLIT DATA
# =========================
cols_to_drop = ["status"]
for c in ["no", "nama", "riw_ht_keluarga", "protein_urin"]:
    if c in df.columns:
        cols_to_drop.append(c)

X = df.drop(columns=cols_to_drop, errors="ignore").values
y = df["status"].values

X_train, X_test, y_train, y_test, idx_train, idx_test = train_test_split(
    X, y, df.index, test_size=0.10, random_state=42
)

# =========================
# 7. TUNING K (K-FOLD + PIPELINE)
# =========================
k_values = [3, 5, 7, 9, 11]
best_k = 0
best_cv_acc = 0
results_k = []

kf = KFold(n_splits=5, shuffle=True, random_state=42)

for k in k_values:
    pipeline = Pipeline([
        ('scaler', MinMaxScaler()),
        ('knn', KNeighborsClassifier(n_neighbors=k))
    ])
    
    scores = cross_val_score(pipeline, X_train, y_train, cv=kf)
    acc = scores.mean()
    
    results_k.append({"k": k, "cv_accuracy": round(acc * 100, 2)})

    if acc > best_cv_acc:
        best_cv_acc = acc
        best_k = k

# =========================
# 8. EVALUASI FINAL (TEST SET)
# =========================
final_pipeline = Pipeline([
    ('scaler', MinMaxScaler()),
    ('knn', KNeighborsClassifier(n_neighbors=best_k))
])

final_pipeline.fit(X_train, y_train)
y_pred = final_pipeline.predict(X_test)

test_acc = accuracy_score(y_test, y_pred)

# =========================
# 8.5 PREDIKSI UNTUK SELURUH DATA
# =========================
full_predictions = final_pipeline.predict(X)
df["prediksi_knn"] = full_predictions

# =========================
# 9. OUTPUT TERMINAL
# =========================
print(f"Hasil Terbaik Diperoleh Pada K: {best_k}")
print(f"Nilai Akurasi Test: {round(test_acc * 100, 2)}%")
print("\n===== DATA TEST YANG DIPAKAI (10%) =====")
print(df.iloc[idx_test].to_string())
print("========================================\n")

# =========================
# 10. OUTPUT JSON
# =========================
output = {
    "best_k": int(best_k),
    "cv_accuracy": round(best_cv_acc * 100, 2),
    "test_accuracy": round(test_acc * 100, 2),
    "k_results": results_k,
    "data": df.to_dict(orient="records")
}

print("===== JSON DATA =====")
print(json.dumps(output))