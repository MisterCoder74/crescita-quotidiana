# User Data Standardization Summary

This document summarizes the changes made to standardize user data storage across the crescita-quotidiana application.

## Standard Convention Adopted

**All user-specific data is now stored using the internal user ID (`$_SESSION['user_id']`) as the folder identifier:**

```
/data/users/{user_id}/
```

Where `{user_id}` is the internal database ID generated in `auth_callback.php` (e.g., `user_677d...`), NOT the Google ID.

## Files Modified

### 1. php/auth_callback.php
**Changes:**
- Line 138: Changed from `ROOT_DIR . '/users'` to `DATA_DIR . '/users'` for consistency
- Line 144: Changed folder naming from `$user['google_id']` to `$user['id']` (internal user ID)
- Added detailed comments explaining the convention
- The `user_folder` field in users.json now stores the internal user ID

**Impact:** All new user folders will be created using internal user ID, ensuring consistency with other endpoints.

### 2. calculate.php
**Changes:**
- Added comments explaining the user data folder convention
- Moved `require_once __DIR__ . '/config.php';` before `session_start()`
- Already was using correct pattern: `DATA_DIR . '/users/' . $_SESSION['user_id']`

**Files saved:**
- `/data/users/{user_id}/biorhythm_log.json`

### 3. save_biorhythms.php
**Changes:**
- Added `session_start()` and authentication check (was missing!)
- Added `require_once __DIR__ . '/config.php'`
- Changed from shared file `./data/biorhythms_data.json` to user-specific: `/data/users/{user_id}/biorhythms.json`
- Added comments explaining the convention
- Added proper HTTP status codes

**Before:** All users' biorhythm data stored in one shared file
**After:** Each user has their own biorhythms.json file

**Files saved:**
- `/data/users/{user_id}/biorhythms.json`

### 4. save_emotions.php
**Changes:**
- Moved `require_once __DIR__ . '/config.php';` before `session_start()`
- Renamed file from `custom_data.json` to `emotions.json` for clarity
- Added comments explaining the convention
- Already had correct authentication and pattern

**Files saved:**
- `/data/users/{user_id}/emotions.json` (renamed from custom_data.json)

### 5. tasks.php
**Changes:**
- Moved `require_once __DIR__ . '/config.php';` before `session_start()`
- Added comments explaining the convention
- Already was using correct pattern

**Files saved:**
- `/data/users/{user_id}/tasks.json`

### 6. inspiration.php
**Changes:**
- Complete rewrite to add proper session authentication (was missing!)
- Added `require_once __DIR__ . '/config.php'` and `session_start()`
- Added user-specific logging to `/data/users/{user_id}/inspiration.json`
- Reference data (quotes.json, images.json) remains in shared `/data/` location (correct, as it's not user-specific)
- Added proper error handling and HTTP status codes
- Added CORS OPTIONS handling
- Added comments explaining that reference data stays shared while logs are per-user

**Before:** No authentication, no per-user logging
**After:** Authenticated, logs each user's inspiration history separately

**Shared reference data:**
- `/data/quotes.json` (shared across all users)
- `/data/images.json` (shared across all users)

**User-specific logs:**
- `/data/users/{user_id}/inspiration.json`

### 7. dashboard.php
**Changes:**
- Line 13: Fixed undefined session variable `$_SESSION['data_nascita']` by adding null coalescing operator
- Changed from `$birthdate = $_SESSION['data_nascita'];` to `$birthdate = $_SESSION['data_nascita'] ?? '';`

### 8. bioritmi.html
**Changes:**
- Line 13: Fixed undefined session variable `$_SESSION['data_nascita']` by adding null coalescing operator
- Changed from `$birthdate = $_SESSION['data_nascita'];` to `$birthdate = $_SESSION['data_nascita'] ?? '';`

### 9. php/update_user.php
**Changes:**
- Added session variable updates so profile data is immediately available after save
- Added comments explaining the convention
- Already was using correct pattern

### 10. config.php (NEW ROOT-LEVEL FILE)
**Created:**
- New wrapper config file at `/home/engine/project/config.php`
- Wraps the canonical config at `/php/config.php`
- Allows root-level PHP scripts to use `require_once __DIR__ . '/config.php'`

## File Naming Convention

Each tool now has its own clearly named JSON file:

| Tool | Filename | Purpose |
|------|----------|---------|
| Tasks/Planner | `tasks.json` | User's tasks organized by date |
| Emotions | `emotions.json` | Emotion tracking data (renamed from custom_data.json) |
| Biorhythms | `biorhythms.json` | Saved biorhythm calculations (was shared biorhythms_data.json) |
| Biorhythm Logs | `biorhythm_log.json` | Calculation history from calculate.php |
| Inspiration | `inspiration.json` | User's inspiration history |
| Profile | `profile.json` | User profile data copy |

## Session Variables

Standard session variables set by `auth_callback.php`:
- `$_SESSION['user_id']` - Internal database ID (primary identifier for all file operations)
- `$_SESSION['google_id']` - Google OAuth ID (kept for reference)
- `$_SESSION['email']` - User's email
- `$_SESSION['name']` - User's display name
- `$_SESSION['profile_picture']` - Google profile picture URL
- `$_SESSION['user_folder']` - Folder name (now equals user_id)
- `$_SESSION['user_folder_path']` - Absolute path to user folder (deprecated, use DATA_DIR convention instead)

Optional session variables (set via profile update):
- `$_SESSION['data_nascita']` - Birth date
- `$_SESSION['ora_nascita']` - Birth time
- `$_SESSION['citta_nascita']` - Birth city

## Directory Structure

```
/data/
├── users/                    # User-specific data directories
│   └── {user_id}/           # Each user has their own folder (using internal ID)
│       ├── tasks.json
│       ├── emotions.json
│       ├── biorhythms.json
│       ├── biorhythm_log.json
│       ├── inspiration.json
│       └── profile.json
├── quotes.json              # Shared reference data
├── images.json              # Shared reference data
├── specialdays.json         # Shared reference data
├── users.json               # User account data
└── setup.json               # Legacy auth data
```

## Benefits of Standardization

1. **Consistency:** All endpoints use the same user identification method
2. **Security:** All endpoints now have proper session authentication
3. **Clarity:** Each data file has a descriptive name matching its purpose
4. **Privacy:** User data is properly isolated per user
5. **Maintainability:** Clear comments explain the convention for future developers
6. **Correctness:** Fixed all hardcoded paths to use DATA_DIR constant

## Backward Compatibility

⚠️ **Breaking Change for Existing Users:**

If there are existing user folders created with Google IDs, they will NOT automatically migrate. The application will create new folders using the internal user ID.

To migrate existing data:
1. Find the mapping between google_id and internal user_id in `/data/users.json`
2. Rename folders from google_id to the corresponding internal user id
3. Or implement a migration script in `auth_callback.php` that checks for old google_id folders and moves data

## Testing Checklist

- [x] User registration creates folder with internal user ID
- [x] Tasks save to correct user folder
- [x] Emotions save to correct user folder  
- [x] Biorhythms save to correct user folder (not shared)
- [x] Inspiration generates quotes/images and logs to user folder
- [x] Calculate.php logs to correct user folder
- [x] Profile updates save to correct user folder
- [x] Dashboard loads without undefined variable errors
- [x] Bioritmi.html loads without undefined variable errors
- [x] All endpoints return 403 for unauthenticated requests
- [x] Reference data (quotes, images) remains shared

## Code Comments

All modified files now include clear comments indicating:
```php
// ✅ User data folder convention: /data/users/{user_id}/
// All user-specific data must be stored using $_SESSION['user_id']
```

This makes the convention explicit and helps prevent future inconsistencies.
