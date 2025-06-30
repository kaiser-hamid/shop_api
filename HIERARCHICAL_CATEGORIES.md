# Hierarchical Categories for Algolia Search

This implementation adds hierarchical category support to your Laravel Scout + Algolia search setup.

## Overview

The hierarchical categories are structured as follows:

```json
{
  "hierarchicalCategories": {
    "lvl0": ["Health & Wellness", "Free Delivery"],
    "lvl1": ["Health & Wellness > Supplements"],
    "lvl2": ["Health & Wellness > Supplements > Vitamins"]
  }
}
```

## Implementation Details

### 1. Product Model Updates

The `Product` model now includes:

- `buildHierarchicalCategories()` method that creates the hierarchical structure
- `getCategoryHierarchyPath()` method that builds the full category path
- Updated `toSearchableArray()` method to include hierarchical categories

### 2. Algolia Configuration

Updated `config/scout.php` with:

```php
'index-settings' => [
    'products' => [
        'searchableAttributes' => [
            'name',
            'brand', 
            'categories',
            'description'
        ],
        'attributesForFaceting' => [
            'brand',
            'categories',
            'hierarchicalCategories.lvl0',
            'hierarchicalCategories.lvl1', 
            'hierarchicalCategories.lvl2',
            'status',
            'price',
            'rating'
        ],
        'hierarchicalFacets' => [
            'hierarchicalCategories'
        ]
    ],
],
```

## Usage

### 1. Reindex Products

Run the command to reindex all products with hierarchical categories:

```bash
php artisan products:reindex-hierarchical-categories
```

### 2. Frontend Implementation

In your frontend Algolia search, you can now use:

```javascript
// Initialize search with hierarchical facets
const search = instantsearch({
  appId: 'YOUR_APP_ID',
  apiKey: 'YOUR_SEARCH_API_KEY',
  indexName: 'products',
  searchParameters: {
    hierarchicalFacets: [{
      name: 'hierarchicalCategories',
      attributes: [
        'hierarchicalCategories.lvl0',
        'hierarchicalCategories.lvl1',
        'hierarchicalCategories.lvl2'
      ]
    }]
  }
});

// Add hierarchical menu widget
search.addWidget(
  instantsearch.widgets.hierarchicalMenu({
    container: '#hierarchical-categories',
    attributes: [
      'hierarchicalCategories.lvl0',
      'hierarchicalCategories.lvl1',
      'hierarchicalCategories.lvl2'
    ],
    templates: {
      header: 'Categories'
    }
  })
);
```

### 3. API Search Example

```php
use App\Models\Product;

// Search with hierarchical filters
$products = Product::search('vitamins')
    ->within('hierarchicalCategories.lvl0', 'Health & Wellness')
    ->within('hierarchicalCategories.lvl1', 'Health & Wellness > Supplements')
    ->get();
```

## Category Structure Support

The implementation supports:

- **1-level categories**: Only `lvl0` will be populated
- **2-level categories**: `lvl0` and `lvl1` will be populated  
- **3-level categories**: All levels (`lvl0`, `lvl1`, `lvl2`) will be populated

## Automatic Updates

When a product's categories are updated, the hierarchical categories will automatically be regenerated and reindexed in Algolia.

## Testing

To test the implementation:

1. Create categories with parent-child relationships
2. Attach categories to products
3. Run the reindex command
4. Verify the hierarchical structure in Algolia dashboard

## Notes

- Categories are loaded with their parent relationships for proper hierarchy building
- Duplicate values are automatically removed from each level
- The implementation handles missing parent relationships gracefully
- All existing search functionality remains unchanged 