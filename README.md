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
The reason for this method (in which the `index.php` file is copied into each directory in which it is to be used) is threefold. **First**, as there is only one file and it is relatively compact, it should be able to function indefinitely without updates. It simply displays the files wrapped in HTML so that they are viewed in the browser. No Content Management System consisting of thousands of files that are constantly being changed is needed. Joomla! is not needed for this. Drupal is not needed for this. WordPress is not needed for this. All of these frameworks are built to do jobs that are much, much more complex. This file simply displays media in a single web page. There are no headers, footers, sidebars and menus. This means it is much more difficult to break and should function indefinitely without updates.

**Second**, copying the file in this way into multiple directories mimics the action of a seed planted in a field. Obviously, the farmer does not expect to grow a crop from a single seed. If that seed failed, the entire farm would go down. By following the lead of the biological world and copying a single file into multiple locations, this means that any one seed (file) that failed for whatever reason would not affect the others. If this happened and one file became corrupt, then a file could be selected from another directory that had not been corrupted and used to overwrite the corrupted one.

**Three**, when beginning a project, there is rarely enough information to warrant a new website. As each day goes by, a few images may be found, a video made, or an audio clip. In order to share this information in the most expedient manner possible, and without making use of third party sites, this method can be used to accomplish that goal. All that is needed is a directory in one's `My Document` folder `Docs` folder, or similar, called `html`. Within that can be created the directory `/0` and `/1`. By carefully developing a directory structure _within_ these two folders, the `/0` directory can be used as a CDN (Content Delivery Network) ready directory. This will save time and cost later. The `/1` directory can be used to write articles in a text based format using a simple text editor and saving each file in the appropriate directory as `article.html`. 

Thus, if the _media_ directory (`/0`) mirrors the _article_ directory (`/1`), then supporting media can be loaded into the media directory and uploaded from there. As there is often little time to write an article for every media item created (image, audio or video), these media items can still be shared with others using a _direct link_ and then be easily viewable. Thus, by carefully setting things up in this manner, a much better workflow can be obtained, without the additional overhead of have to work through a complicated CMS from the beginning. _However_, this method _is still_ built to work _with_ the WordPress framework. It simply places it in its own directory, rather than at the root, as is common with this package by default. However, that is beyond the scope of this summary.

To make it work, it _does_ expect a stylesheet to be available at `/0/theme/css/style.css`. Obviously this location can be changed at the start, before is copied to each directory. However, once a decision is made, it is expected to remain consistent. After all, who buys a home on a foundation, and then calls up the contractor to move the foundation after the purchase has been made. By thinking carefully about a stable directory structure, the intent is to make the entire system that much more stable. This is the reason for putting the stylesheet in `/0/theme/css/style.css`. This mirrors the media directory shown above, which begins with: `/0/media/...`. The stylesheet is available in this download. This means there are two and only two active files.

Oh yes, this file can also function as a WordPress plugin. No changes need to be made. The `ec01-media-index` folder functions as a plugin folder and it can be installed via the normal method. However, in order for it to work, it needs to be told what media directory to access. This can be accomplished using the shortcode, with an argument, like so: `[media-index dir="/0/media/image/1"]`.

This solution was discovered as a part of an effort to build a model, a design for an integrated community. Even though this solution is very, very simple, the simplicity was discovered as a result of wading through much complexity. It came at the tail end of 20 months of development work. Due to the nature in which this solution was found and due to the focus and intent of these development efforts, a payment is requested upon decision to use, using a Try and Buy model, on the honour system.

**Payment can be made here: http://ec01.earth3300.local/how/stor/progr/softwa/php/ once a decision to use it has been made, on a per user basis.**

The long term goal is to create a sustainable community that can live indefinitely on the land that supports it. This community may be from 150 to 500 people in size, but is initially planned for 144 (6 properties of 4 people each times six clusters), on a property of a quarter section (2,640' x 2,640'). Your purchase of this product helps to support this endeavour. Thank you for your support.
