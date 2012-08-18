PHP Rich Content Embedder
====================================

This plugin will embed the rich content from any 3rd party site into your website. This will scrape the content from the provided URL and fetch all the below listed fields. 

This is similar to Facebook wall URL posting feature, where user entered URL will be used to fetch Rich Content from the site and embed into Facebook wall.


Dynamic Rich Content
--------------------------------
 1. Page Title
 2. Description
 3. Keywords
 4. Site specific Images
 5. Video URL

 
Usage
-----------
 1. Deploy the **urlparsing** folder to your apache htdocs folder(your document root)
 2. Load index.html in browser. Ex: **http://[servername]/urlparsing**
 3. You can enter your own URL in the textbox or click on the sample link provided.
 4. Rich Content will be parsed in the parse.php and JSON object will be returned.
 5. Returned JSON object will be used for showing the content in UI.

Various HTML elements used to render Rich Content
----------
**Page Title**

 1. Title Tag
 2. Open Graph Meta tag (og:title)

**Description**

 1. HTML Meta Tag
 2. Open Graph Meta tag (og:description)
 3. HTML5 Microdata (itemprop)

**Site Specific Images**

 1. Open Graph Meta tag (og:image)
 2. HTML Link tag
 3. All images in the page, which will be filtered per custom logic

**Video URL**

 1. Open Graph Meta tag (og:video)
 2. Twitter Meta tag (twitter:player)

**Keywords**

 1. HTML Meta tag

Issues
=======
Report issues/features on github, please.