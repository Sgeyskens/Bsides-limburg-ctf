# image-name: a3/command-injection-ping-a3
FROM python:3.12-slim

ENV DEBIAN_FRONTEND=noninteractive

# ping utility
RUN apt-get update && apt-get install -y --no-install-recommends iputils-ping \
  && rm -rf /var/lib/apt/lists/*

# Create non-root user
RUN useradd -m -u 10001 ctf

WORKDIR /app
COPY requirements.txt /app/
RUN pip install --no-cache-dir -r requirements.txt

COPY app.py /app/app.py

# Flag location + permissions (readable by app user only)
RUN mkdir -p /flag
COPY flag.txt /flag/flag.txt
RUN chown -R ctf:ctf /flag && chmod 400 /flag/flag.txt

USER ctf

EXPOSE 5000
CMD ["gunicorn", "-w", "2", "-b", "0.0.0.0:5000", "app:app"]
