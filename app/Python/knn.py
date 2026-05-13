import pandas as pd
import sys
import json
import numpy as np
from sklearn.model_selection import train_test_split, StratifiedKFold, cross_val_score
from sklearn.neighbors import KNeighborsClassifier
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.metrics import accuracy_score, classification_report

# =========================
# 1. LOAD DATA (CSV)
# =========================
if len(sys.argv) < 2:
    print(json.dumps({"error": "Path file CSV wajib diberikan sebagai argumen."}))
    sys.exit(1)

file_path = sys.argv[1]

try:
    df = pd.read_csv(file_path, sep=None, engine='python')
except Exception as e:
    print(json.dumps({"error": f"Gagal membaca file CSV: {str(e)}"}))
    sys.exit(1)

# =========================
# 2. CLEANING
# =========================
df.columns = df.columns.str.strip().str.lower()

# Simpan kolom asli untuk output nanti
df_original = df.copy()

for col in df.columns:
    if df[col].dtype == "object":
        df[col] = df[col].astype(str).str.strip()

# =========================
# 3. DROP NULL
# =========================
df = df.dropna().reset_index(drop=True)

# Validasi kolom target
if "status" not in df.columns:
    print(json.dumps({"error": "Kolom 'status' tidak ditemukan dalam dataset"}))
    sys.exit(1)

# =========================
# 4. PILIH FITUR
# =========================
# Sesuaikan nama kolom jika ada perbedaan kecil (misal tb vs tinggibadan)
column_mapping = {
    'tinggibadan': 'tb',
    'beratbadan': 'bb',
    'sistol': 'sistolik',
    'diastol': 'diastolik',
    'protein_urine': 'protein_urin'
}
df = df.rename(columns=column_mapping)

features = [
    "usia", "paritas", "tb", "bb", "imt", 
    "sistolik", "diastolik", "riw_ht_keluarga", 
    "hb", "gds", "protein_urin"
]

# Pastikan semua fitur ada
missing_features = [f for f in features if f not in df.columns]
if missing_features:
    print(json.dumps({"error": f"Fitur berikut tidak ditemukan: {', '.join(missing_features)}"}))
    sys.exit(1)

X = df[features].copy()
y = df["status"]

# =========================
# 5. DEFINISI PREPROCESSOR
# =========================
categorical_features = ["riw_ht_keluarga", "protein_urin"]
numerical_features = ["usia", "paritas", "tb", "bb", "imt", "sistolik", "diastolik", "hb", "gds"]

preprocessor = ColumnTransformer(
    transformers=[
        ('num', MinMaxScaler(), numerical_features),
        ('cat', OneHotEncoder(handle_unknown='ignore'), categorical_features)
    ]
)

# =========================
# 6. SPLIT DATA
# =========================
X_train, X_test, y_train, y_test, idx_train, idx_test = train_test_split(
    X, y, df.index, test_size=0.20, random_state=42, stratify=y
)

# =========================
# 7. TUNING K
# =========================
k_values = [1, 3, 5, 7, 9]
best_k = 0
best_cv_acc = 0
results_k = []

skf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)

for k in k_values:
    model = Pipeline([
        ('preprocessor', preprocessor),
        ('knn', KNeighborsClassifier(n_neighbors=k, metric='euclidean'))
    ])
    
    # Cross Validation
    scores = cross_val_score(model, X_train, y_train, cv=skf)
    cv_acc = scores.mean()
    
    # Training & Prediction untuk info tambahan
    model.fit(X_train, y_train)
    y_pred_k = model.predict(X_test)
    test_acc_k = accuracy_score(y_test, y_pred_k)
    
    results_k.append({
        "k": k, 
        "cv_accuracy": round(cv_acc * 100, 2),
        "test_accuracy": round(test_acc_k * 100, 2)
    })

    if cv_acc > best_cv_acc:
        best_cv_acc = cv_acc
        best_k = k

# =========================
# 8. MODEL FINAL
# =========================
final_model = Pipeline([
    ('preprocessor', preprocessor),
    ('knn', KNeighborsClassifier(n_neighbors=best_k, metric='euclidean'))
])

final_model.fit(X_train, y_train)
y_pred = final_model.predict(X_test)
test_acc = accuracy_score(y_test, y_pred)

# =========================
# 9. PREDIKSI UNTUK SELURUH DATA
# =========================
df["prediksi_knn"] = final_model.predict(X)

# =========================
# 10. OUTPUT TERMINAL (Dilihat di log Laravel)
# =========================
print(f"\n===== HASIL MODEL FINAL =====")
print(f"K terbaik        : {best_k}")
print(f"Distance Metric  : Euclidean Distance")
print(f"CV Accuracy      : {round(best_cv_acc * 100, 2)}%")
print(f"Test Accuracy    : {round(test_acc * 100, 2)}%")
print("\nClassification Report Final:")
print(classification_report(y_test, y_pred, zero_division=0))

# =========================
# 11. OUTPUT JSON
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