# Testing Guide - BackOffice Category CRUD

## Overview
This guide provides step-by-step instructions to test the BackOffice Category CRUD functionality.

## Prerequisites
✅ PHP 7.4+ with PDO MySQL extension
✅ MySQL 5.7+ database server
✅ Web server (Apache/Nginx) or PHP built-in server
✅ All syntax checks passed (run `./test_installation.sh`)

## Setup Instructions

### 1. Database Setup
```bash
# Start MySQL service
sudo service mysql start

# Create database and tables
mysql -u root -p < model/Innovation/schema.sql

# Verify database creation
mysql -u root -p -e "USE innovation_db; SHOW TABLES;"
```

Expected output:
```
+-------------------------+
| Tables_in_innovation_db |
+-------------------------+
| categories              |
| commentaires            |
| innovations             |
| pieces_jointes          |
| votes                   |
+-------------------------+
```

### 2. Start Web Server

**Option A: PHP Built-in Server**
```bash
cd /path/to/projet-web
php -S localhost:8000
```

**Option B: Apache/Nginx**
- Place project in web root (htdocs, www, etc.)
- Ensure mod_rewrite is enabled (Apache)
- Configure virtual host if needed

### 3. Access the Application

**BackOffice Dashboard:**
```
http://localhost:8000/veiw/Admin/index.html
```

## Test Cases

### Test Case 1: List Categories (READ)
**Goal:** Verify that the category list page displays correctly

**Steps:**
1. Open: `http://localhost:8000/veiw/Admin/a_Category.html`
2. Verify the page loads without errors
3. Check that the table shows 5 sample categories:
   - Exploration Spatiale
   - Énergie Orbitale
   - Habitats Lunaires
   - Robotique Spatiale
   - Propulsion Avancée

**Expected Result:**
- ✅ Page displays with proper styling
- ✅ Navigation links are functional
- ✅ Table shows all categories with ID, Name, Description, Date
- ✅ Action buttons (Edit, Delete) are visible

**API Test (Optional):**
```bash
curl http://localhost:8000/controller/components/CategoryController.php
```

---

### Test Case 2: Create Category (CREATE)
**Goal:** Add a new category through the BackOffice

**Steps:**
1. From category list, click "➕ Ajouter une catégorie"
2. Fill in the form:
   - **Nom:** "Satellites de Communication"
   - **Description:** "Technologies et systèmes de communication spatiale"
3. Click "Créer la catégorie"
4. Verify success message appears
5. Verify redirect to category list
6. Confirm new category appears in the table

**Expected Result:**
- ✅ Form validation works (required fields)
- ✅ Success message: "✅ Catégorie créée avec succès !"
- ✅ Automatic redirect after 1.5 seconds
- ✅ New category visible in list with correct data

**API Test (Optional):**
```bash
curl -X POST http://localhost:8000/controller/components/CategoryController.php \
  -H "Content-Type: application/json" \
  -d '{"nom":"Test Category","description":"Test Description"}'
```

---

### Test Case 3: Update Category (UPDATE)
**Goal:** Modify an existing category

**Steps:**
1. From category list, click "✏️ Modifier" on any category
2. Verify the form is pre-filled with current data
3. Modify the fields:
   - **Nom:** "Exploration Spatiale Avancée"
   - **Description:** "Missions d'exploration au-delà du système solaire"
4. Click "Mettre à jour"
5. Verify success message
6. Return to list and confirm changes

**Expected Result:**
- ✅ Edit page loads with correct data
- ✅ Form validation works
- ✅ Success message: "✅ Catégorie mise à jour avec succès !"
- ✅ Changes reflected in the list
- ✅ Other fields remain unchanged

**API Test (Optional):**
```bash
curl -X PUT http://localhost:8000/controller/components/CategoryController.php \
  -H "Content-Type: application/json" \
  -d '{"id":1,"nom":"Updated Name","description":"Updated Description"}'
```

---

### Test Case 4: Delete Category (DELETE)
**Goal:** Remove a category from the system

**Steps:**
1. From category list, click "🗑️ Supprimer" on a test category
2. Confirm the deletion in the alert dialog
3. Verify success message
4. Confirm category is removed from the list

**Expected Result:**
- ✅ Confirmation dialog appears
- ✅ Success message: "✅ Catégorie supprimée avec succès !"
- ✅ Category immediately removed from table (no page reload)
- ✅ Database record deleted

**API Test (Optional):**
```bash
curl -X DELETE http://localhost:8000/controller/components/CategoryController.php \
  -H "Content-Type: application/json" \
  -d '{"id":6}'
```

---

### Test Case 5: Input Validation
**Goal:** Verify input validation and security

**Steps:**
1. Try to create a category with empty fields
2. Try to submit form with only name filled
3. Try to submit form with only description filled
4. Try to inject HTML/JavaScript in fields

**Expected Results:**
- ✅ Form prevents submission with empty required fields
- ✅ Browser shows "Please fill out this field" message
- ✅ HTML tags are escaped/sanitized (check database)
- ✅ No XSS vulnerability

**Security Test:**
```
Nom: <script>alert('XSS')</script>Test
Description: <img src=x onerror=alert('XSS')>

Expected in DB: &lt;script&gt;alert('XSS')&lt;/script&gt;Test
```

---

### Test Case 6: Navigation
**Goal:** Verify all navigation links work correctly

**Steps:**
1. From Dashboard (`index.html`), click "Catégories"
2. From Category list, click "⬅ Retour à la liste"
3. Test "Retour au Front Office" link
4. Test navigation between Innovations and Categories

**Expected Result:**
- ✅ All navigation links work
- ✅ No 404 errors
- ✅ Consistent header across all pages

---

## Browser Console Testing

Open browser DevTools (F12) and check:

1. **Network Tab:**
   - ✅ All API requests return 200 (success) or appropriate status
   - ✅ No 404 or 500 errors
   - ✅ JSON responses are well-formed

2. **Console Tab:**
   - ✅ No JavaScript errors
   - ✅ No CORS errors
   - ✅ No missing resource errors

## Database Verification

After testing CRUD operations, verify database state:

```sql
-- Check categories table
USE innovation_db;
SELECT * FROM categories ORDER BY date_creation DESC;

-- Count categories
SELECT COUNT(*) as total FROM categories;

-- Check for sanitized data
SELECT id, nom, description FROM categories WHERE nom LIKE '%<%';
```

## Known Limitations

- File uploads not implemented for categories (by design)
- No user authentication (admin pages are publicly accessible)
- No pagination (all records displayed at once)
- Soft delete not implemented (hard delete only)

## Troubleshooting

### Issue: "Connection error: ..."
**Solution:** Check MySQL service is running and credentials in `data-base.php`

### Issue: "Cannot fetch categories"
**Solution:** 
1. Check PHP error logs
2. Verify API endpoint path is correct
3. Check CORS headers if using different domains

### Issue: "404 Not Found on CategoryController.php"
**Solution:** Verify web server root path and controller file location

### Issue: JavaScript errors in console
**Solution:**
1. Clear browser cache
2. Check `category.js` file path in HTML
3. Verify JavaScript syntax with `node -c category.js`

## Success Criteria

All test cases should pass with:
- ✅ No PHP errors
- ✅ No JavaScript console errors
- ✅ All CRUD operations functional
- ✅ Proper input validation
- ✅ Data properly sanitized
- ✅ Consistent UI/UX
- ✅ Responsive design

## Additional Resources

- See `README.md` for installation instructions
- Run `./test_installation.sh` to verify environment
- Check `model/Innovation/schema.sql` for database structure
