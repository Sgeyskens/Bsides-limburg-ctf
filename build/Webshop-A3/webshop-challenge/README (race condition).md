# README: The Race Condition Coupon Exploit (Friday the 13th CTF)

## 1. Challenge Overview

This is a timing-based race condition vulnerability in a web application's discount code system. The goal is to teach players how to identify and exploit concurrent request handling flaws by sending multiple simultaneous requests that bypass validation checks.

* **Theme**: Friday the 13th / Camp Crystal Lake Webshop
* **Difficulty**: Medium
* **Final Flag**: `CTF{RACE_CONDITION_EXPLOITED_COUPON_STACKING}`

## 2. Vulnerability Logic (Creation)

The discount application process has three critical flaws that combine to create a race condition. Each flaw enables the next stage of exploitation.

| Layer | Method | Key/Parameters |
|-------|--------|----------------|
| **Layer 1 (Validation)** | Check cart total | Happens BEFORE discount application |
| **Layer 2 (Processing)** | Backend delay | `sleep(2)` creates 2-second window |
| **Layer 3 (Update)** | Additive discount | Uses `+=` instead of `=` |

### Generation Command

Deploy the `CheckoutController_VULNERABLE.php` with the following configurations:
* `sleep(2)` in the `applyDiscount()` method
* `cart->discount_amount += $discount` (additive operation)
* No database locking (`lockForUpdate()` disabled)
* Discount code `RACE50`: 50% off with $50 minimum purchase

## 3. The Challenge Pose

The challenge appears to the player as a functioning e-commerce checkout system with discount code functionality.

**Example scenario:**
```
Cart Total: $100.00
Available discount code: RACE50
Expected discount: $50.00 (50%)
Expected final total: $50.00
```

The primary obstacle is that the vulnerability will not reveal itself through normal usage. The discount system works perfectly when requests are sent sequentially. Only when multiple **simultaneous** requests are sent will the race condition manifest, causing the discount to stack incorrectly and the total to drop to $0.00.

## 4. Possible Solve Path (Walkthrough)

### Step 1: Identify the Discount Endpoint

The player discovers the discount code system and identifies its API endpoint.

* **Action**: Use browser DevTools Network tab to inspect the "Apply Discount" request.
* **Result**: Endpoint `/checkout/apply-discount` with POST method and JSON body `{"code":"RACE50"}`.

### Step 2: Test Sequential Requests

The player attempts to apply the discount code multiple times by clicking rapidly.

* **Action**: Click "Apply Discount" multiple times in succession.
* **Result**: Second request returns error "Discount already applied" - sequential exploitation fails.

### Step 3: Exploit with Concurrent Requests

Using the Friday the 13th theme hint about "timing," the player sets up a race condition attack.

* **Action**: Use Burp Suite Intruder with:
  * Payload type: Null payloads
  * Number of payloads: 10
  * Thread count: 10-20
* **Logic**:
  * Request 1: `Check cart=$100` → `Calculate discount=$50` → `cart.discount += $50`
  * Request 2: `Check cart=$100` → `Calculate discount=$50` → `cart.discount += $50`
  * Request 3: `Check cart=$100` → `Calculate discount=$50` → `cart.discount += $50`
  * All execute during the `sleep(2)` window
  * Final: `cart.discount_amount = $150+`
* **Result**: The discount amount exceeds the cart total, bringing the final price to $0.00 and revealing the flag `CTF{RACE_CONDITION_EXPLOITED_COUPON_STACKING}`.

## 5. Tools for Solvers

Players can use:

* **Burp Suite**: Essential for sending multiple concurrent requests via the Intruder tool.
* **Python with threading**: Necessary for custom scripts that send simultaneous requests.
* **cURL with background processes**: Alternative command-line approach for concurrent execution.
