# CORS Configuration Fix

## Problem
```
CORS policy blocked: No 'Access-Control-Allow-Origin' header in response
```

## Solution Applied

### 1. Updated Laravel CORS Config
**File**: `config/cors.php`

Added frontend ports to allowed origins:
```php
'allowed_origins' => [
    'http://localhost:5173',
    'http://localhost:5174',
    'http://127.0.0.1:5173',
    'http://127.0.0.1:5174',
],
```

### 2. Created Missing routes/web.php
Laravel requires this file even for API-only applications.

### 3. Cleared Config Cache
```bash
php artisan config:clear
```

## Restart Backend

**Important**: You MUST stop and restart the Laravel development server for changes to take effect.

```bash
# Stop current server (Ctrl+C)

# Then restart:
php artisan serve
```

## Testing CORS

After restarting Laravel, test the login endpoint:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Origin: http://localhost:5174" \
  -d '{"email":"test@example.com","password":"password"}'
```

Should see `Access-Control-Allow-Origin: http://localhost:5174` in response headers.

## Key Points

1. **Frontend runs on**: http://localhost:5174 (sometimes 5173 if port in use)
2. **Backend runs on**: http://localhost:8000
3. **CORS headers** must be present for cross-origin requests
4. **Config changes** don't take effect until server restart
5. **Sanctum credential** support is enabled (`supports_credentials: true`)

## Common CORS Headers

When properly configured, Laravel responds with:
```
Access-Control-Allow-Origin: http://localhost:5174
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
Access-Control-Allow-Credentials: true
```

## If Issue Persists

1. **Check backend is running**: 
   ```
   http://localhost:8000/api/login should return 422 error (missing email/password), not CORS error
   ```

2. **Verify config changes were applied**:
   ```bash
   php artisan config:show cors
   ```

3. **Check laravel.log for errors**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Ensure Middleware is loaded** in `bootstrap/app.php`:
   ```php
   ->withMiddleware(function (Middleware $middleware) {
       $middleware->api(append: [
           // Laravel Sanctum middleware
       ]);
   })
   ```

## CORS with Sanctum

When using Laravel Sanctum (as in this project):
- Tokens are JWT-based
- Credentials are NOT sent via cookies by default
- Bearer token is included in `Authorization` header
- CORS config must allow `Authorization` header (✓ already configured with `'allowed_headers' => ['*']`)

## After Fixes

1. Restart Laravel backend
2. Refresh React frontend (Ctrl+Shift+R to hard refresh)
3. Try logging in again
4. Check Network tab in DevTools (F12) to verify:
   - Request shows `Authorization: Bearer {token}` 
   - Response shows `Access-Control-Allow-*` headers
   - Status code is 200/422 (not CORS error)
