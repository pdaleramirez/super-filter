# Super Filter plugin for Craft CMS 3.x  
  
Build your search page with search filters from your element fields and filter element entries by categories, tags, element relations and other fields with Vue.js support. Easily setup your search page by using twig variable functions, back-end coding not needed.
  
![Screenshot](resources/img/super-filter-quick-demo.gif)  
  
## Requirements  
  
This plugin requires Craft CMS 3.2.0 or later.  
  
## Installation  
  
To install the plugin, follow these instructions.  
  
 1. Open your terminal and go to your Craft project:  
  
        cd /path/to/project  
  
 2. Then tell Composer to load the plugin:  
  
        composer require pdaleramirez/super-filter  
  
 3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Search Filter.  
 4. Go to Super Filter -> Setting and click **Install Example Data** button  to generate example entries (Optional).
  
## Configuring Search Filter  
1. Create new setup entry in Setup Search tab and click new setup
2. Setup Config:
- Title - to easily identify your setup
- Handle - needed to initialize search setup.
- Items per page - number of entries on each content loading or pagination.
- Element - choose an element display
- Container - section, group or product type for an element.
- Sort Fields - drag fields to selected column to be displayed on sorting template for sorting elements.
- Initial Sort - the default sort query on page load.
- Search Fields - drag fields to selected column to be displayed on search field template for filtering elements.
- Template Folder - the folder of the templates that you'll modify to get element attributes or modify html's.

  
## Using Super Filter  
  
-Insert text here-  
  

Brought to you by [Dale Ramirez](https://github.com/pdaleramirez)
