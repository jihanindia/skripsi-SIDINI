import pandas as pd
import sys
import json

from sklearn.model_selection import (
    train_test_split,
    StratifiedKFold,
    cross_val_score
)

from sklearn.neighbors import KNeighborsClassifier

from sklearn.preprocessing import (
    MinMaxScaler,
    OneHotEncoder
)

from sklearn.compose import ColumnTransformer

from sklearn.metrics import (
    confusion_matrix,
    classification_report,
    accuracy_score,
    roc_curve,
    auc
)

from sklearn.pipeline import Pipeline
from sklearn.metrics import accuracy_score, classification_report

# =========================
# 1. LOAD FILE CSV DARI LARAVEL
# =========================
if len(sys.argv) < 2:
    print(json.dumps({
        "error": "Path file CSV wajib diberikan"
    }))
    sys.exit(1)

file_path = sys.argv[1]

try:
    df = pd.read_csv(file_path)

except Exception as e:
    print(json.dumps({
        "error": f"Gagal membaca file CSV: {str(e)}"
    }))
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

# Calculate map (Mean Arterial Pressure)
# df['map'] = ((2 * df['diastolik']) + df['sistolik']) / 3

# =========================
# 4. PILIH FITUR
# =========================
X = df[[
    "usia",
    "paritas",
    "tb",
    "bb",
    "imt",
    "sistolik",
    "diastolik",
    "map",
    "gds",
    "protein_urin"
]].copy()

y = df["status"]

# =========================
# 5. FITUR NUMERIK & KATEGORIK
# =========================
numerical_features = [
    "usia",
    "paritas",
    "tb",
    "bb",
    "imt",
    "sistolik",
    "diastolik",
    "map",
    "gds"
]

categorical_features = [
    "protein_urin"
]

# =========================
# 6. SPLIT DATA
# =========================
X_train, X_test, y_train, y_test, idx_train, idx_test = train_test_split(
    X,
    y,
    df.index,
    test_size=0.20,
    random_state=42
    # stratify=y
)

# =========================
# 7. PREPROCESSOR
# =========================
preprocessor = ColumnTransformer(
    transformers=[

        (
            'num',
            MinMaxScaler(),
            numerical_features
        ),

        (
            'cat',
            OneHotEncoder(
                handle_unknown='ignore'
            ),
            categorical_features
        )
    ]
)

# =========================
# 8. TUNING K
# =========================
k_values = [3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23]

best_k = 0
best_cv_acc = 0

results_k = []

skf = StratifiedKFold(
    n_splits=5,
    shuffle=True,
    random_state=42
)

for k in k_values:

    model = Pipeline([

        ('preprocessor', preprocessor),

        ('knn', KNeighborsClassifier(
            n_neighbors=k,
            metric='euclidean'
        ))
    ])

    # =========================
    # CROSS VALIDATION
    # =========================
    scores = cross_val_score(
        model,
        X_train,
        y_train,
        cv=skf
    )

    cv_acc = scores.mean()

    results_k.append({
        "k": k,
        "cv_accuracy": round(cv_acc * 100, 2)
    })

    # =========================
    # SIMPAN K TERBAIK
    # =========================
    if cv_acc > best_cv_acc:
        best_cv_acc = cv_acc
        best_k = k


# =========================
# 9. MODEL FINAL
# =========================
final_model = Pipeline([

    ('preprocessor', preprocessor),

    ('knn', KNeighborsClassifier(
        n_neighbors=best_k,
        metric='euclidean'
    ))
])

# =========================
# 10. TRAINING FINAL
# =========================
final_model.fit(X_train, y_train)

# =========================
# 11. PREDIKSI FINAL
# =========================
y_pred = final_model.predict(X_test)

# =========================
# 12. EVALUASI
# =========================
test_acc = accuracy_score(y_test, y_pred)

cm = confusion_matrix(y_test, y_pred)

report = classification_report(
    y_test,
    y_pred,
    zero_division=0,
    output_dict=True
)

try:
    y_prob = final_model.predict_proba(X_test)
    classes = final_model.classes_
    pos_idx = 1
    if 'preeklampsia' in [str(c).lower() for c in classes]:
        pos_idx = [str(c).lower() for c in classes].index('preeklampsia')
    elif len(classes) > 1:
        pos_idx = 1
    
    fpr, tpr, _ = roc_curve(y_test, y_prob[:, pos_idx], pos_label=classes[pos_idx])
    roc_auc = auc(fpr, tpr)
    roc_data = {
        'fpr': fpr.tolist(),
        'tpr': tpr.tolist(),
        'auc': round(roc_auc, 3)
    }
except Exception as e:
    roc_data = None


# =========================
# 13. HASIL ONE HOT ENCODING
# =========================
encoder = final_model.named_steps[
    'preprocessor'
].named_transformers_['cat']

encoded_columns = encoder.get_feature_names_out(
    categorical_features
)

all_columns = (
    numerical_features +
    list(encoded_columns)
)

X_encoded = final_model.named_steps[
    'preprocessor'
].transform(X)

X_encoded_df = pd.DataFrame(
    X_encoded,
    columns=all_columns
)

# =========================
# 14. PREDIKSI FULL DATA
# =========================
df["prediksi_knn"] = final_model.predict(X)

# =========================
# 15. OUTPUT JSON
# =========================
output = {

    "best_k": int(best_k),

    "cv_accuracy": round(best_cv_acc * 100, 2),

    "test_accuracy": round(test_acc * 100, 2),

    "confusion_matrix": cm.tolist(),

    "classification_report": report,

    "k_results": results_k,

    "roc_curve": roc_data,

    "encoded_columns": list(encoded_columns),

    "data_after_encoding": (
        X_encoded_df.head(20)
        .to_dict(orient="records")
    ),

    "data_test": (
        df.loc[idx_test]
        .assign(prediksi=y_pred)
        .to_dict(orient="records")
    ),

    "data": df.to_dict(orient="records")
}

# Ringkasan untuk terminal (dibaca Laravel sebelum marker JSON)
print(f"Best K: {best_k}")
print(f"CV Accuracy: {round(best_cv_acc * 100, 2)}%")
print(f"Test Accuracy: {round(test_acc * 100, 2)}%")
print(f"Total data: {len(df)}")
print("===== JSON DATA =====")
print(json.dumps(output, default=str))