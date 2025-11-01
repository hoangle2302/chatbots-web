import os
from typing import Optional

import requests
from dotenv import load_dotenv

load_dotenv()


def _resolve_key_and_url(override_key: Optional[str] = None, override_url: Optional[str] = None):
    api_key = (override_key or os.getenv("KEY4U_API_KEY") or os.getenv("AI_API_KEY") or "").strip()
    if not api_key:
        raise RuntimeError("Chưa cấu hình KEY4U_API_KEY/AI_API_KEY.")

    api_url = (override_url or os.getenv("KEY4U_API_URL") or os.getenv("AI_API_URL") or "https://api.key4u.shop/v1/chat/completions").strip()

    return api_key, api_url


def call_ai(
    prompt: str,
    system_prompt: str = "Bạn là trợ lý AI hữu ích.",
    json_mode: bool = False,
    temperature: float = 0.3,
    api_key: Optional[str] = None,
    model: Optional[str] = None
) -> str:
    token, url = _resolve_key_and_url(api_key, os.getenv("KEY4U_API_URL"))

    payload = {
        "model": model or os.getenv("AI_MODEL", "gpt-4-turbo"),
        "messages": [
            {"role": "system", "content": system_prompt},
            {"role": "user", "content": prompt}
        ],
        "temperature": temperature,
    }

    if json_mode:
        payload["response_format"] = {"type": "json_object"}

    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {token}"
    }

    response = requests.post(url, headers=headers, json=payload, timeout=120)
    response.raise_for_status()

    data = response.json()

    if isinstance(data, dict) and "choices" in data and data["choices"]:
        return data["choices"][0]["message"]["content"]

    if isinstance(data, dict) and "result" in data:
        return data["result"]

    return str(data)