from fastapi import FastAPI, File, UploadFile, Form, Header
from typing import Optional
from fastapi.responses import JSONResponse, FileResponse
import os
import tempfile
from core.tasks import process_request

app = FastAPI(title="AI File Worker")

@app.post("/process-file")
async def process_file(
    file: UploadFile = File(...),
    user_prompt: str = Form(...),
    output_format: str = Form("auto"),  # "auto", "py", "json", "docx", v.v.
    authorization: Optional[str] = Header(default=None),
    internal_key: Optional[str] = Header(default=None, alias="X-Internal-Key")
):
    api_key: Optional[str] = None
    if authorization:
        scheme_split = authorization.split(" ", 1)
        if len(scheme_split) == 2 and scheme_split[0].lower() == "bearer":
            api_key = scheme_split[1].strip()
        else:
            api_key = authorization.strip()
    elif internal_key:
        api_key = internal_key.strip()

    with tempfile.NamedTemporaryFile(delete=False) as tmp:
        content = await file.read()
        tmp.write(content)
        tmp_path = tmp.name

    try:
        result = process_request(
            file_path=tmp_path,
            filename=file.filename,
            prompt=user_prompt,
            output_format=output_format,
            api_key=api_key
        )

        if isinstance(result, dict) and "file_path" in result:
            return FileResponse(
                path=result["file_path"],
                filename=result["filename"],
                media_type="application/octet-stream"
            )
        else:
            return JSONResponse({"result": str(result)})

    except Exception as e:
        return JSONResponse({"error": str(e)}, status_code=500)
    finally:
        if os.path.exists(tmp_path):
            os.unlink(tmp_path)