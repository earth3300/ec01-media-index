# EC01 Media Index
Allows media (images, video and audio) to be viewed in a directory through an index file.

## Installation

1. Copy the `index.php` file to each directory that contains media files.
2. Created to be used in the following manner:

```
0/
  - media/
    - topic-1/
       - sub-topic-1/
          - index.php
          - image-1.jpg
          - audio-1.mp3
          - video-1.mp4
       - sub-topic-2/
          - index.php
          - image-2.jpg
          - image-3.jpg
          - image-4.jpg
          ...
       - sub-topic-3 /
    - topic-2
       - sub-topic-1/
          - index.php
          - image-1.jpg
          - audio-2.mp3
          - audio-3.mp3
       - sub-topic-2/
          ...
       - sub-topic-3 /
          ...
    - topic-3
       - sub-topic-1/
         - index.php
         - video-2.mp4
         - video-3.mp4
         - video-4.mp4
       - sub-topic-2/
         ...
       - sub-topic-3 /
         ...
     ...
```
The reason for this method (in which the `index.php` file is copied into each directory in which it is to be used) is two fold. First, as there is only one file and it is relatively compact, it should be able to function indefinitely without updates. It simply displays the files wrapped in HTML so that they are viewed in the browser. No Content Management System consisting of thousands of files that are constantly being changed is needed. Joomla! is not needed for this. Drupal is not needed for this. WordPress is not needed for this. All of these frameworks are built to do jobs that are much, much more complex. This file simply displays media in a single web page. There are no headers, footers, sidebars and menus. This means it is much more difficult to break and should function indefinitely without updates.

To make it work, it _does_ expect a stylesheet to be available at `/0/theme/css/style.css`. Obviously this location be changed at the start, before is copied to each directory. However, once a decision is made, it is expected to remain consistent. After all, who buys a home on a foundation, and then calls up the contractor to move the foundation after the purchase has been made. By thinking carefully about a stable directory structure, the intent is to make the entire system that much more stable. This is the reason for putting the stylesheet in `/0/theme/css/style.css`. This mirrors the media directory shown above, which begins with: `/0/media/...`. The stylesheet is available in this download. This means there are two and only two active files.

Oh yes, with a snippet of code, this file can also function as a WordPress plugin. No changes need to be made. The `ec01-media-index` folder functions as a plugin folder and it can be installed via the normal method.

Thus, with so many words is something that can be demonstrated better than it can be explained. Due to the intensive nature in which this solution showed itself, a payment is requested for unlimited use, on a per user basis. Please see: http://ec01.earth3300.info/how/stor/softw/php/ to make a payment, once a decision to use it has been made. For this price, there is no support, however, you may use it indefinitely and on as many sites as you own. Although the file may be freely shared with others, any other new user is request to make payment, to help support this endeavour and the incredible amount of work that has gone into this discovery of this simple solution... as part of a larger integrated solution.

The long term goal is to create a sustainable community that can live indefinitely on the land that supports it. This community may be from 150 to 500 people in size, but is initially planned for 144 (6 properties of 4 people each times six clusters), on a property of a quarter section (2,640' x 2,640'). Your purchase of this product helps to support this endeavour. Thank you for your support.
