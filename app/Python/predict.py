import sys
import json
import pandas as pd
import numpy as np
from sklearn.neighbors import KNeighborsClassifier
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline

def predict():
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Dataset path and input data (JSON) are required."}))
        sys.exit(1)

    dataset_path = sys.argv[1]
    try:
        input_data = json.loads(sys.argv[2])
    except:
        print(json.dumps({"error": "Invalid input JSON."}))
        sys.exit(1)

    try:
        # Load dataset untuk training (KNN butuh data referensi)
        df = pd.read_csv(dataset_path, sep=None, engine='python')
        df.columns = df.columns.str.strip().str.lower()
        df = df.dropna().reset_index(drop=True)
        
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
        
        X = df[features].copy()
        y = df["status"]

        # Preprocessing
        categorical_features = ["riw_ht_keluarga", "protein_urin"]
        numerical_features = ["usia", "paritas", "tb", "bb", "imt", "sistolik", "diastolik", "hb", "gds"]

        preprocessor = ColumnTransformer(
            transformers=[
                ('num', MinMaxScaler(), numerical_features),
                ('cat', OneHotEncoder(handle_unknown='ignore'), categorical_features)
            ]
        )

        # Gunakan K=5 sebagai default jika tidak ditentukan
        model = Pipeline([
            ('preprocessor', preprocessor),
            ('knn', KNeighborsClassifier(n_neighbors=1, metric='euclidean'))
        ])

        model.fit(X, y)

        # Prepare input for prediction
        # Map input keys to feature names
        input_mapped = {
            "usia": float(input_data.get("usia", 0)),
            "paritas": float(input_data.get("paritas", 0)),
            "tb": float(input_data.get("tinggibadan", 0)),
            "bb": float(input_data.get("beratbadan", 0)),
            "imt": float(input_data.get("imt", 0)),
            "sistolik": float(input_data.get("systolic_bp", 0)),
            "diastolik": float(input_data.get("diastolic_bp", 0)),
            "riw_ht_keluarga": str(input_data.get("riw_ht_keluarga", "0")),
            "hb": float(input_data.get("hb", 0)),
            "gds": float(input_data.get("gds", 0)),
            "protein_urin": str(input_data.get("protein_urine", "0"))
        }
        
        # Convert riw_ht_keluarga 1/0 to Ada/Tidak ada if necessary
        if input_mapped["riw_ht_keluarga"] == "1":
            input_mapped["riw_ht_keluarga"] = "Ada"
        else:
            input_mapped["riw_ht_keluarga"] = "Tidak ada"
            
        # Convert protein_urin to PositifX/Negatif
        pu = input_mapped["protein_urin"]
        if pu == "0":
            input_mapped["protein_urin"] = "Negatif"
        else:
            input_mapped["protein_urin"] = f"Positif{pu}"

        X_new = pd.DataFrame([input_mapped])
        
        prediction = model.predict(X_new)[0]
        probabilities = model.predict_proba(X_new)[0]
        classes = model.classes_
        
        # Ambil tetangga terdekat
        distances, indices = model.named_steps['knn'].kneighbors(model.named_steps['preprocessor'].transform(X_new))
        neighbors = df.iloc[indices[0]].to_dict(orient="records")

        result = {
            "prediction": prediction,
            "confidence": float(np.max(probabilities)),
            "neighbors": neighbors
        }
        print(json.dumps(result))

    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    predict()
