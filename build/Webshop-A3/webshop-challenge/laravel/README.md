# Friday the 13th Webshop - CTF Challenge

A deliberately vulnerable webshop application for security training and CTF challenges.

## CTF Challenges

### 1. Stored XSS (Cross-Site Scripting)

**Vulnerability Type:** A03:2021 - Injection (XSS)

**Location:** User profile bio field

**Description:**
The user bio field does not sanitize input, allowing arbitrary HTML and JavaScript to be stored and executed when the profile is viewed.

**Vulnerable Code:**
```php
// resources/views/user-account.blade.php
{!! $user->bio !!}
```

The `{!! !!}` Blade syntax renders raw HTML without escaping, allowing script injection.

**How to Exploit:**
1. Log in to your account
2. Go to "Edit Profile"
3. In the Bio field, enter: `<script>alert(1)</script>`
4. Save the profile
5. View your account page
6. The script executes and displays the flag

**Flag:** `CTF{STORED_XSS_VULNERABILITY}`

**Real-world Impact:**
- Session hijacking (stealing cookies)
- Keylogging user input
- Phishing attacks
- Defacing the website
- Spreading malware

**Mitigation:**
- Always use `{{ }}` (escaped output) instead of `{!! !!}` for user input
- Implement Content Security Policy (CSP) headers
- Use HTML sanitization libraries (e.g., HTMLPurifier)

---

### 2. Race Condition - Discount Code Stacking

**Vulnerability Type:** A04:2021 - Insecure Design

**Location:** Checkout discount application

**Description:**
The discount code application has a race condition vulnerability that allows the same discount to be applied multiple times through concurrent requests.

**Vulnerable Code:**
```php
// app/Http/Controllers/CheckoutController.php
sleep(2); // Simulated processing delay

DB::table('cart')
    ->where('cart_id', $cart->cart_id)
    ->increment('discount_amount', $discount);
```

**How to Exploit:**
1. Add items to cart
2. Go to checkout
3. Use a tool like Burp Suite Intruder to send multiple concurrent requests applying the same discount code
4. The discount stacks multiple times, potentially making the total $0

**Flag:** `CTF{RACE_CONDITION_EXPLOITED_COUPON_STACKING}`

**Mitigation:**
- Use database transactions with proper locking
- Implement atomic operations
- Add rate limiting
- Use optimistic/pessimistic locking patterns

---

## Setup

```bash
# Install dependencies
composer install
npm install

# Setup database
php artisan migrate
php artisan db:seed

# Run development server
php artisan serve
npm run dev
```

## Default Accounts

| Username | Password | Role |
|----------|----------|------|
| admin | admin123 | Admin |
| user | user123 | User |

## Disclaimer

This application contains intentional security vulnerabilities for educational purposes. Do NOT deploy this application in a production environment or on a publicly accessible server.
