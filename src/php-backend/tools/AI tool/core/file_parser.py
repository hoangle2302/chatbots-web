import os
from PyPDF2 import PdfReader
from docx import Document
import pandas as pd

def extract_text(file_path: str, original_name: str) -> str:
    ext = os.path.splitext(original_name)[1].lower()
    if ext == ".pdf":
        reader = PdfReader(file_path)
        return "\n".join(page.extract_text() or "" for page in reader.pages)
    elif ext == ".docx":
        doc = Document(file_path)
        return "\n".join(para.text for para in doc.paragraphs)
    elif ext == ".txt":
        with open(file_path, "r", encoding="utf-8") as f:
            return f.read()
    elif ext in [".xlsx", ".xls"]:
        df = pd.read_excel(file_path)
        return df.to_string()
    else:
        return f"[Nội dung file {ext} chưa được hỗ trợ, xử lý như text thô]\n" + str(open(file_path, "rb").read()[:500])