#!/bin/bash

echo "=========================================="
echo "Innovation DB - Installation Test Script"
echo "=========================================="
echo ""

# Check PHP
echo "1. Checking PHP..."
if command -v php &> /dev/null; then
    php_version=$(php -v | head -n 1)
    echo "   ✅ PHP is installed: $php_version"
else
    echo "   ❌ PHP is not installed"
    exit 1
fi

# Check PHP PDO extension
echo ""
echo "2. Checking PHP PDO extension..."
if php -m | grep -q PDO; then
    echo "   ✅ PDO extension is installed"
else
    echo "   ❌ PDO extension is not installed"
    exit 1
fi

# Check PHP PDO MySQL driver
echo ""
echo "3. Checking PHP PDO MySQL driver..."
if php -m | grep -q pdo_mysql; then
    echo "   ✅ PDO MySQL driver is installed"
else
    echo "   ❌ PDO MySQL driver is not installed"
    exit 1
fi

# Check MySQL
echo ""
echo "4. Checking MySQL..."
if command -v mysql &> /dev/null; then
    mysql_version=$(mysql --version)
    echo "   ✅ MySQL is installed: $mysql_version"
else
    echo "   ❌ MySQL is not installed"
    exit 1
fi

# Check PHP syntax
echo ""
echo "5. Checking PHP file syntax..."
php -l model/config/data-base.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ data-base.php: No syntax errors"
else
    echo "   ❌ data-base.php: Syntax error detected"
    exit 1
fi

php -l model/Innovation/Category.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ Category.php: No syntax errors"
else
    echo "   ❌ Category.php: Syntax error detected"
    exit 1
fi

php -l controller/components/CategoryController.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ CategoryController.php: No syntax errors"
else
    echo "   ❌ CategoryController.php: Syntax error detected"
    exit 1
fi

# Check JavaScript syntax
echo ""
echo "6. Checking JavaScript syntax..."
if command -v node &> /dev/null; then
    node -c veiw/Client/assets/js/category.js > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "   ✅ category.js: No syntax errors"
    else
        echo "   ❌ category.js: Syntax error detected"
        exit 1
    fi
else
    echo "   ⚠️  Node.js not installed, skipping JavaScript check"
fi

echo ""
echo "=========================================="
echo "✅ All checks passed!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Start your MySQL server"
echo "2. Run: mysql -u root -p < model/Innovation/schema.sql"
echo "3. Configure database credentials in model/config/data-base.php"
echo "4. Start your web server (Apache/Nginx/PHP built-in)"
echo "5. Access the application at:"
echo "   - FrontOffice: http://localhost/projet-web/veiw/Client/src/list_Innovation.html"
echo "   - BackOffice: http://localhost/projet-web/veiw/Admin/index.html"
echo ""
