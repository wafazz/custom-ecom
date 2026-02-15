# Code Refactoring Guide - function.php

## Critical Issues Found

### 1. SQL Injection Vulnerabilities ❌
**Problem:** Direct string interpolation in SQL queries

```php
// ❌ VULNERABLE
$sql = "SELECT * FROM categories WHERE id='$id'";
$query = $conn->query($sql);
```

**Solution:** Use prepared statements

```php
// ✅ SECURE
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
```

### 2. Exposed Credentials ❌
**Affected Lines:**
- Line 26: `'889d41001@smtp-brevo.com'`
- Line 27: `'xsmtpsib-XXXXXXXXXXXXXXXXXXXX'`
- Lines 636-641: DHL hardcoded addresses, phone, email

**Solution:** Move to `.env` file using environment variables

```php
// ✅ SECURE
$mail->Username = getenv('BREVO_SMTP_EMAIL');
$mail->Password = getenv('BREVO_SMTP_KEY');
```

### 3. Inconsistent Return Types ❌
**Problem:** Some functions return arrays, some return query objects, some echo directly

```php
// ❌ INCONSISTENT
function cartCount() { return array("count" => $theCount); }
function cartList() { return $query; }
function totalSales() { echo $totalMYR; }
```

**Solution:** Use consistent patterns

```php
// ✅ CONSISTENT - Always return data
function getCartCount(): array {
    return ["count" => $this->getCount()];
}

function getCartList(): mysqli_result {
    return $this->query("SELECT * FROM cart WHERE ...");
}

function getTotalSales(): float {
    return $result['total_myr'] ?? 0.0;
}
```

### 4. Database Connection Management ❌
**Problem:** Functions close connections prematurely

```php
// ❌ BAD - Closes connection in function
function totalProduct() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM products WHERE deleted_at IS NULL";
    $result = $conn->query($sql);
    echo $result->num_rows;
    $conn->close();  // ❌ Closes shared connection!
}
```

**Solution:** Don't close shared connections

```php
// ✅ GOOD - Let connection manager handle closure
function getTotalProducts(): int {
    $conn = getDbConnection();
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE deleted_at IS NULL");
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}
```

### 5. Missing Type Hints ❌
**Problem:** No parameter or return type declarations

```php
// ❌ NO TYPE HINTS
function getCategoryDetails($id) {
    // ...
}
```

**Solution:** Add type hints

```php
// ✅ WITH TYPE HINTS
function getCategoryDetails(int $id): ?array {
    // ...
}
```

## Refactoring Priority

### Phase 1: CRITICAL (Do First)
1. [ ] Move all hardcoded credentials to `.env` file
2. [ ] Convert all SQL queries to prepared statements
3. [ ] Stop closing database connections in functions
4. [ ] Remove all direct echo statements (return data instead)

### Phase 2: HIGH (Do Soon)
1. [ ] Add type hints to all functions
2. [ ] Consolidate duplicate functions
3. [ ] Create helper classes (DHL, Email, Database)
4. [ ] Add input validation

### Phase 3: MEDIUM (Do Later)
1. [ ] Add error handling/exceptions
2. [ ] Add logging
3. [ ] Add comments/documentation
4. [ ] Refactor into classes/services

## Specific Functions to Refactor

### Database Functions
- [ ] `activity()` - SQL injection, should use prepared statement
- [ ] `roleVerify()` - SQL injection, dangerous LIKE query
- [ ] `getCategoryDetails()` - SQL injection
- [ ] `getBrandDetails()` - SQL injection
- [ ] `stockBalanceIndividual()` - SQL injection
- [ ] ALL functions using direct SQL

### Mail Functions
- [ ] `getMailerBrevo()` - Expose credentials via `.env`

### DHL Functions
- [ ] `tokenDHLOnSaveSetting()` - SQL injection, hardcoded addresses
- [ ] `dhlToken()` - SQL injection
- [ ] `dhlCreateShipping()` - SQL injection, hardcoded data

### Utility Functions
- [ ] `totalSales()` - Should return value, not echo
- [ ] `totalProduct()` - Should return value, not echo
- [ ] `totalOrder()` - Should return value, not echo
- [ ] `totalOrderReturn()` - Should return value, not echo
- [ ] `dateFromat1()` - Should return value, not echo (also typo in name)

## Example Refactored Functions

See attached refactored snippets for pattern implementation.
