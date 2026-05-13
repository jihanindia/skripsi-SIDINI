import pandas as pd
import sys

file_path = "public/assets/datasets/dataset_1778681621.csv"
df = pd.read_csv(file_path)
print(f"Columns: {df.columns.tolist()}")
