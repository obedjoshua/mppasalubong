import uvicorn
from fastapi import FastAPI, UploadFile, File, HTTPException
from fastapi.responses import Response
from background_engine import process_cake_image

# THIS EXACT VARIABLE NAME "app" IS REQUIRED BY UVICORN
app = FastAPI(title="Masaganang Pagpapala AI Processor")

@app.post("/api/ai/process-cake")
async def api_process_cake(file: UploadFile = File(...)):
    if not file.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="File must be an image.")

    image_bytes = await file.read()
    
    try:
        final_image_bytes = process_cake_image(image_bytes)
        return Response(content=final_image_bytes, media_type="image/jpeg")
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    uvicorn.run("main:app", host="127.0.0.1", port=8001)