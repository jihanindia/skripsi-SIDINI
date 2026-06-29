import sys
import json
import pandas as pd
import numpy as np
import os
from sklearn.neighbors import KNeighborsClassifier
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.metrics import confusion_matrix, classification_report, accuracy_score

def predict_csv():
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Butuh 2 argumen: path dataset training dan path file uji CSV."}))
        sys.exit(1)

    training_path = sys.argv[1]
    test_path     = sys.argv[2]

    # =========================================
    # 1. LOAD DATASET TRAINING (dengan label)
    # =========================================
    try:
        df_train = pd.read_csv(training_path, sep=None, engine='python')
        df_train.columns = df_train.columns.str.strip().str.lower()
        df_train = df_train.dropna().reset_index(drop=True)
    except Exception as e:
        print(json.dumps({"error": f"Gagal membaca dataset training: {str(e)}"}))
        sys.exit(1)

    # Normalisasi nama kolom training
    column_mapping = {
        'tinggibadan': 'tb',
        'beratbadan':  'bb',
        'sistol':      'sistolik',
        'diastol':     'diastolik',
        'protein_urine': 'protein_urin',
    }
    df_train = df_train.rename(columns=column_mapping)

    features = ["usia", "paritas", "tb", "bb", "imt",
                "sistolik", "diastolik", "map", "gds", "protein_urin"]

    # Pastikan kolom 'status' ada
    if 'status' not in df_train.columns:
        print(json.dumps({"error": "Dataset training tidak memiliki kolom 'status'."}))
        sys.exit(1)

    # Hitung MAP jika belum ada
    if 'map' not in df_train.columns:
        df_train['map'] = ((2 * df_train['diastolik']) + df_train['sistolik']) / 3

    # Cek kolom fitur tersedia
    missing_cols = [c for c in features if c not in df_train.columns]
    if missing_cols:
        print(json.dumps({"error": f"Kolom tidak ditemukan di training: {missing_cols}"}))
        sys.exit(1)

    X_train_full = df_train[features].copy()
    y_train_full = df_train['status']

    # =========================================
    # 2. LOAD FILE UJI (tanpa label)
    # =========================================
    try:
        df_test = pd.read_csv(test_path, sep=None, engine='python')
        df_test.columns = df_test.columns.str.strip().str.lower()
    except Exception as e:
        print(json.dumps({"error": f"Gagal membaca file uji: {str(e)}"}))
        sys.exit(1)

    # Normalisasi kolom file uji
    df_test = df_test.rename(columns=column_mapping)

    # Hitung MAP jika belum ada di file uji
    if 'map' not in df_test.columns:
        if 'sistolik' in df_test.columns and 'diastolik' in df_test.columns:
            df_test['map'] = ((2 * pd.to_numeric(df_test['diastolik'], errors='coerce')) +
                               pd.to_numeric(df_test['sistolik'], errors='coerce')) / 3
        else:
            print(json.dumps({"error": "Kolom sistolik/diastolik tidak ditemukan untuk menghitung MAP."}))
            sys.exit(1)

    # Cek apakah file uji punya label (untuk confusion matrix)
    has_label = 'status' in df_test.columns

    # Bersihkan data uji
    df_test_clean = df_test.copy()
    for col in features:
        if col in df_test_clean.columns and col != 'protein_urin':
            df_test_clean[col] = pd.to_numeric(df_test_clean[col], errors='coerce')

    df_test_clean = df_test_clean.dropna(subset=features).reset_index(drop=True)

    missing_test_cols = [c for c in features if c not in df_test_clean.columns]
    if missing_test_cols:
        print(json.dumps({"error": f"Kolom tidak ditemukan di file uji: {missing_test_cols}"}))
        sys.exit(1)

    X_test_data = df_test_clean[features].copy()

    # =========================================
    # 3. PREPROCESSING & MODEL
    # =========================================
    categorical_features = ["protein_urin"]
    numerical_features   = ["usia", "paritas", "tb", "bb", "imt",
                             "sistolik", "diastolik", "map", "gds"]

    preprocessor = ColumnTransformer(
        transformers=[
            ('num', MinMaxScaler(),                           numerical_features),
            ('cat', OneHotEncoder(handle_unknown='ignore'),   categorical_features),
        ]
    )

    # Ambil best_k dari metadata
    best_k = 5
    metadata_path = os.path.join(os.getcwd(), 'storage', 'app', 'knn_metadata.json')
    if os.path.exists(metadata_path):
        try:
            with open(metadata_path, 'r') as f:
                meta = json.load(f)
                best_k = meta.get('best_k', 5)
        except:
            pass

    model = Pipeline([
        ('preprocessor', preprocessor),
        ('knn', KNeighborsClassifier(n_neighbors=best_k, metric='euclidean')),
    ])

    model.fit(X_train_full, y_train_full)

    # =========================================
    # 4. PREDIKSI
    # =========================================
    y_pred = model.predict(X_test_data)
    probas = model.predict_proba(X_test_data)
    classes = list(model.classes_)

    # =========================================
    # 5. HASIL PER BARIS
    # =========================================
    rows_result = []
    for i in range(len(df_test_clean)):
        row = df_test_clean.iloc[i].to_dict()
        row['prediksi_knn'] = str(y_pred[i])
        row['confidence']   = float(np.max(probas[i])) * 100
        proba_dict = {c: float(probas[i][j]) for j, c in enumerate(classes)}
        row['probabilitas'] = proba_dict
        if has_label:
            row['label_asli'] = str(df_test_clean.iloc[i].get('status', ''))
        rows_result.append(row)

    # =========================================
    # 6. CONFUSION MATRIX (jika ada label)
    # =========================================
    confusion_matrix_data = None
    classification_rep     = None
    test_accuracy          = None
    cm_labels              = None

    if has_label:
        y_true = df_test_clean['status'].astype(str)
        y_pred_series = pd.Series(y_pred).astype(str)

        cm_labels = sorted(list(set(y_true.tolist() + y_pred_series.tolist())))
        cm = confusion_matrix(y_true, y_pred_series, labels=cm_labels)
        confusion_matrix_data = cm.tolist()
        test_accuracy = float(accuracy_score(y_true, y_pred_series)) * 100

        report = classification_report(
            y_true,
            y_pred_series,
            labels=cm_labels,
            zero_division=0,
            output_dict=True
        )
        classification_rep = report

    # =========================================
    # 7. STATISTIK PREDIKSI
    # =========================================
    pred_series = pd.Series(y_pred)
    stats = pred_series.value_counts().to_dict()

    output = {
        "best_k":               best_k,
        "total_data":           len(df_test_clean),
        "has_label":            has_label,
        "test_accuracy":        test_accuracy,
        "confusion_matrix":     confusion_matrix_data,
        "confusion_matrix_labels": cm_labels,
        "classification_report": classification_rep,
        "prediction_stats":     {str(k): int(v) for k, v in stats.items()},
        "predictions":          rows_result,
    }

    print("===== JSON DATA =====")
    print(json.dumps(output, default=str))

if __name__ == "__main__":
    predict_csv()
