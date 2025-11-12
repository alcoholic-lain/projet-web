# Category Front-Office Feature

This document describes the category functionality implemented for the front-office.

## Overview

The category system allows users to browse and explore different content categories through an intuitive web interface.

## Features

- **Category Browsing**: View all available categories in grid or list format
- **Search**: Real-time search to filter categories by name or description
- **View Modes**: Toggle between grid and list views
- **Dark Mode**: Full support for dark/light theme
- **Responsive Design**: Works on all device sizes

## Available Categories

1. **Technology** - Tech news and discussions
2. **Sports** - Sports updates and events
3. **Music** - Music and entertainment
4. **Gaming** - Video games and esports
5. **Science** - Scientific discoveries
6. **Travel** - Travel experiences and tips
7. **Food** - Recipes and restaurants
8. **Education** - Learning and courses

## File Structure

```
model/
  └── Category.php           # Category model class

controller/components/
  └── category.php          # Category API endpoint

veiw/Client/src/
  ├── index.html            # Main page (updated with Categories link)
  └── categories.html       # Categories browsing page
```

## API Usage

### List All Categories

**Endpoint**: `GET /controller/components/category.php?action=list`

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Technology",
      "description": "Tech news and discussions",
      "icon": "fa-laptop",
      "color": "#3b82f6"
    },
    ...
  ]
}
```

### Get Single Category

**Endpoint**: `GET /controller/components/category.php?action=get&id={id}`

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Technology",
    "description": "Tech news and discussions",
    "icon": "fa-laptop",
    "color": "#3b82f6"
  }
}
```

## How to Access

1. Navigate to the main page at `veiw/Client/src/index.html`
2. Click on "Categories" in the navigation menu
3. Browse, search, and explore categories

## Technical Details

- **Backend**: PHP 8.3+
- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript
- **Icons**: Font Awesome 6.5.0
- **Fonts**: Inter (Google Fonts)

## Security

- Input validation on all user inputs
- Proper JSON encoding
- No SQL injection vulnerabilities
- XSS protection through proper encoding
