from flask import Flask, request, render_template_string
import subprocess
import re

app = Flask(__name__)

HTML = """
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Crystal Ping</title>
  <style>
    body { font-family: sans-serif; max-width: 900px; margin: 40px auto; padding: 0 16px; }
    input { width: 420px; padding: 10px; }
    button { padding: 10px 16px; }
    pre { background: #111; color: #eee; padding: 14px; overflow-x: auto; border-radius: 8px; white-space: pre-wrap; }
    .hint { color: #666; font-size: 14px; }
  </style>
</head>
<body>
  <h2>Connectivity Check</h2>
  <p class="hint">Enter a host (IP or name) to ping once.</p>
  <form method="POST" action="/ping">
    <input name="host" placeholder="8.8.8.8" autocomplete="off" required />
    <button type="submit">Ping</button>
  </form>

  {% if result is not none %}
    <h3>Result</h3>
    <pre>{{ result }}</pre>
  {% endif %}
</body>
</html>
"""

MAX_LEN = 60
MAX_OUTPUT = 350
PING_TIMEOUT_SECONDS = 2
FLAG_PATH = "/flag/flag.txt"


@app.route("/", methods=["GET"])
def index():
    return render_template_string(HTML, result=None)


@app.route("/ping", methods=["POST"])
def ping():
    host = request.form.get("host", "")

    # Moderate knobs: strip ALL whitespace + cap length
    host = re.sub(r"\s+", "", host)[:MAX_LEN]

    if not host:
        return render_template_string(HTML, result="Error: empty host")

    # If injection is attempted (;) enforce a strict allowlist
    if ";" in host:
        allow_ls = re.fullmatch(
            r"^[A-Za-z0-9\.\-:]+;ls(\$\{IFS\}/(\w+)?)?$",
            host
        )

        allow_read = re.fullmatch(
            r"^[A-Za-z0-9\.\-:]+;(head|tail)(<" + re.escape(FLAG_PATH) + r"|\$\{IFS\}" + re.escape(FLAG_PATH) + r")$",
            host
        )

        if not (allow_ls or allow_read):
            return render_template_string(HTML, result="Invalid host")

    # Intentionally vulnerable:
    cmd = f"ping -c 1 {host}"

    try:
        p = subprocess.run(
            cmd,
            shell=True,
            capture_output=True,
            text=True,
            timeout=PING_TIMEOUT_SECONDS
        )
        out = (p.stdout + p.stderr).strip() or "(no output)"
    except subprocess.TimeoutExpired:
        out = "Error: ping timed out."

    if len(out) > MAX_OUTPUT:
        out = out[:MAX_OUTPUT] + "\n...(trimmed)"

    return render_template_string(HTML, result=out)


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
