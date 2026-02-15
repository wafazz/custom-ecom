# Code Review Checklist - function.php

## 🔴 CRITICAL SECURITY ISSUES

### SQL Injection - IMMEDIATE ACTION REQUIRED
- [ ] **Line 55-56**: `roleVerify()` - Direct string interpolation
  ```php
  // ❌ VULNERABLE
  $query = $conn->query("SELECT * FROM role_access WHERE page_url='$url' AND allowed_user LIKE '%[$id]%'");
  ```

- [ ] **Line 97-98**: `itemSold()` - Unsafe query
- [ ] **Line 286-287**: `getCategoryDetails()` - SQL injection via $id
- [ ] **Line 303-304**: `getBrandDetails()` - SQL injection via $id
- [ ] **Line 315+**: `stockBalanceIndividual()` - SQL injection via $id
- [ ] **Line 387**: `getAllProductImage()` - SQL injection via $id
- [ ] **Line 424-426**: `getProductImageSingle()` - SQL injection via $id
- [ ] **Line 438-439**: `getPriceOnCountry()` - SQL injection via $cid, $pid
- [ ] **Line 453-454**: `countUsedCategory()` - SQL injection via $id
- [ ] **Line 882-884**: `getUsedCategory()` - SQL injection via $id
- [ ] **Line 903-905**: `getUsedBrand()` - SQL injection via $id
- [ ] **Line 923-926**: `getCategoryBrand()` - SQL injection via $id
- [ ] **Line 1306-1308**: `userData()` - SQL injection via $userID
- [ ] **Line 1323-1325**: `dataCountry()` - SQL injection via $id

**SOLUTION**: Replace ALL with prepared statements using `$stmt->bind_param()`

---

### Hardcoded Credentials - SECURITY BREACH RISK
- [ ] **Line 27**: Brevo SMTP Username exposed
  ```php
  // ❌ EXPOSED
  $mail->Username = '889d41001@smtp-brevo.com';
  ```

- [ ] **Line 28**: Brevo API Key exposed (looks like real API key format)
  ```php
  // ❌ EXPOSED - LONG API KEY
  $mail->Password = 'xsmtpsib-XXXXXXXXXXXXXXXXXXXX';
  ```

- [ ] **Lines 636-641**: DHL hardcoded business data
  ```php
  // ❌ HARDCODED SENSITIVE DATA
  "name" => "ROZZ BEAUTY LEGACY",
  "address1" => "B-G-48, SAVANNA LIFESTYLE RETAIL",
  "phone" => "60389123807",
  "email" => "wafazz.tech@gmail.com"
  ```

**ACTION ITEMS**:
1. Create `.env` file (add to .gitignore)
2. Move all credentials to environment variables
3. Use `getenv()` or `$_ENV` to access
4. Rotate the exposed API key immediately
5. Update git history to remove exposed credentials

---

## 🟡 MAJOR CODE QUALITY ISSUES

### Inconsistent Return Types
- [ ] **Lines 249-268**: `cartCount()` returns array
- [ ] **Line 269-280**: `cartList()` returns query object
- [ ] **Lines 786-823**: `totalSales()` echoes output instead of returning
- [ ] **Lines 819-830**: `totalProduct()` echoes output
- [ ] **Lines 832-843**: `totalOrder()` echoes output
- [ ] **Lines 845-856**: `totalOrderReturn()` echoes output

**FIX**: All functions should return values, never echo. Let calling code handle display.

### Database Connection Mismanagement
- [ ] **Line 823**: `$conn->close()` in `totalSales()`
- [ ] **Line 830**: `$conn->close()` in `totalProduct()`
- [ ] **Line 843**: `$conn->close()` in `totalOrder()`
- [ ] **Line 856**: `$conn->close()` in `totalOrderReturn()`

**FIX**: Never close shared connections. Use connection pooling/singleton pattern.

### Missing Type Hints
```php
// ❌ NO TYPE HINTS
function calculatePostage($weightKg, $baseRate, $additionalRate)

// ✅ WITH TYPE HINTS
function calculatePostage(float $weightKg, float $baseRate, float $additionalRate): float
```

**ACTION**: Add type hints to ALL function parameters and return types

### Functions with Side Effects
- [ ] **Line 858-867**: `dateFromat1()` - Echoes instead of returning (also spelling: "Fromat" → "Format")
- [ ] **Line 511-568**: `tokenDHLOnSaveSetting()` - Echoes debug output

**FIX**: Remove all direct output. Return data for calling code to handle.

---

## 📋 SPECIFIC FUNCTIONS TO REFACTOR

### Priority 1 - Fix These First (SQL Injection + Credentials)

| Function | Lines | Issue | Fix |
|----------|-------|-------|-----|
| `roleVerify()` | 54-65 | SQL injection | Use prepared statement |
| `activity()` | 46-51 | SQL injection | Use prepared statement |
| `getMailerBrevo()` | 21-34 | Hardcoded credentials | Use environment variables |
| `dhlCreateShipping()` | 592-785 | SQL injection + hardcoded data | Use prepared statement + env vars |
| `tokenDHLOnSaveSetting()` | 511-568 | SQL injection | Use prepared statement |

### Priority 2 - Fix These Second (Code Quality)

| Function | Lines | Issue | Fix |
|----------|-------|-------|-----|
| `totalSales()` | 786-823 | Echoes, closes conn | Return float value |
| `totalProduct()` | 819-830 | Echoes, closes conn | Return int count |
| `totalOrder()` | 832-843 | Echoes, closes conn | Return int count |
| `totalOrderReturn()` | 845-856 | Echoes, closes conn | Return int count |
| `dateFromat1()` | 858-867 | Echoes, spelling error | Return string, rename to `formatDate()` |
| `userData()` | 1306-1328 | SQL injection | Use prepared statement |
| `cartCount()` | 249-268 | Poor logic flow | Simplify |
| `cartList()` | 269-280 | Returns query object | Keep but ensure proper usage |

### Priority 3 - Refactor (Design Improvements)

| Function | Lines | Issue | Fix |
|----------|-------|-------|-----|
| `dhlCreateShipping()` | 592-785 | Too long, complex | Break into smaller functions |
| `validatePhoneNumber()` | 164-247 | Duplicate validation | Consolidate with `validatePhone()` |
| `getUsedCategory()` | 883-902 | Redundant | Could be generic function |
| `getUsedBrand()` | 903-922 | Redundant | Could be generic function |

---

## 🔧 IMPLEMENTATION STEPS

### Step 1: Setup Environment Variables (Week 1)
```bash
# Create .env file
cp .env.example .env

# Install dotenv
composer require vlucas/phpdotenv

# Update config/mainConfig.php to load .env
```

### Step 2: Implement Prepared Statements (Week 1-2)
- Start with most critical functions
- Use findAndReplace with regex
- Test thoroughly after each change
- Create database abstraction layer (optional)

### Step 3: Fix Return Types (Week 2)
- Remove all `echo` statements from functions
- Add type hints
- Return consistent data types

### Step 4: Remove Connection Closures (Week 2)
- Audit all `$conn->close()` calls
- Remove from helper functions
- Close only in main application lifecycle

### Step 5: Code Quality (Week 3)
- Rename functions (e.g., `dateFromat1()` → `formatDate()`)
- Consolidate duplicates
- Add error handling
- Add comments/documentation

---

## ✅ TESTING CHECKLIST

After implementing changes:

- [ ] Unit tests for database functions
- [ ] Security audit for SQL injection
- [ ] Test with invalid/malicious input
- [ ] Verify all functions return correct types
- [ ] Check application still runs without errors
- [ ] Review git commits for security issues

---

## 📚 RESOURCES

- [PHP Prepared Statements](https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php)
- [OWASP SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- [PHP Environment Variables Best Practices](https://12factor.net/config)
- [PHP Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
